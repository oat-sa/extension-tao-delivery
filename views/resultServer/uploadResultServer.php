<?php
/*
called from tao_result_wsdl.php
*/
/**
 * Implements web service for upload of test results
 * @package plugins.uploadresultserver
 * @author Plichart Patrick <patrick.plichart@tudor.lu>
 * @version 1.1
 */

//split xml to an array containing all results
include("split.php");

//implements management of the packets got from the client, assemble packets to a complete xml file
include("cacheResults.php");

//include import result data into results ontologies
// require_once($_SERVER['DOCUMENT_ROOT'] . "/taoResults/models/ext/utrv1/classes/importLogToGenerisResult.php");

//soap library
include_once("nusoap.php");

include_once(dirname(__FILE__).'/../../../generis/common/config.php');

/**
 * implements server receiving informations about results
 * For A Client Application
 * @author patrick
 * @access public
 * @package taoplugins
 */

$server = new nusoap_server();
$server->debug_flag=true;

error_reporting(0);

/*
*sends back the list of missing packets to the client
*/
function isFullyOk($IDresult,$numberElts) {
    return isFullyOkResult($IDresult[0],$numberElts[0]);
}

/*
* deal with one packet received from the client, the resutl is identified with IDresult, $length gives the number of packets to be received for this result, $seq defines the order number for this apcket (starts with the 0th packet)
*/
function setResult($xml,$IDresult,$seq,$length) {

    $return = getFullXml($xml[0], $IDresult[0], $seq[0], $length[0]);


    if (!($return)) {
        return "OK";
        die();
    }
    else {
        $listXmls = splitXML($return);
    }

    $xmlString="";
    foreach ($listXmls as $key=> $val) {
        $xml =array($val);
        $today = date("F j, Y, g i a").time().rand(0,256);

        $xmlFile="Result ".$today." ".$IDresult[0].".xml";
        //Hack, an old bug in the xml of item caused problems with some items created by MRE
        $xml[0]=str_replace("http://mod1.tao.lu/middleware/MoniqueReichertItems.rdfhttp","http",$xml[0]);

        $fp = fopen("./received/$xmlFile", "wb");
        fwrite($fp,$xml[0]);
        fclose($fp);

        $xmlString.=$xml[0];
    }

//send the link to the document to the taoResults extension
    $xmlPath=dirname(__FILE__)."/received/$xmlFile";
    $location= ROOT_URL . "/taoResults/models/ext/utrv1/classes/class.ImportLogToGenerisResult.php";//?resultxml=".urlencode($xmlPath);
    

    //send th XML itself , Added by Younes
    $url = $location;
    //send path of the file and the content too.
    $postFileds = array ();

    $postFileds['pathLogFile'] = urlencode($xmlPath);
    $postFileds["contentLogFile"] = urlencode($xmlString);
    $ch = curl_init();
    //set options
    curl_setopt($ch,CURLOPT_URL,$url);
    
	if(USE_HTTP_AUTH){
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, USE_HTTP_USER.":".USE_HTTP_PASS);
	}

    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$postFileds);//'pathLogFile='.urlencode($xmlPath));

    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // times out after 4s
    //close the cURL connexion

    $result = curl_exec($ch);


// Using the included scripts from the Result extension to import result data in Result Ontologies
// $resultDOM=new DomDocument();
// $resultDOM->loadXML($xmlString);
// $importResult=new importLog($resultDOM);

    return "OK";
}


$server->add_to_map("isFullyOk", array("string"), array("string"));
$server->add_to_map("setResult", array("string", "string","string","string"), array("string"));

$server->service($HTTP_RAW_POST_DATA);


?>