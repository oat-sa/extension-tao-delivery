<?php
require_once('../../includes/common.php');
require_once('../../includes/constants.php');
require_once('../../includes/config.php');
require_once('../../models/classes/class.DeliveryService.php');

if(!isset($_SESSION["subject"]["uri"])){
	header("location: index.php");
}
?>
<html>
<head>
	<title>Test Index</title>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript">
		
		$(document).ready(function(){
		
			get_tests(1);
			
			$("#submit").click(function(){
				
			});
			
			function get_tests(page){
				data ="page="+page;
				$.ajax({
					type: "POST",
					url: "testListing.php",
					data: data,
					success: function(result){
						$("#result0").html(result);
						var json_string = result.substr(result.indexOf("result=") + 7);
						r = eval('(' + json_string + ')');
						//var data= "test no="+r.tests[5]+" current page="+r.pager.current+" total="+r.pager.total;//for test only
						
						//pager creation
						var pager = "";
						if (r.pager.total > 1) {
							pager += '<p align="center">';
							
							//previous page
							if (r.pager.current > 1) {
								url = "get_tests(" + (parseInt(r.pager.current) - 1) + ")";
								pager += '<a class="nav" onclick="' + url + '">prev.</a>&nbsp;&nbsp;';
							}
							// page listing
							imax = Math.min(r.pager.total, 10);
							for (i = 1; i <= imax; i++) {
								url = "get_tests(" + i + ")";
								pager += '<a class="nav" onclick="' + url + '">[' + i + ']</a>';
							}
							// following page
							if (r.pager.current < r.pager.total) {
								url = "get_tests(" + (parseInt(r.pager.current) + 1) + ")";
								pager += '<a class="nav" onclick="' + url + '">next</a>&nbsp;&nbsp;';
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
								
							testTable += '<tr class="test_list ' + clazz + '">';
							testTable += '<td>'+ i +'</td>';
							testTable += '<td>'+ r.tests[i].label +'</td>';
							testTable += '<td>'+ r.tests[i].uri +'</td>';
							testTable += '</tr>';
						}
						testTable += '</tbody></table>';
						
						$("#result").html(testTable+pager);
					}
				});
			}
			
			/*
			function receive_on(response) {
				// alert(response);
				indexPager = response.indexOf("pager=");
				page = response.substr(indexPager + 6);
				r = eval('(' + response.substring(0, indexPager) + ')');
				var content = '<table><thead><tr>' 
					+ '<td>Test no</td>'
					+ '<td>Label</td>'
					+ '<td>Comment</td>'
					+ '</tr></thead><tbody>';
				var clazz = '';
				for (i = 0; i < r.tests.length; i++) {
					if ((i % 2) == 0)
						clazz = "even";
					else
						clazz = "odd";
					provider = r.receive_on[i].provider;
					provider_url = "/profile/" + provider;
					title = r.receive_on[i].title;
					msg = r.receive_on[i].msg;
					url = r.receive_on[i].url;
					rwd = r.receive_on[i].reward;
					date = format_date(r.receive_on[i].timingType, r.receive_on[i].start,
							r.receive_on[i].end);
					status = r.receive_on[i].status;
					// action ='<a
					// onclick="action_update('+action+','+id+')">'+action+'</a>';

					// mettre en forme ici
					content += '<tr class="message ' + clas + '" name="bid">';
					content += '<td><a href="' + url + '">' + title + '</a></td>';
					content += '<td>' + status + '</td>';
					content += '<td><a href="' + provider_url + '">' + provider
							+ '</a></td>';
					content += '<td>' + rwd + '</td>';
					content += '<td>' + msg + '</td>';
					content += '<td>' + "action" + '</td>';
					// alert("oksd");
				}
				content += '</tbody></table>';
				content += paging('request', 'on', page);
				// gerer le pager ici:
				$("#receive_on").html(content);
			}
			function paging(reqType, type, page) {
				// alert(page);
				var paging = page.split("/", 2);
				current = paging[0];
				total = paging[1];
				var pager = "";
				if (total > 1) {
					pager += '<p align="center">';
					if (current > 1) {
						url = "get_listing('" + reqType + "','" + type + "',"
								+ (parseInt(current) - 1) + ")";
						pager += '<a class="nav" onclick="' + url + '">prev.</a>&nbsp;&nbsp;';
					}
					// --- listing des pages
					imax = Math.min(total, 10);
					for (i = 1; i <= imax; i++) {
						url = "get_listing('" + reqType + "','" + type + "'," + i + ")";
						pager += '<a class="nav" onclick="' + url + '">[' + i + ']</a>';
					}
					// --- page suivante
					if (current < total) {
						url = "get_listing('" + reqType + "','" + type + "',"
								+ (parseInt(current) + 1) + ")";
						pager += '<a class="nav" onclick="' + url + '">next</a>&nbsp;&nbsp;';
					}
					pager += '</p>';
				}
				return pager;
			}
			*/
		});
	</script>
</head>
<body>
<a href="logout.php">logout</a>
<div id="result0"></div>
<div id="result"></div>
</body>
</html>

