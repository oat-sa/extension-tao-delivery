alert("activity diagram loaded");

ActivityDiagramClass = [];
ActivityDiagramClass.canvas = "#process_diagram_container";
ActivityDiagramClass.defaultActivityLabel = "Activity";
ActivityDiagramClass.activities = [];
ActivityDiagramClass.arrows = [];


ActivityDiagramClass.feedActivities = function(activityData, positionData){
	
	
	//buiild the model here:
	
	//activityData sent by treeservice:
	activities = activityData.children;
	
	for(var i=0; i<activities.length; i++){
		activity = activities[i];
		if(activity.attributes.id){
			var activityId = ActivityDiagramClass.getIdFromUri(activity.attributes.id);
			//search in the coordinate list, if coordinate exist
			
			//if not, generate one:
			var position = {top:0, left:0};
			
			//save coordinate in the object:
			ActivityDiagramClass.activities[activityId].position = position;
			ActivityDiagramClass.activities[activityId].label = activity.attributes.data;
			
			//draw activities:
			
		
			//manage the links:
			ActivityDiagramClass.feedLinks();
		}
	}
	
	// activity+related connectors
	
	//arrows:
	
}

ActivityDiagramClass.feedLinks = function(connectorData, linkData){
	//find recursively all connectors and create the associated arrows:
	
	var connectorId = connectorData.attributes.id;
	var origineId = ActivityDiagramClass.getActivityId('connector', connectorId, 'bottom');
	
	//check if arrow exists:
	if(linkData[origineId]){
		//check if destination is correct:
		if(linkData[origineId].end == 'the destination found in tree'){
			//ok
		}
		//if so find the flex, and type value of the related arrow
		if(linkData[origineId].type){
			//the type is defined, so can determine the destination id:
			var endId = ActivityDiagramClass.getActivityId('connector', connectorId, type);
		}
		
		ActivityDiagramClass.arrows[origineId] = '??';
		
		//create arrow here:
	}
	
	//check if the connector has another connector:
	if(connectorData.children){
		for(var i=0;i<connectorData.children.length; i++){
			var nextConnectorData = connectorData.children[i];
			if(nextConnectorData.attributes.id && nextConnectorData.attributes.class=='node-connector'){
				ActivityDiagramClass.feedLinks(nextConnectorData, linkData);
			}
		}
	}
	
	
}

ActivityDiagramClass.getIdFromUri = function(uri){
	var returnValue = 'invalidUri';

	var startIndex = uri.lastIndexOf('%23');//look for the encoded "#" in the uri
	if(startIndex>0){
		returnValue = uri.substr(startIndex+3);
	}
	return returnValue;
}

ActivityDiagramClass.getActivityId = function(targetType, id, position, port){
	
	var prefix = '';
	var body = id;
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
		case 'activityLabel':{
			prefix = 'activityLabel';
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
	
	if(position){
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
				if(processUtil.isset(port)){
					suffix += '_port_'+port;
				}
				//port 1, 2, 3... next, then, else
				break;
			}
			case '':{
				suffix = '';
				break;
			}
			default:
				suffix = '';
		}
	}
	
	returnValue = prefix+'_'+body+suffix;
	return returnValue;
}



