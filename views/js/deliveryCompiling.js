
var deliveryUri = '';
var classUri = '';
var testIndex = 0;
var testArray = null;
var progressbar = null;
var progressbarStep = 0;
var remainValue = 0;

function updateProgress(){
	//update progress bar
	
	if(testIndex<testArray.length){
		//compile each test here:
		compileTest(testArray[testIndex].uri);
		
		//increment the index in test array
		testIndex++;
	}else{
		// alert("compilation completed");
		endCompilation();
	}
}

function initCompilation(uri,clazz){
	$("#initCompilation").hide();
	deliveryUri = uri;
	classUri = clazz;
	//detroying the progressbar, if it has been initiated
	if( progressbar != null ){
		$("#progressbar").empty();
		progressbar.progressbar( 'destroy' );
	}
	
	$.ajax({
		type: "POST",
		url: "/taoDelivery/Delivery/initCompilation",
		dataType: "json",
		data: {uri : uri, classUri: clazz},
		success: function(r){
			//save the tests data in a global value
			// deliveryUri = r.uri;
			testArray = r.tests;
			progressbarStep = Math.floor(100/parseInt(testArray.length));
			
			//table creation
			var testTable = '<table id="user-list" class="ui-jqgrid-btable" cellspacing="0" cellpadding="0" border="0" role="grid" aria-multiselectable="false" aria-labelledby="gbox_user-list" style="width: 100%;">'
				+'<thead>'
				+'<tr class="ui-jqgrid-labels" role="rowheader">' 
				+ '<th class="ui-state-default ui-th-column " role="columnheader" style="width: 10px;">Test no</th>'
				+ '<th class="ui-state-default ui-th-column " role="columnheader" style="width: 100px;">Test Label</th>'
				+ '<th class="ui-state-default ui-th-column " role="columnheader" style="width: 250px;"></th>'
				+ '</tr></thead><tbody>';
			var clazz = '';
			
			for (j = 0; j < testArray.length; j++){
				if ((j % 2) == 0)
					clazz = "even";
				else
					clazz = "odd";
				
				var testStatus= __("stand by");
				
				url="#";
				testTable += '<tr class="ui-widget-content jqgrow ' + clazz + '">';
				testTable += '<td style="text-align: center;" role="gridcell">'+ (j+1) +'</td>';
				testTable += '<td style="text-align: center;" role="gridcell"><b>'+ r.tests[j].label +'</b></td>';
				testTable += '<td style="text-align: center;" role="gridcell"><span id="test_compiling_'+getTestId(r.tests[j].uri)+'">'+ testStatus +'</span></td>';
				testTable += '</tr>';
				testTable += '<tr><td colspan="3" id="result'+getTestId(r.tests[j].uri)+'" class="ui-widget-content jqgrow ' + clazz + '"></td></tr>';
			}
			testTable += '</tbody></table>';
			
			$("#testsContainer").html(testTable);
			
			//initiate the progressbar:
			remainValue = 100-progressbarStep*testArray.length;
			progressbar = $("#progressbar").progressbar({ value: 0 }).width("60%");
			
			updateProgress();
		}
	});
}

function getTestId(uri){
	return uri.substr(uri.indexOf(".rdf#")+5);//urlencode??
}

function compileTest(testUri){
	var testTag = "#test_compiling_" + getTestId(testUri);
	$(testTag).html( __("compiling...") );
	// var success=0;
	
	$.ajax({
		type: "POST",
		url: "/taoDelivery/Delivery/compile",
		data: {uri : testUri},
		dataType: "json",
		success: function(r){
		
			if(r.success==1){
				//let the user know that the compilation succeeded
				$(testTag).html( __("ok") );
				
				//update the progress bar
				incrementProgressbar(progressbarStep);
				
				//go to next step
				updateProgress();
			}else{
				if(r.success==2){
					$(testTag).html( __("compiled with warning") );
				}else{
					$(testTag).html( __("compilation failed") );
				}
				
				resultTag="#result"+ getTestId(testUri);
				errorMessage="";
				failedCopy="";
				failedCreation="";
				for(key in r.failed.copiedFiles) {

					failedCopy+= __("the following file(s) could not be copied for the test")+' '+key+":";
					
					for(i=0;i<r.failed.copiedFiles[key].length;i++) {
						failedCopy+="<ul>";
						failedCopy+="<li>"+r.failed.copiedFiles[key][i]+"</li>";
						failedCopy+="</ul>";
					}
				}
				
				for(key in r.failed.createdFiles) {
				
					failedCreation+= __("the following file(s) could not be created for the test")+':';
					
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
				
				//reinitiate the values and suggest recompilation
				testIndex = 0;
				testArray = null;
				$("#initCompilation").html( __("Recompile the delivery") );
			}
		}//end success function callback
	});
}

function endCompilation(){
	//reinitiate the value
	testIndex = 0;
	testArray = null;
	
	$.ajax({
		type: "POST",
		url: "/taoDelivery/Delivery/endCompilation",
		data: {uri:deliveryUri, classUri:classUri},
		dataType: "json",
		success: function(r){
			if(r.result == 1){
				incrementProgressbar(remainValue);
				$("#initCompilation").html( __("Recompile the delivery") ).show();
				$("#compiledDate").html(r.compiledDate);
				$("#progressbar").append(__('complete!'));				
				$(document.createElement("img")).attr({ "src": "/taoDelivery/views/img/ok.png" }).appendTo($("#progressbar"));

			}else{
				alert(__("the delivery has been successfully compiled but an issue happened with the delivery status update"));
			}
		}
	});	
}

function incrementProgressbar(value){
	//update the progress bar
	progressbar.progressbar("option", "value", progressbar.progressbar("option", "value") + value);
}

