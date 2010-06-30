// alert('ModeArrowEdit loaded');

ModeArrowEdit = [];
ModeArrowEdit.tempId = '';

ModeArrowEdit.on = function(options){

	var arrowId = options.arrowId;
	if(!arrowId){
		return false;
	}
	
	if(ModeArrowEdit.tempId){
		//an arrow is beeing edited:
	}
	
	ModeArrowEdit.tempId = arrowId;
	
	//create the menu delete:
	ModeArrowEdit.createArrowMenu(arrowId);
	
	//activate droppable points of the target object:
	var arrow = ArrowClass.arrows[arrowId];
	if(arrow.target && arrow.targetObject){
		var targetType = '';
		// console.log(arrow.target, arrow.target.indexOf('connector_'));
		if(arrow.target.indexOf('activity_')==0){
			targetType = 'activity';
		}else if(arrow.target.indexOf('connector_')==0){
			targetType = 'connector';
		}
		if(targetType != ''){
			ModeArrowLink.activateActivityDroppablePoints(targetType, arrow.targetObject);
		}else{
			return false;
		}
	}
	
	ModeArrowEdit.createDraggableTempArrow(arrowId);
	
	return true;
}

ModeArrowEdit.createArrowMenu = function(arrowId){
	//create top menu for the activity: first, last, edit, delete
	var containerId = ActivityDiagramClass.getActivityId('activity', activityId);
	actions = [];
	actions.push({
		label: "Delete",
		icon: img_url + "delete.png",
		action: function(arrowId){
			console.log('delete => ',arrowId);
		},
		autoclose: false
	});
	
	// console.log('actions',actions);
	
	ModeActivityMenu.createMenu(
		arrowId,
		arrowId,
		'bottom',
		actions,
		{offset:-15}
	);
	// ModeActivityMenu.existingMenu = new Array();
	ModeActivityMenu.existingMenu[arrowId] = arrowId;
	// console.log("created menus:", ModeActivityMenu.existingMenu);
}


//TODO: reverse if not dropped!
ModeArrowEdit.createDraggableTempArrow = function(originId){

	if(!ArrowClass.arrows[originId]){
		return false;
	}
	var arrow = ArrowClass.arrows[originId];
	if(arrow.target){
		var targetElt = $('#'+arrow.target);
		if(targetElt.length){
			var targetOffset = targetElt.offset();
			var canvasOffset = $(ActivityDiagramClass.canvas).offset();
			
			//hide actual arrow:
			ArrowClass.removeArrow(originId, false);
			
			//create temporary draggable arrow:
			var created = ModeArrowLink.createDraggableTempArrow(
				originId, 
				{
					left: targetOffset.left-canvasOffset.left, 
					top:targetOffset.top-canvasOffset.top
				},
				{
					revert: 'invalid',
					arrowType: arrow.type,
					flex:arrow.flex,
					actualTarget: arrow.target
				}
			);
		}else{
			throw 'the target element of the arrow does not exist';
		}
	}else{
		throw 'the arrow '+originId+' does not exist';
	}
	
}

ModeArrowEdit.save = function(){
	
	
	if(ModeArrowEdit.tempId){
		var connectorId = ModeArrowEdit.tempId;
		// save the temporay arrow data into the actual arrows array:
		if(ArrowClass.tempArrows[connectorId]){
			console.log('ModeArrowEdit.save:', 'not implemented yet');
			console.dir(ArrowClass.tempArrows);
			if(ArrowClass.tempArrows[connectorId].actualTarget){
				ArrowClass.saveTemporaryArrowToReal(connectorId);
			}
		}
	}
	
	ModeActivityMenu.removeAllMenu();
	ModeArrowEdit.tempId = 'emptied';
	return true;
	
	//unquote section below when the communication with server is established:
	/*
	
	//send the coordinate + label to server
	//call processAuthoring/addActivity:
	
	//on success, delete the temp activity:
	ModeArrowEdit.cancel();
	
	//draw the real activity:
	positionData = 'positon of temp activity';
	activityData = [];
	newActivity = ActivityDiagramClass.feedActivity = function(activityData, positionData);//no need for array data since it is not connnected yet
	ActivityDiagramClass.drawActivity(newActivity.id);	
	*/
}

ModeArrowEdit.cancel = function(){
	console.log('ModeArrowLink.cancel', ModeArrowEdit);
		
	if(ModeArrowEdit.tempId){
		var connectorId = ModeArrowEdit.tempId;
		
		if(ArrowClass.tempArrows[connectorId]){
			//delete the temp arrows and draw the actual one:
			ModeArrowLink.removeTempArrow(connectorId);
		}
				
		if(ArrowClass.arrows[connectorId]){
			//redraw the original arrow anyway
			ArrowClass.drawArrow(connectorId, {
				container: ActivityDiagramClass.canvas,
				arrowWidth: 2
			});
			
			//important: reset the arrow menu handler on the redrawn activity:
			ActivityDiagramClass.setArrowMenuHandler(connectorId);// == arrowId
		}
	}
	
	ModeActivityMenu.removeAllMenu();
	
	ModeArrowEdit.tempId = 'empty';
	
	return true;
}
