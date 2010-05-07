function get_tests(){
	$.ajax({
		type: "POST",
		url: base_url + "/Delivery/deliveryListing",
		dataType: "json",
		success: function(result){
			//table creation
			var testTable = '<table id="user-list" class="ui-jqgrid-btable" cellspacing="0" cellpadding="0" border="0" role="grid" aria-multiselectable="false" aria-labelledby="gbox_user-list" style="width: 1047px;">'
				+'<thead>'
				+'<tr class="ui-jqgrid-labels" role="rowheader">' 
				+ '<th class="ui-state-default ui-th-column " role="columnheader" style="width: 20px;">Test no</th>'
				+ '<th class="ui-state-default ui-th-column " role="columnheader" style="width: 250px;">Test Label</th>'
				+ '<th class="ui-state-default ui-th-column " role="columnheader" style="width: 100px;"></th>'
				+ '</tr></thead><tbody>';
			var clazz = '';
			
			r=result;
			for (j = 0; j < r.tests.length; j++){
				if ((j % 2) == 0)
					clazz = "even";
				else
					clazz = "odd";
				
				var testStatus="";
				if(r.tests[j].compiled==1){
					//if the delivery has been compiled, add a preview button and offer the option to recompile:
					testStatus='<a href="../../taoDelivery/compiled/'+r.tests[j].id+'/theTest.php?subject=previewer" target="_blank">preview</a> / <a href="#" onclick="compile(\''+r.tests[j].uri+'\'); return false;">recompile</a>';
				}else{
					if(r.tests[j].active==1){
						//if the delivery is active ut not compiled yet, add a compile button:
						testStatus='<a href="#" onclick="compile(\''+r.tests[j].uri+'\'); return false;">compile</a>';
					}else{
						//if it is inactive, simply let the user know:
						testStatus="inactive";
					}
				}
				
				url="#";
				testTable += '<tr class="ui-widget-content jqgrow ' + clazz + '">';
				testTable += '<td style="text-align: center;" role="gridcell">'+ (j+1) +'</td>';
				testTable += '<td style="text-align: center;" role="gridcell"><b>'+ r.tests[j].label +'</b></td>';
				testTable += '<td style="text-align: center;" role="gridcell"><span id="test'+r.tests[j].id+'">'+ testStatus +'</span></td>';
				testTable += '</tr>';
				testTable += '<tr><td colspan="3" id="result'+r.tests[j].id+'" class="ui-widget-content jqgrow ' + clazz + '"></td></tr>';
			}
			testTable += '</tbody></table>';
			
			$("#tests").html(testTable);
		}
	});
}

function compile(testUri){
	var testTag="#test"+testUri.substr(testUri.indexOf(".rdf#")+5);
	$(testTag).html("compiling...");
	var data="uri="+testUri;
	$.ajax({
		type: "POST",
		url: root_url + "/taoDelivery/Delivery/compile",
		data: data,
		dataType: "json",
		success: function(r){
		
			if(r.success==1){
				get_tests();
			}else{
				if(r.success==2){
					$(testTag).html("compiled with warning");
				}else{
					$(testTag).html("compilation failed");
				}
				
				resultTag="#result"+testUri.substr(testUri.indexOf(".rdf#")+5);
				errorMessage="";
				failedCopy="";
				failedCreation="";
				for(key in r.failed.copiedFiles) {

					failedCopy+="the following file(s) could not be copied for the test "+key+":";
					
					for(i=0;i<r.failed.copiedFiles[key].length;i++) {
						failedCopy+="<ul>";
						failedCopy+="<li>"+r.failed.copiedFiles[key][i]+"</li>";
						failedCopy+="</ul>";
					}
				}
				
				for(key in r.failed.createdFiles) {
				
					failedCreation+="the following file(s) could not be created for the test:";
					
					for(i=0;i<r.failed.createdFiles[key].length;i++) {
						failedCreation+="<ul>";
						failedCreation+="<li>"+r.failed.createdFiles[key][i]+"</li>";
						failedCreation+="</ul>";
					}
				}
				
				errorMessage="<div>";
				errorMessage+=failedCopy;
				errorMessage+="<br/><br/>";
				errorMessage+=failedCreation;
				errorMessage+='<br/><br/><a href="#" onclick="$(\''+resultTag+'\').hide(); return false;">close</a>';
				errorMessage+="</div>";
				
				$(resultTag).html(errorMessage);
			}
		}
	});
}

$(document).ready(function(){
	get_tests();
});		