ActivityDiagramClass.drawActivity  = function (activityId, position, activityLabel){
	
	if(!ActivityDiagramClass.canvas){
		return false
	}
	
	if(position){
		pos = position;
	}else if(ActivityDiagramClass.activities[activityId].position){
		pos = ActivityDiagramClass.activities[activityId].position;
	}else{
		throw 'no position specified';
		//or default position {0, 0}???
	}
	
	if(!isset(pos.left)){
		left = 0;
	}else{
		left = pos.left;
	}
	if(!isset(pos.top)){
		top = 0;
	}else{
		top = pos.top;
	}
	
	//elementActivityContainer:
	// var containerId = activityId+'_container';
	var containerId = ActivityDiagramClass.getActivityId('container', activityId);
	var elementContainer = $('<div id="'+containerId+'"></div>');
	elementContainer.css('position', 'absolute');
	elementContainer.css('left', Math.round(left)+'px');
	elementContainer.css('top', Math.round(top)+'px');
	elementContainer.addClass(activityId);
	elementContainer.appendTo(ActivityDiagramClass.canvas);
	
	//elementActivity
	var elementActivityId = ActivityDiagramClass.getActivityId('activity', activityId);
	var elementActivity = $('<div id="'+elementActivityId+'"></div>');
	elementActivity.addClass('diagram_activity');
	elementActivity.addClass(elementActivityId);
	elementActivity.appendTo('#'+containerId);
	$('#'+elementActivity.attr('id')).position({
		my: "center top",
		at: "center top",
		of: '#'+containerId
	});
	
	
	
	
	//element activity label:
	var label = '';
	if(activityLabel){
		label = activityLabel;
	}else if(ActivityDiagramClass.defaultActivityLabel){
		label = ActivityDiagramClass.defaultActivityLabel;
	}else{
		label = 'Act';
	}
	
	var elementLabelId = ActivityDiagramClass.getActivityId('activityLabel', activityId);
	var elementLabel = $('<div id="'+elementLabelId+'"></div>');
	elementLabel.text(label);
	elementLabel.addClass('diagram_activity_label');
	elementLabel.addClass(elementActivityId);
	elementLabel.appendTo('#'+elementActivityId);
	$('#'+elementLabel.attr('id')).position({
		my: "center center",
		at: "center center",
		of: '#'+elementActivityId
	});
	var inputBox = ModeActivityLabel.createLabelTextbox(activityId);
	inputBox.blur(function(){
		ModeActivityLabel.destroyLabelTextbox(activityId);
	});
	
	//add "border points" to the activity
	var positions = ['top', 'right', 'left', 'bottom'];
	for(var i in positions){
		ActivityDiagramClass.setBorderPoint(activityId, 'activity', positions[i]);
	}
	
	
	//if it is not a terminal activity, element connector, according to the type:
	//elementLink:
	var elementLinkId = ActivityDiagramClass.getActivityId('link', activityId, '');
	var elementLink = $('<div id="'+elementLinkId+'"></div>');//put connector id here instead
	elementLink.addClass('diagram_link');
	elementLink.addClass(elementActivityId);
	elementLink.appendTo('#'+containerId);
	$('#'+elementLink.attr('id')).position({
		my: "center top",
		at: "center bottom",
		of: '#'+elementActivityId
	});
	
	var connectorId = 'connectId';
	/*
	var elementConnectorId = ActivityDiagramClass.getActivityId('connector', activityId, '');
	var elementConnector = $('<div id="'+elementConnectorId+'"></div>');//put connector id here instead
	elementConnector.addClass('diagram_connector');
	elementConnector.addClass(elementActivityId);
	elementConnector.appendTo('#'+containerId);
	$('#'+elementConnector.attr('id')).position({
		my: "center top",
		at: "center bottom",
		of: '#'+elementLinkId
	});
	
	
	for(var i in positions){
		ActivityDiagramClass.setBorderPoint(connectorId, 'connector', positions[i]);
	}*/
	
	ActivityDiagramClass.drawConnector(connectorId, 'activity', 'split', activityId);
	
	//event onlick:
	$('#'+containerId).click(function(){
		//create menu here:
		
		// getDraggableActivity($(this).attr('id'));
		
		//modeActivityMoveOn/Off(activityId)
		//modeActivityMenuOn/Off()
		//modeArrowMenuOn
		//modeArrowFlexOn
		//modeArrowDestionationOn
		//modeMultiMove
	});
	
}

