<?php
session_start();
require_once('config.php');

//get the current page
if(isset($_POST["page"])) $currentPage=intval($_POST["page"]);
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

//if a subject is loged in, get available tests with their properties(uri,label,comment):

//connect to the delivery service:
$deliveryService = new taoDelivery_models_classes_DeliveryService();
$allTestArray=$deliveryService->getTestClass()->getInstances(true);
		
$compiledTestArray=array();
foreach($allTestArray as $test){
	//check whether it is compiled or not, and select only the compiled one
	$isCompiled=$deliveryService->getTestStatus($test, "compiled");
	if($isCompiled){
		$compiledTestArray[]=$test;
	}
}
	
// $tests[]="8888";
// $tests[]="5645645645";
// $tests[]="33645645645";
// $tests[]="456907286945645";
// $tests[]="45645645645";
// $tests[]="45645668875";
// $tests[]="45645645645";
// $tests[]="45645645645";
// $tests[]="5645645645";
// $tests[]="33645645645";
// $tests[]="456907286945645";
// $tests[]="45645645645";
// $tests[]="45645668875";
// $tests[]="45645645645";

$total_number=count($compiledTestArray);
$end_number=min($total_number-1, $end_number);//re-adjust the end_number, in the case $end_number > $total_number (for the last page)
		
$totalPage=ceil($total_number/$tests_per_page);
$pager_data=array(
	"current"=>$currentPage, 
	"total"=>$totalPage,
	"start"=>$start_number,
	"end"=>$end_number
	);
		
$selectedTests_data=array();
for($i=$start_number; $i<=$end_number; $i++){
	//get the values of the properties of each instance: label, some parameter for 
	$selectedTests_data[$i]["uri"]=$compiledTestArray[$i]->uriResource;
	$selectedTests_data[$i]["label"]=$compiledTestArray[$i]->getLabel();
	$selectedTests_data[$i]["comment"]="comment of test no{$i+1}";
}

$result=array();
$result["tests"]=$selectedTests_data;
$result["pager"]=$pager_data;
$result["subject"]["uri"]=$_SESSION["subject"]["uri"];
$result["subject"]["label"]=$_SESSION["subject"]["label"];

echo json_encode($result);

?>