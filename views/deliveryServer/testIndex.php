<?php
session_start();
// require_once('../../includes/common.php');
// require_once('../../includes/constants.php');
// require_once('../../includes/config.php');
// require_once('../../models/classes/class.DeliveryService.php');

if(!isset($_SESSION["subject"]["uri"])){
	header("location: index.php");
}
?>
<html>
<head>
	<title>Test Index</title>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
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
					// alert(result.pager.total);
					 print_result(result);
				}
			});
		}
		
		function print_result(result){
				// var json_string = result.substr(result.indexOf("result=") + 7);
						// r = eval('(' + json_string + ')');
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
							+ '<td>Test no</td>'
							+ '<td>Label</td>'
							+ '<td>Comment</td>'
							+ '</tr></thead><tbody>';
						var clazz = '';
						for (i = r.pager.start; i <= r.pager.end; i++) {
							if ((i % 2) == 0)
								clazz = "even";
							else
								clazz = "odd";
							
							var url="../../compiled/"+r.tests[i].uri +"/start.php?subject="+r.subject.uri;	
							testTable += '<tr class="test_list ' + clazz + '">';
							testTable += '<td>'+ i +'</td>';
							testTable += '<td><a href="'+ url +'" target="_blank">'+ r.tests[i].label +'</a></td>';
							testTable += '<td>'+ r.tests[i].comment +'</td>';
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
<a href="logout.php">logout</a>
<div id="result0"></div>
<div id="result"></div>
</body>
</html>

