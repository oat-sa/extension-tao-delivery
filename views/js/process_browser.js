freezeGUI = false;
consistencyActionPerformed = false;
window.onscroll = adjustFloatingButtons;
window.onresize = adjustFloatingButtons;
suppressLocation = '';
consistencySelectedElement = 0;
consistencyInvolvedElements = 0;
window.processUri = null;

submitActivity = function(url)
{
	window.location.href = url;
}

showNextButton = function()
{
	$('#next').attr('disabled', false);
}

registerToServiceEvent = function(handler, eventType, serviceId)
{
	$('#' + serviceId).get(0).contentWindow.giveMeAnHandler(handler, eventType);
}

getToolHeight = function(index)
{
	var tool = $(index).get(0);
	if (tool)
	{
		var height = tool.contentWindow.getHeight();
		return height;
	}
	
}

insertShortcutsInTool = function(index)
{
	$('#xul_question').eq(0).get(0).contentWindow.shortcuts = shortcuts;
}

function keyboardHandlerTreshold(event)
{
	if ((event.keyCode || event.charCode) && (event.shiftKey == true || event.shiftKey == false)
					  && (event.ctrlKey == true || event.ctrlKey == false)
					  && (event.altKey == true || event.altKey == false))
		return true;
		
	else
		return false;
}

function keyboardHandler(event)
{
	// Should we have to handle the event ? Are keyboard shortcuts enabled ?
	if (window.shortcuts && keyboardHandlerTreshold(event))
	{
		// keyboardFunction is defined in api/javascript/keyboard.js
		var isFunction = isKeyboardFunction(event);
		
		if (isFunction)
		{
			if (matchKeyboardFunction(event, 'next_activity'))
			{
				if (!freezeGUI && !isHyperViewInTransaction() && $('#xul_question').get(0).contentWindow.isFinished())
				{	
					freezeGUI = true;
					$('#xul_question').get(0).contentWindow.submitHyperView(function() {
						goNextFromService();
					});
				}
			}
			else if (matchKeyboardFunction(event, 'go_to_last_answered_question'))
			{	
				if (!freezeGUI && !isHyperViewInTransaction() && $('#xul_question').get(0).contentWindow.isFinished())
				{
					// Log this event.
					logBusinessEvent('MOVE_LAST', getCurrentItemId(), 'Moved to last answered question');
					
					$('#xul_question').get(0).contentWindow.submitHyperView(function() {
						window.location.href = '../../../taoqual/plugins/UserFrontend/index.php/processBrowser/jumpLast?processUri=' + window.processUri;
					});
				}
			}
			else if (matchKeyboardFunction(event, 'validate'))
			{
				if ($('#xul_question').get(0).contentWindow.isFinished())
				{	
					freezeGUI = true;
					$('#xul_question').get(0).contentWindow.submitHyperView(function() {
						goNextFromService();
					});
				}
			}
			else if (matchKeyboardFunction(event, 'previous_activity'))
			{				  
				$('#xul_question').get(0).contentWindow.submitHyperView(function() {
					goBackFromService();
				});
			}
			else if (matchKeyboardFunction(event, 'pause_process'))	
			{		
				$('#xul_question').get(0).contentWindow.submitHyperView(function() {
					pauseProcess();
				});
			}
			else if (matchKeyboardFunction(event, 'breakoff'))	
			{			
				$('#xul_question').get(0).contentWindow.submitHyperView(function() {
					breakOff();
				});
			}
			else if (matchKeyboardFunction(event, 'show_calendar'))
			{				
				event.preventDefault();
				event.stopPropagation();
				showCalendar();
			}
			else if (matchKeyboardFunction(event, 'watch'))
			{
				// Log this event.
				$('#xul_question').get(0).contentWindow.submitHyperView(function() {
					event.preventDefault();
					event.stopPropagation();
					showWatch();
				});
			}
			else if (matchKeyboardFunction(event, 'testing'))
			{
				$('#xul_question').get(0).contentWindow.submitHyperView();
				
				event.preventDefault();
				event.stopPropagation();
				showTesting();
			}
			else if (matchKeyboardFunction(event, 'switch_language'))
			{
				event.preventDefault();
				event.stopPropagation();
				
				$('#xul_question').get(0).contentWindow.submitHyperView(function () {
					switchLanguage();
				});
				
			}
			else if (matchKeyboardFunction(event, 'refused'))
			{
				event.preventDefault();
				event.stopPropagation();
			}
		}
	}
}

