ModeActivityAdd = [];
ModeActivityAdd.tempId = '';

ModeActivityAdd.on = function(){
	//desactivate 'add activity' button(??)
			
	//create an activity temp
	var tempActivity = ActivityDiagramClass.createTempActivity();
	ModeActivityAdd.tempId = tempActivity.id;
	
	//delete the old temp activity(if already drawn):
	ActivityDiagramClass.removeActivity(tempActivity.id);
	
	//draw it:
	ActivityDiagramClass.drawActivity(tempActivity.id);//note: no need the postion and label parameter since the values are already set
	
	//make it draggable (set handler to its container):
	var containerId = ActivityDiagramClass.getActivityId('container', tempActivity.id);
	if(!$('#'+containerId).length){
		throw 'The activity container '+containerId+' do not exists.';
	}
	$('#'+containerId).draggable({
		containment: ActivityDiagramClass.canvas,
		handle: '#'+ActivityDiagramClass.getActivityId('activity', tempActivity.id),
		scroll:true
	});
	
	//make the label editable
	// ModeActivityLabel.createLabelTextbox(tempActivity.id);
	$('#'+ActivityDiagramClass.getActivityId('activityLabel', tempActivity.id)).click(function(){
		var inputBox = ModeActivityLabel.createLabelTextbox(tempActivity.id);
		inputBox.blur(function(){
			ModeActivityLabel.destroyLabelTextbox(tempActivity.id);
		});
	});
	
	return true;
}

ModeActivityAdd.save = function(){
	console.log('ModeActivityAdd.save:', 'not implemented yet');
	
	//unquote section below when the communication with server is established:
	/*
	
	//send the coordinate + label to server
	//call processAuthoring/addActivity:
	
	//on success, delete the temp activity:
	ModeActivityAdd.cancel();
	
	//draw the real activity:
	positionData = 'positon of temp activity';
	activityData = [];
	newActivity = ActivityDiagramClass.feedActivity = function(activityData, positionData);//no need for array data since it is not connnected yet
	ActivityDiagramClass.drawActivity(newActivity.id);	
	*/
}

ModeActivityAdd.cancel = function(){
	// if(ActivityDiagramClass.currentMode == 'ModeActivityAdd'){
		//delete temp
		ActivityDiagramClass.removeActivity(ModeActivityAdd.tempId);
		// ActivityDiagramClass.unsetFeedbackMenu();
	// }
}
