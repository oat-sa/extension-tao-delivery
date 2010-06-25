// alert('ModeConnectorMove loaded');

ModeConnectorMove = [];
ModeConnectorMove.tempId = '';
ModeConnectorMove.originalPosition = [];

ModeConnectorMove.on = function(connectorId){
	//desactivate 'add activity' button(??)
	ActivityDiagramClass.currentMode = 'ModeConnectorMove';
	
	//insert information in the feedback 'div'
	if(!ActivityDiagramClass.setFeedbackMenu('ModeConnectorMove')){
		return false;
	}
	
	//save a temporary object the initial position of the activity in case of cancellation:
	var connector = ActivityDiagramClass.connectors[connectorId];
	ModeConnectorMove.tempId = connectorId;
	ModeConnectorMove.originalPosition = connector.position;
	
	//transform the connector to draggable (with itself as a helper opacity .7)
	var containerId = ActivityDiagramClass.getActivityId('connector', connectorId);
	if(!$('#'+containerId).length){
		throw 'The connector dom element '+containerId+' do not exists.';
	}
	$('#'+containerId).draggable({
		containment: ActivityDiagramClass.canvas,
		scroll: true,
		drag: function(event, ui){
			//ondrag, update all connected arrows:
			
			//arrows that are connected to that connector:
			//get the arrows that are originated from the different ports of that connector
			var tempConnectorId = ModeConnectorMove.tempId;
			var connector = ActivityDiagramClass.connectors[tempConnectorId];
			if(!connector){
				throw 'no connector found for the id:'+tempConnectorId
			};
			
			for(var portId in connector.port){
				if(connector.port[portId]){
					//get the arrow name and update it:
					var arrowId = ActivityDiagramClass.getActivityId('connector',tempConnectorId,'bottom',portId);
					ArrowClass.updateArrow(arrowId);
				}
			}
			
			for(var arrowId in ArrowClass.arrows){
				var arrow = ArrowClass.arrows[arrowId];
				if(arrow.targetObject == tempConnectorId){
					ArrowClass.updateArrow(arrowId);
				}
			}
		}
	});
	
	return true;
}

ModeConnectorMove.save = function(){
	console.log('ModeConnectorMove.save:', 'not implemented yet');
	ModeConnectorMove.cancel();
}

ModeConnectorMove.cancel = function(){

	if(ActivityDiagramClass.currentMode == 'ModeConnectorMove'){
		if(ModeConnectorMove.tempId){
			var containerId = ActivityDiagramClass.getActivityId('connector', ModeConnectorMove.tempId);
			if(!$('#'+containerId).length){
				throw 'The connector dom element '+containerId+' do not exists.';
			}
			$('#'+containerId).draggable('destroy');
		}
	}
	
}