function openRangeErrorDialog(message)
{
	generateGenericErrorDialog('ranges', message);
}

function helpHandler(content, e)
{
	// log this event.
	logBusinessEvent('OPEN_HELP', getCurrentItemId(), 'Help requested');
	
	if (!$('ui-dialog-overlay').length)
	{
		// We remove everything in the dialog.
		$('#help').empty();
		
		// We insert the help content in the help container.
		$('#help').append(content);
		
		$('#help').taoqualDialog({
			draggable:true,
			modal:true,
			position:'center',
			resizable: true,
			width:640,
			height: 320,
			overlay: {'background-color':'#000', opacity: '0.3'},
			keyboard: function(e) { if (e.keyCode != 27) { e.stopPropagation(); e.preventDefault(); }},
			close: function () {$('#help').dialog('destroy'); $('#xul_question').get(0).contentWindow.focusNode(null, true, true);}
		});
		
		// The content of the dialog must be shown.
		$('#help').toggle();
	}
}

function openConsistencyDialog(processUri, activityUri, activities, message, suppressable)
{
	if (!$('ui-dialog-overlay').length)
	{
		// Freeze underlying GUI.
		$('#xul_question').get(0).contentWindow.freezeGUI();
		
		consistencySelectedElement = 0;
		consistencyInvolvedElements = activities.length;
		suppressLocation = '../../index.php/processBrowser/next?processUri=' + processUri + '&ignoreConsistency=true';
		suppressableConsistencyDialog = suppressable;
		
		// We empty the consistency dialog (defensive).
		$('#consistency').empty();
		
		var classForTextEdit = (suppressable) ? 'soft_text' : 'hard_text';
		
		// We insert the needed content in the consistency dialog container.
		$('#consistency').append('<p id="comment" class="' + classForTextEdit + '">' + message + '</p>');
		
		$('#consistency').append('<ul id="involved_activities">');
		
		for (var i = 0; i < activities.length; i++)
		{
			var listItem = $('<li></li>');
			var jumperLink = $('<a></a>');
			
			jumperLink.addClass('involved_activity');
			jumperLink.text(activities[i].label);
			jumperLink.attr('href', '../../index.php/processBrowser/jumpBack?processUri=' + activities[i].processUri + '&activityUri=' + activities[i].uri);
			
			jumperLink.click(function(e){
				e.stopPropagation();
				e.preventDefault();
				
				if (!window.consistencyActionPerformed)
				{
					window.consistencyActionPerformed = true;
					window.location.href = $(this).attr('href');
				}
			});
			
			listItem.append(jumperLink);
			$('#involved_activities').append(listItem);
		}
		
		$('#involved_activities > li').eq(0).addClass('activity_activated');
		
		$('#consistency').append('</ul>');
		
		var buttons = new Object();
		
		buttons[I18n.__("Go to")] = function() {
			
			if (!window.consistencyActionPerformed)
			{
				window.consistencyActionPerformed = true;
				disableConsistencyButtons();
				window.location.href = '../../index.php/processBrowser/jumpBack?processUri=' + activities[consistencySelectedElement].processUri + '&activityUri=' + activities[consistencySelectedElement].uri;	
			}	
		}
		
		buttons[I18n.__("Close (Esc)")] = function() {
			
			if (!window.consistencyActionPerformed)
			{
				$('#consistency').dialog('destroy');
				$('#xul_question').get(0).contentWindow.unfreezeGUI();
				$('#xul_question').get(0).contentWindow.focusNode(null, true, true);
			}
		}
		
		if (suppressable)
		{
			buttons[I18n.__("Suppress")] = function(e) {
				e.preventDefault();
				e.stopPropagation();
				if (!window.consistencyActionPerformed)
				{
					window.consistencyActionPerformed = true;
					disableConsistencyButtons();
					window.location.href = suppressLocation;
				}
			};
		}
		
		$('#consistency').taoqualDialog({
			buttons: buttons,
			keyboard: consistencyDialogKeyboardHandler,
			draggable:true,
			modal: true,
			position:'center',
			resizable:true,
			width:640,
			height: (activities.length * 25) + 70 + 50 + 40,
			closable: true,
			overlay: {'background-color':'#000', opacity: '0.3'},
			close: function () {		
					if (!window.consistencyActionPerformed)
					{
						$('#xul_question').get(0).contentWindow.unfreezeGUI();
						$('#xul_question').get(0).contentWindow.focusNode(null, true, true);
						
						$('#consistency').dialog('destroy'); 
					}
				}
		});
		
		$('#consistency').toggle();
		$('#involved_activities').focus();
	}
}

