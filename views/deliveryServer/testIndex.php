<?php
// session_start();
require_once('config.php');
if(!isset($_SESSION["subject"]["uri"])){
	header("location: index.php");
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
  <title>TAO - An Open and Versatile Computer-Based Assessment Platform - Test Index</title>

	<style type="text/css">
		body {background: #CDCDCD;color: #022E5F; font-family: verdana, arial, sans-serif;}
		td {font-size: 14px;}
		a {text-decoration: none;font-weight: bold; border: none; color: #BA122B;}
		a:hover {text-decoration: underline; border: none; color: #BA122B;}
		tabCenter{align: center;}
	</style>
	
	<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script> -->
	<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
	<script type="text/javascript">
		function get_tests(page){
			page = parseInt(page);
			data ="page="+page;
			$.ajax({
				type: "POST",
				url: "testListing.php",
				data: data,
				dataType: "json",
				success: function(result){
					 print_result(result);
				}
			});
		}
		
		function print_result(result){
				// var json_string = result.substr(result.indexOf("result=") + 7);
						//var data= "test no="+r.tests[5]+" current page="+r.pager.current+" total="+r.pager.total;//for test only
						
						r=result;
						//pager creation
						var pager = "";
						if (r.pager.total > 1) {
							pager += '<p align="center">';
							
							//previous page
							if (r.pager.current > 1) {
								url = "get_tests(" + (parseInt(r.pager.current) - 1) + ")";
								pager += '<a href="#" onclick="' + url + '">prev.</a>&nbsp;&nbsp;';
							}
							// page listing
							imax = Math.min(r.pager.total, 10);
							for (i = 1; i <= imax; i++) {
								url = "get_tests('" + i + "')";
								pager += '<a href="#" onclick="' + url + '">[' + i + ']</a>';
								// pager += '<a onclick="alert(url)">[' + i + ']</a>';
							}
							// following page
							if (r.pager.current < r.pager.total) {
								url = "get_tests(" + (parseInt(r.pager.current) + 1) + ")";
								pager += '&nbsp;&nbsp;<a href="#" onclick="' + url + '">next</a>';
							}
							pager += '</p>';
						}
						
						//table creation
						var testTable = '<table><thead><tr>' 
							+ '<td width="60px" style="text-align:right;"><b>Test no</b></td>'
							+ '<td width="250px" style="text-align: center;"><b>Label</b></td>'
							+ '</tr></thead><tbody>';
						var clazz = '';
						for (i = r.pager.start; i <= r.pager.end; i++) {
							if ((i % 2) == 0)
								clazz = "even";
							else
								clazz = "odd";
							
							var url="<?=TAODELIVERY_PATH?>compiled/"+r.tests[i].uri +"/theTest.php?subject="+r.subject.uri;	
							testTable += '<tr class="test_list ' + clazz + '">';
							testTable += '<td style="text-align: center;">'+ (i+1) +'</td>';
							testTable += '<td style="text-align: center;"><a href="'+ url +'" target="_blank">'+ r.tests[i].label +'</a></td>';
							testTable += '</tr>';
						}
						testTable += '</tbody></table>';
						
						$("#result").html(testTable+pager);
		}
			
		$(document).ready(function(){
			get_tests(1);
		});
	</script>
	
</head>
<body>
<div align="center" style="position:relative; top:50px;">
<table  width="759px" height="569px" cellpadding="10" cellspacing="0" background="bg_index.jpg" style="border:thin solid #022E5F;">
	<tr height="20px">
		<td>You are currently logged in as <b><?=$_SESSION["subject"]["label"]?></b><br/><a href="logout.php">logout</a></td>
		<td/>
	</tr>
	<tr height="150px"/>
	<tr style="text-align: center; vertical-align:top;">
		<td>Click on the label of a test to start:</td>
		<td id="result" width="350px" >
		</td>
	</tr>
</table>
</div>


</body>
</html>

