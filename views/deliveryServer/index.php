<?php 

//session_start();//already started in common.php

if(isset($_POST["login"]) && ($_POST["login"]!="") && isset($_POST["password"]) && ($_POST["password"]!="")){
	session_start();

	require_once('../../includes/common.php');
	require_once('../../includes/constants.php');
	require_once('../../includes/config.php');
	require_once('../../models/classes/class.DeliveryService.php');

	$login=$_POST["login"];
	$password=$_POST["password"];

	//connect to API here:
	// $deliveryService = new taoDelivery_models_classes_DeliveryService();

	//login check here:
	$ok=true;
	
	if($ok){
		$_SESSION["subject"]=array();

		//identify the subject and get unique uri of the subject
		$subjectUri="uri";
		$_SESSION["subject"]["uri"]=$subjectUri;
		
		header("location: testIndex.php");	
	}else{
		session_unset();
		$errorLogin = "wrong login or/and password, please try agiain";
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="title" content="TAO platform">
  <meta name="author" content="Administrator">
  <meta name="description" content="TAO, Testing assist&eacute; par ordinateur, computer based testing, evaluation, assessment, CBT, CAT, elearning, competencies, comp&eacute;tences">
  <meta name="keywords" content="TAO, Testing assist&eacute; par ordinateur, computer based testing, evaluation, assessment, CBT, CAT, elearning, competencies, comp&eacute;tences">
  <meta name="robots" content="index, follow">
  <title>TAO - An Open and Versatile Computer-Based Assessment Platform</title>

	<style type="text/css">
		body {background: #CDCDCD;color: #022E5F; font-family: verdana, arial, sans-serif;}
		td {font-size: 14px;}
		a {text-decoration: none;font-weight: bold; border: none; color: #BA122B;}
		a:hover {text-decoration: underline; border: none; color: #BA122B;}
		//table {width:759px; height:569px; }
	</style>
	
	<!--
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script> 
	<script type="text/javascript">
		$(document).ready(function(){
			alert("ok");
			$("#submit").click(function(){
				var data = "";
				data ="usr="+$("#login").val();
				data +="&pwd="+$("#password").val();
				$.ajax({
					type: "POST",
					url: "login.php",
					data: data,
					success: function(msg){
						$("#result").html(msg);
					}
				});
			});
		});
	</script>
	-->
	<script type="text/javascript">
	function validate_required(field,alerttxt){
		var val = document.getElementById(field).value;
		if (val==null||val==""){
			alert(alerttxt);
			document.getElementById(field).focus();
			return false;
		}
		else{
			return true;
		}
	}
	function validate_form(){
		if (validate_required("login","the login must not be empty")==false){
			return false;
		}
		if (validate_required("password","the password must not be empty")==false){
			return false;
		}
		return true
	}
</script>
	
</head>
<body>

<div align="center" style="position:relative; top:50px;">
	<form onsubmit="return validate_form()" method="post" >
	<table width="759px" height="569px" cellpadding="10" cellspacing="0" background="bg_index.jpg" style="border:thin solid #022E5F;">
		<tr><td height="120px"></td></tr>
		<tr>
			<td>
				<table width="739px" cellpadding="4" cellspacing="3">
				<tr>
				<td width="40px"></td>
				<td colspan="2"><b>WELCOME to the TAO demo portal!</b></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td colspan="2"><b>Please log in to experience the possibilities within TAO:</b></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td width="44%"></td>
				<td></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td width="44%"></td>
				<td></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td></td>
				<td><b><?php if(isset($errorLogin)) echo $errorLogin;?></b></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td></td>
				<td><b>Login</b><input type="text" name="login" id="login" /></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td></td>
				<td><b>Password</b><input type="password" name="password" id="password" /></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td></td>
				<td><input type="submit" id="submit" value="login" /></td>
				</tr>			
				</table>
			</td>
		</tr>
	</table>
	</form>
 
	<div id="result">
	</div>

</div>

</body>
</html>