function openErrorDialog(msg)
{
	generateGenericErrorDialog('errors', msg);
}

function generateGenericErrorDialog(elementId, message)
{
	$('#' + elementId).empty();
	$('#' + elementId).append('<p class="hard_text">' + message + '</p>');

	var buttons = new Object();
	buttons[I18n.__("Close (Esc)")] = function () {$('#'  + elementId).dialog('destroy'); $('#xul_question').get(0).contentWindow.focusNode(null, true, true);};

	$('#'  + elementId).taoqualDialog({
		buttons: buttons,
		draggable: true,
		modal: true,
		position: 'center',
		resizable: true,
		width: 640,
		overlay: {'background-color':'#000', opacity: '0.3'},
		keyboard: genericDialogKeyboardHandler,
		closable: true,
		close: function () {$('#'  + elementId).dialog('destroy'); $('#xul_question').get(0).contentWindow.focusNode(null, true, true);}
	});
	
	$('#' + elementId).toggle();
}

function genericDialogKeyboardHandler(event)
{
	if (!$('ui-dialog-overlay').length)
	{
		event = event.originalEvent;
		
		// React only on keyup
		if (event.type == 'keypress')
		{
			if (matchKeyboardFunction(event, 'correct_errors'))
			{
				$('#ranges, #errors').dialog('destroy'); $('#xul_question').get(0).contentWindow.focusNode(null, true, true);
			}
		}
		
		event.preventDefault();
		event.stopPropagation();
	}
}

function adjustFloatingButtons()
{
	$('#next_floating,#back_floating').each(function(i)
	{
		try
		{
			style = window.getComputedStyle($('#xul_question').get(0), null);
			height = style.getPropertyCSSValue('height').getFloatValue(CSSPrimitiveValue.CSS_PX);
			$(this).css('top', Math.floor(height / 2));
		}
		catch (e)
		{
			// Sometimes it fail... I don't know why !!!
		}
	});
}

function consistencyDialogKeyboardHandler(event, self)
{
	var event = event.originalEvent;
	
	if (matchKeyboardFunction(event, 'suppress_edit'))
	{
		if (suppressableConsistencyDialog)
		{
			if (!window.consistencyActionPerformed)
			{
				window.location.href = suppressLocation;
				event.preventDefault();
				event.stopPropagation();
				consistencyActionPerformed = true;
				disableConsistencyButtons();
				
				// Log this event.
				//logEvent(event, 'The suppress_edit shortcut has been triggered using keyboard', 'window');
			}
		}
	}
	else if (matchKeyboardFunction(event, 'select'))
	{
		if (!window.consistencyActionPerformed)
		{
			var loc = $('#involved_activities > li > a').get(consistencySelectedElement).href;
			consistencyActionPerformed = true;
			window.location.href = loc;
			event.preventDefault();
			event.stopPropagation();
			disableConsistencyButtons();
		}
		
		// Log this event.
		//logEvent(event, 'The select shortcut has been triggered using keyboard', 'window');
	}
	else if (matchKeyboardFunction(event, 'previous_edit_activity'))
	{
		if (!window.consistencyActionPerformed)
		{
			$('#involved_activities > li').removeClass('activity_activated');
			$('#involved_activities > li').addClass('activity_deactivated');
			
			if (consistencySelectedElement == 0)
				consistencySelectedElement = consistencyInvolvedElements - 1;
			else
				consistencySelectedElement--;
				
			$('#involved_activities > li').eq(consistencySelectedElement).addClass('activity_activated');
			event.preventDefault();
			event.stopPropagation();
		}
		
		// Log this event.
		//logEvent(event, 'The previous_edit_activity shortcut has been triggered using keyboard', 'window');
	}
	else if (matchKeyboardFunction(event, 'next_edit_activity'))
	{
		if (!window.consistencyActionPerformed)
		{
			$('#involved_activities > li').removeClass('activity_activated');
			$('#involved_activities > li').addClass('activity_deactivated');
			
			if (consistencySelectedElement == consistencyInvolvedElements - 1)
				consistencySelectedElement = 0;
			else
				consistencySelectedElement++;
				
			$('#involved_activities > li').eq(consistencySelectedElement).addClass('activity_activated');
			
			event.preventDefault();
			event.stopPropagation();
		}
		
		// Log this event.
		//logEvent(event, 'The next_edit_activity shortcut has been triggered using keyboard', 'window');
	}
}

