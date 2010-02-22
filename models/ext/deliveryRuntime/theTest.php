<?php
 
session_start();

 
?>

<?php 
$subjectIp = $_SERVER['REMOTE_ADDR'];
$noresult=0;

if(isset($_GET['subject']) and $_GET['subject']!=''){
	$subjectUri=$_GET['subject'];
	$subjectLabel="previewer";
	if($_GET['subject']=="previewer"){
		$subjectUri .= time();
		$noresult=1;
	}
}
else{
	if(isset($_SESSION["subject"]["uri"]) and $_SESSION["subject"]["uri"]!=''){
		$subjectUri=$_SESSION["subject"]["uri"];
		$subjectLabel=$_SESSION["subject"]["label"];
	}else
	{
		die("no user uri defined in the session, please login again.");
	}
}
$subjectUri=urlencode($subjectUri);
$subjectLabel=urlencode($subjectLabel);

$wsdlUrl="http://".$_SERVER['HTTP_HOST']."/taoDelivery/views/deliveryServer/wsdlContract/tao_result_wsdl.php";
$wsdlUrl=urlencode($wsdlUrl);

$runtimeParameters="";
$runtimeParameters="
	TestXmlFile=Test.xml
	&subject=$subjectUri
	&label=$subjectLabel
	&comment=
	&wsdlurl=$wsdlUrl
	&taoIP=$subjectIp
	&fullscreen=0
	&noresult=$noresult";
	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test</title>
</head>
<body bgcolor="#ffffff">

<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="1000" height="720" id="testcommand" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="Test.swf?<? echo $runtimeParameters; ?>" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<param name="salign" value="lt" />
<param name="wmode" value="opaque" />
<embed src="Test.swf?<? echo $runtimeParameters; ?>" quality="high" bgcolor="#ffffff" width="1000" height="720" name="testcommand" wmode="opaque" align="middle" swLiveConnect="true" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>

</body>
</html>


