// alert("modeLinkedActivityAdd loaded");
//class no longer required...

ModeLinkedActivityAdd = [];
ModeLinkedActivityAdd.tempId = '';
ModeLinkedActivityAdd.arrowId = '';

ModeLinkedActivityAdd.on = function(connectorId, portId, position){
	ModeLinkedActivityAdd.tempId = '';
	ModeLinkedActivityAdd.arrowId = '';

	var connectorPortId = ActivityDiagramClass.getActivityId('connector',connectorId,'bottom',portId);
	ModeLinkedActivityAdd.arrowId = connectorPortId;
	
	//insert information in the feedback 'div'
	if(!ActivityDiagramClass.setFeedbackMenu('ModeLinkedActivityAdd')){
		return false;
	}
	
	//create an activity temp
	var tempActivity = ActivityDiagramClass.createTempActivity(position);
	ModeLinkedActivityAdd.tempId = tempActivity.id;
	
	//delete the old temp activity(if already drawn):
	ActivityDiagramClass.removeActivity(tempActivity.id);
	
	//draw it:
	ActivityDiagramClass.drawActivity(tempActivity.id);//note: no need the postion and label parameter since the values are already set
	
	//create a temporary arrow:
	var activityTopBorderPtId = ActivityDiagramClass.getActivityId('activity', tempActivity.id, 'top');
	console.log(connectorPortId, activityTopBorderPtId);
	ArrowClass.tempArrows[connectorPortId] = ArrowClass.calculateArrow($('#'+connectorPortId),$('#'+activityTopBorderPtId), 'top', null, true);
	ArrowClass.drawArrow(connectorPortId, {
		container: ActivityDiagramClass.canvas,
		arrowWidth: 2,
		temp: true
	});
	ArrowClass.getDraggableFlexPoints(connectorPortId);
	
	//make it draggable (set handler to its container):
	var containerId = ActivityDiagramClass.getActivityId('container', tempActivity.id);
	if(!$('#'+containerId).length){
		throw 'The activity container '+containerId+' do not exists.';
	}
	$('#'+containerId).draggable({
		containment: ActivityDiagramClass.canvas,
		handle: '#'+ActivityDiagramClass.getActivityId('activity', tempActivity.id),
		scroll:true,
		drag: function(event, ui) {
			var containerId = $(this).attr('id');
			var activityId = containerId.replace('container_', '')
			var activityTopBorderPtId = ActivityDiagramClass.getActivityId('activity', activityId, 'top');
			//retrieve the arrow object in the temp arrows global array:
			if(ModeLinkedActivityAdd.arrowId){
				var arrow = ArrowClass.tempArrows[ModeLinkedActivityAdd.arrowId];
				//recalculate and redraw it:
				// console.log(ModeLinkedActivityAdd.arrowId, activityTopBorderPtId);
				ArrowClass.tempArrows[ModeLinkedActivityAdd.arrowId] = ArrowClass.calculateArrow($('#'+ModeLinkedActivityAdd.arrowId), $('#'+activityTopBorderPtId), arrow.type, null, true);
				ArrowClass.redrawArrow(ModeLinkedActivityAdd.arrowId, true);
				
			}else{
				throw 'no arrow id found in mode linked activity add';
			}
			
			
			
		}
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



ModeLinkedActivityAdd.cancel = function(){
	if(ActivityDiagramClass.currentMode == 'ModeLinkedActivityAdd'){
		//delete temp
		ActivityDiagramClass.removeActivity(ModeLinkedActivityAdd.tempId);
		ActivityDiagramClass.unsetFeedbackMenu();
		ArrowClass.removeArrow(ModeLinkedActivityAdd.arrowId);
	}
}


ModeLinkedActivityAdd.save = function(){
	console.log('ModeLinkedActivityAdd.save:', 'not implemented yet');
	
	//unquote section below when the communication with server is established:
	/*
	
	//send the coordinate + label to server
	//call processAuthoring/addActivity:
	
	//on success, delete the temp activity:
	ModeLinkedActivityAdd.cancel();
	
	//draw the real activity:
	positionData = 'positon of temp activity';
	activityData = [];
	newActivity = ActivityDiagramClass.feedActivity = function(activityData, positionData);//no need for array data since it is not connnected yet
	ActivityDiagramClass.drawActivity(newActivity.id);	
	*/
}