/**
*redirects the user interface to a constant location when paused,
*/
function pauseProcess()
{
	window.location.href = '../../../taoqual/plugins/UserFrontend/index.php/processBrowser/pause?processUri=' + window.processUri;	
}

/**
*redirects the user interface to a constant location when broke off
*/
function breakOff()
{	
	if (window.breakable)
	{
		window.location.href = '../../../taoqual/plugins/UserFrontend/index.php/processBrowser/breakOff?processUri=' + window.processUri;
	}
}

/** 
* defines keyboard handling on annotate box
**/
function annotateDialogKeyboardHandler(event, self)
{
	// Be really carefull on this line.
	// Jquery UI uses wrappers for events.
	var event = event.originalEvent;
	
	if (matchKeyboardFunction(event, 'submit_annotation'))
	{
		submitAnnotationData();
		event.preventDefault();
		event.stopPropagation();
		
		// Log this event.
		//logEvent(event, 'The submit_annotation shortcut has been triggered using keyboard', 'window');
	}
	else if (matchKeyboardFunction(event, 'focus_annotation_type'))
	{
		$('#annotationClassification').focus();
	
		event.preventDefault();
		event.stopPropagation();
		
		// Log this event.
		//logEvent(event, 'The focus_annotation_type shortcut has been triggered using keyboard', 'window');
	}
	else if (matchKeyboardFunction(event, 'focus_annotation_question'))
	{
		$('#annotationScope').focus();
		event.preventDefault();
		event.stopPropagation();
		
		// Log this event.
		//logEvent(event, 'The focus_annotation_question shortcut has been triggered using keyboard', 'window');
	}
	else if (matchKeyboardFunction(event, 'focus_annotation_text'))
	{		
		focusDialog();
		event.preventDefault();
		event.stopPropagation();
		
		// Log this event.
		//logEvent(event, 'The focus_annotation_text shortcut has been triggered using keyboard', 'window');
	}
}


