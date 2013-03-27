/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

var img_url = root_url + "/taoDelivery/views/img/";
var deliveryUri = '';
var classUri = '';
var testIndex = 0;
var testArray = null;
var progressbar = null;
var progressbarStep = 0;
var remainValue = 0;
var warning = false;

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
	
	$('#generatingProcess_feedback').empty().hide();
	$('#generatingProcess_info').hide();
	
	deliveryUri = uri;
	classUri = clazz;
	
	$("#progressbar").empty();
	
	$.ajax({
		type: "POST",
		url: root_url + ctx_extension + '/' + ctx_module + "/initCompilation",
		dataType: "json",
		data: {uri : uri, classUri: clazz},
		success: function(r){
		
			if(r.resultServer){
				//proceed with the test compilation:
				//save the tests data in a global value
				testArray = r.tests;
				
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
				
				updateProgress();
				
			}else{
				var msg = __('Please select a valid result server in the delivery editing section then try again.<br/>(No valid wsdl contract found for the defined result server)');
				finalMessage(msg, 'failed.png');
			}
			
		}
	});
}

function getTestId(uri){
	return uri.substr(uri.indexOf(".rdf#")+5);//urlencode??
}

function compileTest(testUri){
        var $testTag = $('#test_compiling_' + getTestId(testUri));
        
	$testTag.html( __('compiling...'));
	$('<img src="'+ img_url +'ajax-loader-small.gif" title="'+ __('compiling...') +'"/>').css('top', 2).appendTo($testTag);
        
	$.ajax({
		type: "POST",
		url: root_url + ctx_extension + '/' + ctx_module + "/compile",
		data: {testUri : testUri, deliveryUri: deliveryUri},
		dataType: "json",
		success: function(r){
		
			if(r.success==1){
				//let the user know that the compilation succeeded
				$testTag.html( __("ok") );
				
				//go to next step
				updateProgress();
			}else{
				if(r.success==2){
					warning = true;
					$testTag.html( __("compiled with warning") );
					updateProgress();
				}else{
					$testTag.html( __("compilation failed") );
					//quit compilation and annonce it as failed: print recompile option:
					$("#initCompilation").html( __("Recompile the delivery") ).show();
					
					finalMessage(__('failed!'),'failed.png');
				}
				
				var resultTag="#result"+ getTestId(testUri);
				var errorMessage="";
				var failedCopy="";
				var failedCreation="";
				var untranslatedItems="";
				var unexistingItems = "";
				
				var error="";
				for(var key in r.failed.copiedFiles) {

					failedCopy+= __("The following file(s) could not be copied for the test")+' '+key+":";
					
					for(var i=0;i<r.failed.copiedFiles[key].length;i++) {
						failedCopy+="<ul>";
						failedCopy+="<li>"+r.failed.copiedFiles[key][i]+"</li>";
						failedCopy+="</ul>";
					}
				}
				
				for(var key in r.failed.createdFiles) {
				
					failedCreation+= __("The following file(s) could not be created for the test")+':';
					
					for(var i=0;i<r.failed.createdFiles[key].length;i++) {
						failedCreation+="<ul>";
						failedCreation+="<li>"+r.failed.createdFiles[key][i]+"</li>";
						failedCreation+="</ul>";
					}
				}
				
				for(var key in r.failed.untranslatedItems) {
				
					untranslatedItems+= __("The following item do not exist in or have not been translated into")+' '+key+':';
					
					for(var i=0;i<r.failed.untranslatedItems[key].length;i++) {
						untranslatedItems+="<ul>";
						untranslatedItems+="<li>"+r.failed.untranslatedItems[key][i]+"</li>";
						untranslatedItems+="</ul>";
					}
				}
				
				if(r.failed.unexistingItems) {
				
					unexistingItems+= __("The following items do not exist or are empty")+':';
					unexistingItems+="<ul>";
					for(key in r.failed.unexistingItems) {
						unexistingItems+='<li>'+key+' ('+r.failed.unexistingItems[key]+')</li>';
					}
					unexistingItems+="</ul>";
				}
				
				if(r.failed.errorMsg){
					for(i=0;i<r.failed.errorMsg.length;i++){
						error+='<b>' + r.failed.errorMsg[i] + '</b><br/>';
					}
				}
				
				var $closeButton = $('<a href="#" />').text(__('close')).bind('click', {resultTag:resultTag}, function(e){
					e.preventDefault();
					$(this).parent().hide();
				});
				
				var $errorMessage = $('<div />').css('padding', '20px 30px');
				if(unexistingItems){
					$errorMessage.append(unexistingItems).append('<br/><br/>');
				}
				if(error){
					$errorMessage.append(error).append('<br/><br/>');
				}
				
				$errorMessage.append($closeButton);
				
				// errorMessage="<div>";
				// errorMessage+=failedCopy;
				// errorMessage+="<br/><br/>";
				// errorMessage+=failedCreation;
				// errorMessage+="<br/><br/>";
				// errorMessage+=untranslatedItems;
				// errorMessage+="<br/><br/>";
				// errorMessage+=error;
				// errorMessage+='<br/><br/><a href="#" onclick="$(\''+resultTag+'\').hide(); return false;">close</a>';
				// errorMessage+="</div>";
				
				// $(resultTag).html($errorMessage.html());
				$(resultTag).append($errorMessage);
				
			}
		}//end success function callback
	});
}

