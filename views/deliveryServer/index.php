<?php session_start();?>

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
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script> 
	<script type="text/javascript">
		$(document).ready(function(){
			// alert("ok");
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
	
</head>
<body>

<div align="center" style="position:relative; top:50px;">

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
			<td><b>Login Box</b></td>
			</tr>
			<tr>
			<td width="40px"></td>
			<td></td>
			<td><b>Login</b><input type="text" id="login" /></td>
			</tr>
			<tr>
			<td width="40px"></td>
			<td></td>
			<td><b>Password</b><input type="password" id="password" /></td>
			</tr>
			<tr>
			<td width="40px"></td>
			<td></td>
			<td><input type="button" id="submit" value="login"/></td>
			</tr>			
			</table>

			</td>
		</tr>
 </table>

<div id="result">
</div>

</div>



</body>
</html>
