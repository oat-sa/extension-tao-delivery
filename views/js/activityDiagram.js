// alert("ok");

function drawActivity(activityId, position){
	
	if(!isset(position.left)){
		left = 0;
	}else{
		left = position.left;
	}
	if(!isset(position.top)){
		top = 0;
	}else{
		top = position.top;
	}
	
	
	//elementActivityContainer:
	// var containerId = activityId+'_container';
	var containerId = getActivityId('container', activityId, '');
	var elementContainer = $('<div id="'+containerId+'"></div>');
	elementContainer.css('position', 'absolute');
	elementContainer.css('left', Math.round(left)+'px');
	elementContainer.css('top', Math.round(top)+'px');
	elementContainer.addClass(activityId);
	elementContainer.appendTo(canvas);
	
	
	//elementActivity
	var elementActivityId = getActivityId('activity', activityId, '');
	var elementActivity = $('<div id="'+elementActivityId+'"></div>');
	elementActivity.addClass('diagram_activity');
	elementActivity.addClass(elementActivityId);
	elementActivity.appendTo('#'+containerId);
	// console.log(elementActivity.attr(id));
	$('#'+elementActivity.attr('id')).position({
		my: "center top",
		at: "center top",
		of: "#"+activityId+'_container'
	});
	
	var positions = ['top', 'right', 'left', 'bottom'];
	
	//add "border points" to the activity
	for(var position in positions){
		setBorderPoint(activityId, type, position);
	}
	
	//if is not a terminal activity, element connector, according to the type:
	//elementLink:
	var elementLinkId = getActivityId('link', activityId, '');
	var elementLink = $('<div id="'+elementLinkId+'"></div>');//put connector id here instead
	elementLink.addClass('diagram_link');
	elementLink.addClass(elementActivityId);
	elementLink.appendTo('#'+containerId);
	$('#'+elementLink.attr('id')).position({
		my: "center top",
		at: "center bottom",
		of: '#'+elementActivity.attr('id')
	});
	
	
	var elementConnectorId = getActivityId('connector', activityId, '');
	var elementConnector = $('<div id="'+elementConnectorId+'"></div>');//put connector id here instead
	elementConnector.addClass('diagram_connector');
	elementConnector.addClass(elementActivityId);
	elementConnector.appendTo('#'+containerId);
	$('#'+elementConnector.attr('id')).position({
		my: "center top",
		at: "center bottom",
		of: '#'+elementLink.attr('id')
	});
	
	var connectorId = 'the connector id of the activity';
	for(var position in positions){
		setBorderPoint(connectorId, type, position);
	}
	
	//event onlick:
	$('#'+containerId).click(function(){
		//create menu here:
		
		getDraggableActivity($(this).attr('id'));
		
		//modeActivityMoveOn/Off(activityId)
		//modeActivityMenuOn/Off()
		//modeArrowMenuOn
		//modeArrowFlexOn
		//modeArrowDestionationOn
		//modeMultiMove
	});
	
}

function drawConnector(connectorId, position, type){

}

function getConnectedArrows(id){
	//search all arrows connecterd to a given activity or connecotor

}

function getDraggableActivity(containerId){//use activity id instead
	$('#'+containerId).draggable({
		containment: canvas
	});
	
	//create droppable object
	
}

function createDroppablePoints(activityId){
	//on top, left and right of activity div and on left and right of a connector:
	
	createDroppablePoint(activityId, 'activity', 'top');
	createDroppablePoint(activityId, 'activity', 'left');
	
	createDroppablePoint(activityId, 'activity', 'right');
	
	//add droppable elements to the connector:
	//get real connector id?
	createDroppablePoint(activityId, 'connector', 'left');
	createDroppablePoint(activityId, 'connector', 'right');
}

function setBorderPoints(activityId){
	//on top, left and right of activity div and on left and right of a connector:
	var types = ['activity', 'connector'];
	var positions = ['top', 'right', 'left', 'bottom'];
	
	//add "border points" to the activity
	for(var position in position){
		setBorderPoint(activityId, type, position);
	}
	
	//and its related connector:
	connectorId = 'the connector id of the activity';
	for(var position in position){
		setBorderPoint(connectorId, type, position);
	}
}

function setDroppablePoints(activityId){
	var types = ['activity', 'connector'];
	var positions = ['top', 'right', 'left'];
	for(var type in types){
		for(var position in position){
			// setDroppablePoint function here:
			
			//cjhange their class also
		}
	}
}