ActivityDiagramClass.drawConnector = function(connectorId, position, type, prevActivityId){
	
	var portNumber =0;
	var className = '';
	
	switch(type){
		case 'sequence':{
			portNumber = 1;
			className = 'connector_sequence';
			break;
		}
		case 'split':{
			portNumber = 2;
			className = 'connector_split';
			break;
		}
		case 'parallel':{
			portNumber = 3;
			className = 'connector_parallel';
			break;
		}
		case 'join':{
			portNumber = 1;
			className = 'connector_join';
			break;
		}
		default:
			return false;
	}
	
	//define id:
	
	var elementActivityId = ActivityDiagramClass.getActivityId('activity', prevActivityId);
	var elementConnectorId = ActivityDiagramClass.getActivityId('connector', connectorId);
	
	var elementConnector = $('<div id="'+elementConnectorId+'"></div>');//put connector id here instead
	elementConnector.addClass('diagram_connector');
	elementConnector.addClass(className);
	elementConnector.addClass(elementActivityId);
	
	if(position == 'activity'){
		//connect to the activity as the first connector
		elementConnector.appendTo('#'+ActivityDiagramClass.getActivityId('container', prevActivityId));//containerId
		$('#'+elementConnector.attr('id')).position({
			my: "center top",
			at: "center bottom",
			of: '#'+ActivityDiagramClass.getActivityId('link', prevActivityId)
		});
	}else{
		//position according to
		elementConnector.css('position', 'absolute');
		elementConnector.css('left', Math.round(position.left)+'px');
		elementConnector.css('top', Math.round(position.top)+'px');
		//add directly to canvas:
		elementConnector.appendTo(ActivityDiagramClass.canvas);
	}
	
	//add "border points" to the activity
	var positions = ['top', 'right', 'left'];
	for(var i in positions){
		ActivityDiagramClass.setBorderPoint(connectorId, 'connector', positions[i]);
	}
	
	
	var width = elementConnector.width();
	
	var offsetStart = 0;
	var offsetStep = 0;
	if(portNumber%2){
		//odd:
		offsetStep = width/portNumber;
		offsetStart = offsetStep/2;
	}else{
		//even:
		offsetStep = width/(portNumber+1);
		offsetStart = offsetStep;
	}
	
	//debug:
	/*
	console.log('width', width);
	console.log('portNumber', portNumber);
	console.log('offsetStart', offsetStart);
	console.log('offsetStep', offsetStep);
	*/
	
	//set the border points:
	for(i=0; i<portNumber; i++){
		ActivityDiagramClass.setBorderPoint(connectorId, 'connector', 'bottom', Math.round(offsetStart+i*offsetStep), i);
	}
	
	
}

ActivityDiagramClass.setBorderPoint = function(targetId, type, position, offset, port){
	
	// console.log("pos", position);
	// console.log('util',processUtil);
	var portSet = null;
	var offsetSet = 0;
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
			
			//manage case of multi port on the bottom:
			if(processUtil.isset(offset) && processUtil.isset(port)){
				at = 'left bottom';
				offsetSet = offset+' 0';
				portSet = port;
			}else{
				at = 'center bottom';
				
			}
			break;
		}
		default:
			return false;
	}
	
	if(type != 'activity' && type != 'connector'){
		return false;
	}
	
	
	// console.log('offset', offsetSet);
	
	// var activityId = 'prev activity id of the connector';
	// var containerId = ActivityDiagramClass.getActivityId('container', activityId, '');	
	//OR:
	var containerId = ActivityDiagramClass.getActivityId(type, targetId);	//which add the point to the element
	
	var pointId = ActivityDiagramClass.getActivityId(type, targetId, pos, portSet);
	var elementPoint = $('<div id="'+pointId+'"></div>');//put connector id here instead
	elementPoint.addClass('diagram_activity_border_point');
	elementPoint.appendTo('#'+containerId);
	$('#'+pointId).position({
		my: my,
		at: at,
		of: '#'+containerId,
		offset: offsetSet
	});
}

/*


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
*/