/**
* display a popup enabling the user to view and add new annotations for any of the resource presented on the screen and also classificate them
* @param  annotationsClassificationsJsArray  contains relevant annotations classes  ()different kind of annotations described in a model within generis
* @param annotationsResourcesJsArray (a list of resources which can be annotated)
* @param activeResource the uri of the current resource being annotated
**/
function annotate(annotationsClassificationsJsArray, annotationsResourcesJsArray, activeResource)
{
	logBusinessEvent('OPEN_REMARKS', getCurrentItemId(), 'The remark tool was opened');
	
	//require some vocabulary internationalization here ... 
	var strQuestionField 		= '* ' + I18n.__("Question (Ctrl-Q)");
	var strTypeField 			= '* ' + I18n.__("Type (Ctrl-T)");
	var strAddField 			= I18n.__("Add (Ctrl-A)");
	var strDefaultValueField 	= I18n.__("Enter remark text here");
	var strCloseField 			= I18n.__("Close (Esc)");
	var strMainBox 				= I18n.__("(Ctrl-C)");
	
	$('#annotationBox').empty();
	
	//-- creates the dialog box
	//Buttons' captions and associated functions (Great solution ! Isn't it ? :D)
	var buttons = new Object();
	//buttons[I18n.__("Export - Debug only (Ctrl-E)")] = function () { exportAnnotations(); };
	buttons[I18n.__("Close (Esc)")] = function () { $('#annotationBox').dialog('destroy'); };

	$('#annotationBox').taoqualDialog({
		buttons: buttons,
		keyboard: annotateDialogKeyboardHandler,
		draggable:true,
		modal: true,
		position:'center',
		resizable:false,
		width:660,
		height: 400,
		overlay: {'background-color':'#000', opacity: '0.3'},
		close: function () { $('#annotationBox').dialog('destroy'); $('#xul_question').get(0).contentWindow.focusNode(null, true, true);}
	});
	
	
	//creates a table insert it into the dialog box adding the two drop downs, the textarea for the remark and the button for addition
	var tableBox = '';
	tableBox += '<table>';
	tableBox += '<tr><td>';
	tableBox += '<textarea name="annotationValue" id="annotationValue" style="width:420px;height:100px" ></textarea><br/>'+strMainBox;
	tableBox += '</td><td>';
	
	//drop down to select the resource being annotated 
	var scope='';
	scope='<select onchange="focusDialog()" style="width:150px;" id="annotationScope">';
	for (var i = 0; i < annotationsResourcesJsArray.length; i++)
	{
		if (activeResource==annotationsResourcesJsArray[i][0])
			scope+='<option selected="true" value="'+annotationsResourcesJsArray[i][0]+'">'+annotationsResourcesJsArray[i][1]+'</option>';

		else
			scope+='<option value="'+annotationsResourcesJsArray[i][0]+'">'+annotationsResourcesJsArray[i][1]+'</option>';
	}
	
	scope+='</select>';
	
	
	//drop down to select the annotation type
	var selectType='';
	selectType='<center><select style="width:150px" id="annotationClassification">';
	for (var i = 0; i < annotationsClassificationsJsArray.length; i++)
	{
		selectType+='<option value="'+annotationsClassificationsJsArray[i][0]+'">'+annotationsClassificationsJsArray[i][1]+'';
	}
	selectType+='</select></center>';
	tableBox += '<i>'+strTypeField+'</i>';
	tableBox += selectType;
	tableBox += '<br/><br/><br/><center><a style="position:absolute;" href="#" onClick="submitAnnotationData()" ><img border=1 src="../../views/PIAAC/img/filesaveas.png"> (Ctrl-A)</a></center>';
	tableBox +='</td></tr></table>';
	
	tableBox += '<span style="visibility:hidden;"><i>'+strQuestionField+'</i>';
	tableBox += scope;
	tableBox += '</span>';

	$('#annotationBox').append(tableBox);
	
	$('#annotationValue').get(0).focus();
	$('#annotationValue').get(0).select();
	
	//thanks jerome !
	$('#annotationBox').append('<div id="messages" />');
	$('#annotationBox').toggle();

	
	//retrieve the previous messages from the servers and append them to the dialog box 
	getAnnotationMsgs();
	//displayAnnotationMsgs(annotationsJsArray);
}
/**
* this function is called whenever the button on the annottate box is clicked or when the right shorcut is being used
* It sends the comment message together with the type and the scope of the annotation through xmlhttprequest
*/
function submitAnnotationData()
{
	var scope = $('#annotationScope').val();
	var type = $('#annotationClassification').val();
	var msg = $('#annotationValue').val();
	
	logBusinessEvent('REMARK_COMPOSITION', getCurrentItemId(), 'A remark was composed', msg);
	
	jQuery.post('../../index.php/annotation/save',{ scope: scope, type: type, message:msg },function (data, textStatus) {
	 getAnnotationMsgs();
	}
	);
}
/**
* retrieve annotations from the server and display them
**/
function getAnnotationMsgs()
{
	jQuery.get('../../index.php/Annotation/getAnnotations',{ activityUri: activityUri},function (data, textStatus) {
	
	 $('#messages').empty();
	 displayAnnotationMsgs(eval(data));
	}
	);
}

/**
* Displaw the list of annotation messages for this activity retrieved from the server
**/
function displayAnnotationMsgs(annotationsJsArray)
{
	for (var i = 0; i < annotationsJsArray.length; i++)
	{
		$('#messages').append('<hr size="3" /><span class="annotationType">'+annotationsJsArray[i][0]+'</span>&nbsp;<a href="#" onClick="removeAnnotation('+annotationsJsArray[i][2]+')"><img src="../../views/PIAAC/img/application_delete.png" /></a>');	
		$('#messages').append('<div class="annotationContent"><p>'+annotationsJsArray[i][1]+'</p></div>');	
	}
	
	$('#annotationValue').focus();
}

/**
* this function is called by the hyperview document if the shortcut is detected. It simply trig a click on the link.
*/
function displayAnnotateBox()
{
	//todo creata a js function to pop up the dialog box ... 
	annotate(classification,resources,window.activeResources);
}

/**
* removes a previously made annotation
**/

function removeAnnotation(tripleId)
{
	jQuery.post('../../index.php/annotation/remove',{ tripleId: tripleId },function (data, textStatus) {
	getAnnotationMsgs();
	}
	);
}

/**
* export all annotations, !! this function should normally be accessible through web service or ... just for debug
**/

function exportAnnotations()
{	
	jQuery.get('../../index.php/annotation/export',{},function (data, textStatus) {
	alert(data);
	}
	);
}
/**
* gives the focus to the main textbox of annotationBox
*/
function focusDialog()
{
	$('#annotationValue').focus();
	
}

