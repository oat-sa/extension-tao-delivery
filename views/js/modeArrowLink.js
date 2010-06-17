ModeArrowLink = [];
ModeArrowLink.tempId = '';

ModeArrowLink.on = function(connectorId, port, position){
	
	console.log('ModeArrowLink.on');
	var arrowOriginEltId = ActivityDiagramClass.getActivityId('connector', connectorId, 'bottom', port);
	console.log('arrowOriginEltId: ',arrowOriginEltId);
								
	//insert information in the feedback 'div'
	if(!ActivityDiagramClass.setFeedbackMenu('ModeArrowLink')){
		return false;
	}
	
	//reset temp arrow array:
	ArrowClass.tempArrows = [];
	
	//create a temporary arrow
	var tempArrow = ArrowClass.createTempArrow(arrowOriginEltId, position);
	ModeArrowLink.tempId = tempArrow.id;
	
	//set droppable points:
	ActivityDiagramClass.activateAllDroppablePoints(connectorId);
	
	return true;
}

ModeArrowLink.save = function(){
	console.log('ModeArrowLink.save:', 'not implemented yet');
	
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

ModeArrowLink.cancel = function(){
	//delete temp
	ActivityDiagramClass.removeActivity(ModeActivityAdd.tempId);
	ActivityDiagramClass.unsetFeedbackMenu();
}
