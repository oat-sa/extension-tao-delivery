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

//include("config.php");

//soap library
include_once("nusoap.php");
/**
* implements server receiving informations about results
* For A Client Application
* @author patrick
* @access public
* @package taoplugins
*/

$server = new nusoap_server();
$server->debug_flag=true;


/*
*sends back the list of missing packets to the client
*/
function isFullyOk($IDresult,$numberElts)
{
	return isFullyOkResult($IDresult[0],$numberElts[0]);
}
/*
* deal with one packet received from the client, the resutl is identified with IDresult, $length gives the number of packets to be received for this result, $seq defines the order number for this apcket (starts with the 0th packet)
*/
function setResult($xml,$IDresult,$seq,$length)
{

$return = getFullXml($xml[0], $IDresult[0], $seq[0], $length[0]);


if (!($return)) {return "OK";die();} 
else {$listXmls = splitXML($return);}

foreach ($listXmls as $key=> $val)
{
$xml =array($val);

$today = date("F j, Y, g i a").time().rand(0,256);
//Hack, an old bug in the xml of item caused problems with some items created by MRE
$xml[0]=str_replace("http://mod1.tao.lu/middleware/MoniqueReichertItems.rdfhttp","http",$xml[0]);

$fp = fopen("./received/"."Result ".$today." ".$IDresult[0].".xml", "wb");
fwrite($fp,$xml[0]);
fclose($fp);
}

	return "OK";
	
}


$server->add_to_map("isFullyOk", array("string"), array("string"));
$server->add_to_map("setResult", array("string", "string","string","string"), array("string"));

 $server->service($HTTP_RAW_POST_DATA);


?>