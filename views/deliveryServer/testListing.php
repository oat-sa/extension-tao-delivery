<?php
// session_start();
require_once('config.php');

//get the current page
if(isset($_POST["page"]) && intval($_POST["page"])>0 ) $currentPage=intval($_POST["page"]);
else $currentPage=1;

//define the number of tests per page
if (!isset($tests_per_page)) $tests_per_page=10;

//define the start and the end index of tests
$start_number = ($currentPage-1)*$tests_per_page;
$end_number = $start_number+$tests_per_page; 

//check whether an user is logged in
if(!isset($_SESSION["subject"]["uri"])){
	die("no user session defined, please login again");
}

//if a subject is logged in, get available tests with their properties(uri,label,comment):

//connect to the delivery service:
$deliveryService = new taoDelivery_models_classes_DeliveryService();
//get an arry of test uri
$allTests=$deliveryService->getTestsBySubject($_SESSION["subject"]["uri"]);
// var_dump($allTests);
		
$compiledTestArray=array();
foreach($allTests->getIterator() as $test){
	//check whether it is compiled or not, and select only the compiled one
	$isCompiled=$deliveryService->getTestStatus($test, "compiled");
	if($isCompiled){
		$compiledTestArray[]=$test;
	}
}
// var_dump($compiledTestArray);

//calculated the information for the paging	
$total_number=count($compiledTestArray);
$end_number=min($total_number-1, $end_number);//re-adjust the end_number, in the case $end_number > $total_number (for the last page)
$totalPage=ceil($total_number/$tests_per_page);
$pager_data=array(
	"current"=>$currentPage, 
	"total"=>$totalPage,
	"start"=>$start_number,
	"end"=>$end_number
	);

//fill an array with information from selected compiled tests (within the range defined by the paging information):	
$selectedTests_data=array();
for($i=$start_number; $i<=$end_number; $i++){
	// get the values of the properties of each instance: label, some parameter for 
	$selectedTests_data[$i]["uri"]=tao_helpers_Precompilator::getUniqueId($compiledTestArray[$i]->uriResource);
	$selectedTests_data[$i]["label"]=$compiledTestArray[$i]->getLabel();
	$selectedTests_data[$i]["comment"]="comment of test ";
}

/*
//test only
for($i=0; $i<=10; $i++){
	$selectedTests_data[$i]["uri"]=rand();
	$selectedTests_data[$i]["label"]=rand();
	$selectedTests_data[$i]["comment"]=rand();
}
$pager_data=array(
	"current"=>1, 
	"total"=>1,
	"start"=>0,
	"end"=>10
	);
*/	


$result=array();
$result["tests"]=$selectedTests_data;
$result["pager"]=$pager_data;
$result["subject"]["uri"]=$_SESSION["subject"]["uri"];
$result["subject"]["label"]=$_SESSION["subject"]["label"];

echo json_encode($result);

?>