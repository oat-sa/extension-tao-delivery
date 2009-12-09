<?php
//set_include_path("/");
//require_once('../../models/classes/class.DeliveryService.php');


$login=$_POST["usr"];
$password=$_POST["pwd"];

//test:
echo "login est $login et password est $password<br/>";

//connect to API here:

//login check here:
$_SESSION["subject"]=array();

//identify the subject and get unique uri of the subject
$subjectUri="";
$_SESSION["subject"]["uri"]=$subjectUri;

//available delivery listing (array of test information("uri","label","comment"))

$_SESSION["tests"]=array();
$_SESSION["tests"][]=array("uri"=>"http://127.0.0.1/middleware/demoItems.rdf#115018057017220","label"=>"test 1","");
$_SESSION["tests"][]=array("uri"=>"http://127.0.0.1/middleware/demoItems.rdf#11501546564574570","label"=>"test **%ç*4","");
$_SESSION["tests"][]=array("uri"=>"http://127.0.0.1/middleware/demoItems.rdf#888","label"=>"le bon test","");

$links=array();
//generate link to the test:
foreach($_SESSION["tests"] as $test){
	$links[]=substr($test["uri"],stripos($test["uri"],".rdf#")+5);
	
	//typically:
	// $links='<a href="../../compiled/888/start.html" target="_blank">Diverse item types, multiple language</a><br/>';
	//$links.='<a href="../../compiled/'.$test["uri"].'/start.html" target="_blank">'.$test["label"].'</a><br/>';
}

echo $links;
?>