function endCompilation(){
	//reinitiate the value
	testIndex = 0;
	testArray = null;
	
	$('#generatingProcess_info').show();
	
	$.ajax({
		type: "POST",
		url: root_url + ctx_extension + '/' + ctx_module + "/endCompilation",
		data: {uri:deliveryUri, classUri:classUri},
		dataType: "json",
		success: function(r){
			$('#generatingProcess_info').hide();
			if(r.result == 1){
				$("#initCompilation").html( __("Recompile the delivery") ).show();
				$("#compiledDate").html(r.compiledDate);
				if(warning){
					finalMessage(__('complete with warning'),'warning.png');
				}else{
					finalMessage(__('complete!'),'ok.png');
				}
			}else{
				var $deliveryError = $('<div />');
				var msg = '';
				if(r.errors.delivery){
					msg = __('Error in delivery process');
					
					if(!r.errors.delivery.initialActivity){
						//there is no initial activity
						var noInitialActivity = __('there is no initial activity for the delivery process definition');
						$deliveryError.append('<br/>').append(noInitialActivity);
					}
					
					if(r.errors.delivery.isolatedConnectors.length){
						var isolatedConnectors = __("The delivery process has isolated connectors")+':';
						isolatedConnectors += "<ul>";
						for(var i=0; i<r.errors.delivery.isolatedConnectors.length; i++){
							isolatedConnectors += '<li>'+r.errors.delivery.isolatedConnectors[i]+'</li>';
						}
						isolatedConnectors += "</ul>";
						$deliveryError.append('<br/>').append(isolatedConnectors);
					}
					
				}else{
					msg = __('Error in test process');
					
					for(var i=0; i<r.errors.tests.length; i++){
						var testError = r.errors.tests[i];
						$deliveryError.append('<br/>').append(__('Issue found for the process definiiton of the test')+' "'+testError.label+'":').append('<br/>');
						if(!testError.initialActivity){
							//there is no initial activity
							var noInitialActivity = __('There is no initial activity for the test process definition');
							$deliveryError.append('<br/>').append(noInitialActivity);
						}
						
						if(testError.isolatedConnectors.length){
							var isolatedConnectors = __("The test process has isolated connectors")+':';
							isolatedConnectors += "<ul>";
							for(var i=0; i<testError.isolatedConnectors.length; i++){
								isolatedConnectors += '<li>'+testError.isolatedConnectors[i]+'</li>';
							}
							isolatedConnectors += "</ul>";
							$deliveryError.append('<br/>').append(isolatedConnectors);
						}
					}
					
					
				}
				
				$('#generatingProcess_feedback').append($deliveryError).show();
				
				finalMessage(msg, 'failed.png');
			}
		}
	});	
}

function finalMessage(msg, imageFile){
	$('<img/>').attr("src", img_url + imageFile).appendTo($("#progressbar"));
	$("#progressbar").append(msg);
        
	//reinitiate the values and suggest recompilation
	testIndex = 0;
	testArray = null;
	$("#initCompilation").html( __("Recompile the delivery") );
}

