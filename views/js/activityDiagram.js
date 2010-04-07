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
	var containerId = activityId+'_container';
	var elementContainer = $('<div id="'+containerId+'"></div>');
	elementContainer.css('position', 'absolute');
	elementContainer.css('left', Math.round(left)+'px');
	elementContainer.css('top', Math.round(top)+'px');
	elementContainer.addClass(activityId);
	elementContainer.appendTo(canvas);
	
	
	//elementActivity
	
	var elementActivity = $('<div id="'+activityId+'"></div>');
	elementActivity.addClass('diagram_activity');
	elementActivity.addClass(activityId);
	// var borderStr = '1px '+'solid'+' '+'black';
	// elementActivity.css('border', borderStr);
	// elementActivity.css('width', '120px');
	// elementActivity.css('height', '50px');
	elementActivity.appendTo('#'+containerId);
	// console.log(elementActivity.attr(id));
	$('#'+elementActivity.attr('id')).position({
		my: "center top",
		at: "center top",
		of: "#"+activityId+'_container'
	});
	
	
	//elementLink:
	var elementLink = $('<div id="'+activityId+'_link"></div>');//put connector id here instead
	elementLink.addClass('diagram_link');
	// var borderStr = '1px '+'solid'+' '+'black';
	// elementLink.css('border', borderStr);
	// elementLink.css('width', '1px');
	// elementLink.css('height', '30px');
	elementLink.addClass(activityId);
	elementLink.appendTo('#'+containerId);
	$('#'+elementLink.attr('id')).position({
		my: "center top",
		at: "center bottom",
		of: '#'+elementActivity.attr('id')
	});
	
	//if is not a terminal activity, element connector:
	var elementConnector = $('<div id="'+activityId+'_c"></div>');//put connector id here instead
	elementConnector.addClass('diagram_connector');
	// var borderStr = '1px '+'solid'+' '+'black';
	// elementConnector.css('border', borderStr);
	// elementConnector.css('width', '30px');
	// elementConnector.css('height', '30px');
	elementConnector.addClass(activityId);
	elementConnector.appendTo('#'+containerId);
	$('#'+elementConnector.attr('id')).position({
		my: "center top",
		at: "center bottom",
		of: '#'+elementLink.attr('id')
	});
	
	//event onlick:
	$('#'+containerId).click(function(){
		getDraggableActivity($(this).attr('id'));
	});
	
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

function createDroppablePoint(targetId, targetType, position){
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
		default:
			return false;
	}
	
	var containerId = targetId+'_container';
	switch(targetType){
		case 'activity':{
			targetId = targetId;
			break;
		}
		case 'connector':{
			targetId = targetId+'_c';//put the connector's real id???
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
		drop: function(event, ui) {
			// console.dir(ui);
			
			var id = $(this).attr('id');
			if(id.indexOf('_c')>0){ 
				return false;
			}else{
				var startIndex = id.indexOf('_pos_');
				var newType = id.substr(startIndex+5); 
				var draggableId = ui.draggable.attr('id');
				var arrowName = draggableId.substring(0,draggableId.indexOf('_tip'));
				console.log(arrowName);
				editArrowType(arrowName, newType);
				calculateArrow($('#'+arrowName), $('#'+draggableId));
				//draw new arrow
				removeArrow(arrowName, false);
				drawArrow(arrowName, {
					container: canvas,
					arrowWidth: 1
				});
				
				//edit the arrow's 'end' property value and set it to this draggable, so moving the activity will make the update in position of the connected arrows easier
				
				//destroy draggable
				
				//destroy ALL droppable object on the canvas
				$(this).droppable('destroy');
			}
		}
	});
	
}