alert('ModeArrowEdit loaded');

ModeArrowEdit = new Object();
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

ModeArrowEdit.deleteArrow = function(arrowId){
	//connector only:
	if(arrowId.substr(0,10) == 'connector_'){//length=10
		var index = arrowId.lastIndexOf('_pos_bottom_port_');//length=17
		var indexEnd = arrowId.lastIndexOf('_tip');
		var connectorId = arrowId.substring(10, index);
		var portId = arrowId.substring(index+17, indexEnd);
		//edit connector:
		ActivityDiagramClass.editConnector(connectorId, portId, 'delete', 0);//leave the default value of "value" to trigger the deletion
		ActivityDiagramClass.saveConnector(connectorId);
		
		//delete arrow:
		ArrowClass.removeArrow(arrowId);
		
		//return to default mode
		ModeController.setMode('ModeInitial');
	}
	
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
			ModeArrowEdit.deleteArrow(arrowId);
		},
		autoclose: true
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
			var position = ActivityDiagramClass.getActualPosition(targetElt);
			
			//hide actual arrow:
			ArrowClass.removeArrow(originId, false);
			
			//create temporary draggable arrow:
			var created = ModeArrowLink.createDraggableTempArrow(
				originId, 
				{
					left: targetOffset.left-canvasOffset.left + ActivityDiagramClass.scrollLeft, 
					top: targetOffset.top-canvasOffset.top + ActivityDiagramClass.scrollTop
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
			if(ArrowClass.tempArrows[connectorId].actualTarget){
				ArrowClass.saveTemporaryArrowToReal(connectorId);
			}
		}
	}
	
	ModeActivityMenu.removeAllMenu();
	ModeArrowEdit.tempId = 'emptied';
	ActivityDiagramClass.saveDiagram();
	return true;
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