function setBorderPoint(id, type, position){
	switch(position){
		case 'left':{
			pos = 'left';
			my = 'right center';
			at = 'left center';
			break;
		}
		case 'right':{
			pos = 'right';
			my = 'left center';
			at = 'right center';
			break;
		}
		case 'top':{
			pos = 'top';
			my = 'center bottom';
			at = 'center top';
			break;
		}
		case 'bottom':{
			pos = 'bottom';
			my = 'center top';
			at = 'center bottom';
			//manage case of multi port on the bottom:
			
			break;
		}
		default:
			return false;
	}
	
	if(type != 'activity' || type != 'connector'){
		return false;
	}
	
	activityId = 'prev activity id of the connector';
	var containerId = getActivityId('container', activityId, '');	
	//OR:
	var containerId = getActivityId(type, argetId,, '');	//which add the point to the element
	
	var pointId = getActivityId(type, argetId, pos);
	var elementPoint = $('<div id="'+pointId+'"></div>');//put connector id here instead
	elementPoint.addClass('diagram_activity_border_point');
	elementPoint.appendTo('#'+containerId);
	$('#'+elementPoint.attr('id')).position({
		my: my,
		at: at,
		of: '#'+targetId
	});
}

function createDroppablePoint(targetId, position){
	switch(position){
		case 'left':{
			pos = 'left';
			my = 'right center';
			at = 'left center';
			break;
		}
		case 'right':{
			pos = 'right';
			my = 'left center';
			at = 'right center';
			break;
		}
		case 'top':{
			pos = 'top';
			my = 'center bottom';
			at = 'center top';
			break;
		}
		case 'bottom':{
			pos = 'bottom';
			my = 'center top';
			at = 'center bottom';
			//manage case of multi port on the bottom:
			
			break;
		}
		default:
			return false;
	}
	
	var droppableId = targetId+'_pos_'+pos;
	var elementDroppable = $('<div id="'+droppableId+'"></div>');//put connector id here instead
	elementDroppable.addClass('diagram_activity_droppable');
	elementDroppable.appendTo('#'+containerId);
	$('#'+elementDroppable.attr('id')).position({
		my: my,
		at: at,
		of: '#'+targetId
	});
	
	$('#'+droppableId).droppable({
		over: function(event, ui) {
			// console.dir(ui);
			
			var id = $(this).attr('id');
			if(id.indexOf('_c')>0){ 
				return false;
			}else{
				var startIndex = id.indexOf('_pos_');
				var newType = id.substr(startIndex+5); 
				var draggableId = ui.draggable.attr('id');
				var arrowName = draggableId.substring(0,draggableId.indexOf('_tip'));
				// console.log(arrowName);
				editArrowType(arrowName, newType);
				calculateArrow($('#'+arrowName), $('#'+draggableId));
				//draw new arrow
				removeArrow(arrowName, false);
				drawArrow(arrowName, {
					container: canvas,
					arrowWidth: 1
				});
				
			}
		},
		drop: function(event, ui) {
			
			//edit the arrow's 'end' property value and set it to this draggable, so moving the activity will make the update in position of the connected arrows easier
				
				//destroy draggable
				
				//destroy ALL droppable object on the canvas
				// $(this).droppable('destroy');
		}
	});
	
}

function getIdFromUri(uri){
	var returnValue = '';
	
	if(isset(uri)){
		returnValue = uri.substr(uri.lastIndexOf('%23')+3);
	}
	
	return returnValue;
}

function getActivityId(targetType, id, position, port){
	
	var prefix = '';
	var body = '';
	var suffix = '';
	var returnValue = '';
	
	switch(targetType){
		case 'activity':{
			prefix = 'activity';
			break;
		}
		case 'connector':{
			prefix = 'connector';
			break;
		}
		case 'container':{
			prefix = 'container';
			position = '';
			break;
		}
		case 'link':{
			prefix = 'link';
			position = '';
			break;
		}
		default:
			return returnValue;
	}
	
	switch(position){
		case 'top':{
			suffix = '_pos_top';
			break;
		}
		case 'left':{
			suffix = '_pos_left';
			break;
		}
		case 'right':{
			suffix = '_pos_right';
			break;
		}
		case 'bottom':{
			suffix = '_pos_bottom';
			if(isset(port)){
				suffix += '_'+port;
			}
			//port 1, 2, 3... next, then, else
			break;
		}
		case '':{
			suffix = '';
			break;
		}
		default:
			return returnValue;
	}
	
	returnValue = prefix+'_'+body+suffix;
	return returnValue;
}

function getArrowId(type, name, position){
	
	var body = name;
	var suffix = '';
	var returnValue = '';
	
	switch(type){
		case 'part':{
			position = parseInt(position);
			suffix = '_arrowPart_'+position;
			break;
		}
		case 'handle':{
			position = parseInt(position);
			suffix = '_arrowPart_'+position+'_handle';
			break;
		}
		case 'tip':{
			suffix = '_tip';
			break;
		}
		default:
			return returnValue;
	}
	
	returnValue = body+suffix;
	return returnValue;
}