function showCalendar()
{
	logBusinessEvent('OPEN_CALENDAR', getCurrentItemId(), 'Calendar opened');
	
	var buttons = new Object();
	
	$('#calendar').html('<iframe frameborder="0" id="calendarFrame" src="../../../../iservices/CAPICalendar/CapiCalendar.php?hcoUri='+window.hyperObject+'&processUri='+window.processUri+'&lg='+window.uiLanguage+'"></iframe>');
	

	buttons[I18n.__("Close (Esc)")] = function () { $('#calendar').dialog('destroy'); };

	$('#calendar').taoqualDialog({
		buttons: buttons,
		draggable:true,
		modal: true,
		position:'center',
		resizable:false,
		width:760,
		height: 305,
		overlay: {'background-color':'#000', opacity: '0.3'},
		keyboard: function(e) { if (e.keyCode != 27) { e.stopPropagation(); e.preventDefault(); }},
		close: function () { $('#xul_question').get(0).contentWindow.focusNode(null, true, true);}
	});
	
	$('#calendar').css('display', 'block');
}

function showWatch()
{	
	if (!$('ui-dialog-overlay').length)
	{
	$('#watch').html('<IFRAME width="100%" height="93%" src="../../index.php/watch/getData/?processUri='+window.processUri+'&intervieweeUri='+window.intervieweeUri+'" />');
	var buttons = new Object();
	buttons[I18n.__("Close (Esc)")] = function () { $('#watch').dialog('destroy'); };
	$('#watch').taoqualDialog({
		buttons: buttons,
		draggable:true,
		modal: true,
		position:'center',
		resizable:false,
		width:570,
		height: 470,
		overlay: {'background-color':'#000', opacity: '0.3'},
		keyboard: function(e) { if (e.keyCode != 27) { e.stopPropagation(); e.preventDefault(); }},
		close: function () { $('#xul_question').get(0).contentWindow.focusNode(null, true, true);}
	});
	//jQuery.get('../../index.php/watch/getData',{processUri : window.processUri, intervieweeUri : window.intervieweeUri},
	//	function (data, textStatus)
	//	{
			$('#watch').css('display', 'block');
	//		$('#watch').append(data);
	//	});
	}
}

function showTesting()
{	
	
	$('#testing').html('<IFRAME width="100%" height="90%" src="../../index.php/testing/getData/?processUri='+window.processUri+'&intervieweeUri='+window.intervieweeUri+'" />');
	var buttons = new Object();
	buttons[I18n.__("Close (Esc)")] = function () { $('#testing').dialog('destroy'); };
	$('#testing').taoqualDialog({
		buttons: buttons,
		draggable:true,
		modal: true,
		position:'center',
		resizable:false,
		width:570,
		height: 470,
		overlay: {'background-color':'#000', opacity: '0.3'},
		keyboard: function(e) { if (e.keyCode != 27) { e.stopPropagation(); e.preventDefault(); }},
		close: function () { $('#xul_question').get(0).contentWindow.focusNode(null, true, true);}
	});
	//jQuery.get('../../index.php/watch/getData',{processUri : window.processUri, intervieweeUri : window.intervieweeUri},
	//	function (data, textStatus)
	//	{
			$('#testing').css('display', 'block');
	//		$('#watch').append(data);
	//	});
}

function switchLanguage()
{
	window.location.href = '../../../taoqual/plugins/UserFrontend/index.php/preferences/nextServiceContentLanguage?from=' + window.processUri;
}

function isHyperViewInTransaction()
{
	return $('#xul_question').get(0).contentWindow.isInTransaction;	
}

function bindGUILogs()
{
	$('#next,#back,#next_floating,#back_floating,#toggle_language,#pause,#logout,#remarkAction,#calendarAction').bind('click', function(e) {	
		//logEvent(e, e.target.id + ' has been pressed using a mouse click');
	});
}

function getCurrentQuestionCode()
{
	return $('#xul_question').get(0).contentWindow.getFocusedNodeCode();	
}

function logEvent(e, desc, targetOverload)
{
	// Invoke the PIAAC Event Logger.
	//trigEvent(e, window.intervieweeUri, 'BQ_UI', desc, targetOverload);
}

function logBusinessEvent(type, context, comm, value, callback)
{
	if (!comm)
	{
		comm = '';
	}
	
	if (!value)
	{
		value = '';
	}
	
	date = new Date();
	trigBusinessEvent('BQ_UI', window.intervieweeUri, type, context, comm, Math.round(date.getTime() / 1000), value, callback);
}

function getCurrentItemId()
{
	return $('#xul_question').get(0).contentWindow.getCurrentItemId();
}

function disableConsistencyButtons()
{
	$('.ui-dialog-buttonpane > button').attr('disabled', true);
}