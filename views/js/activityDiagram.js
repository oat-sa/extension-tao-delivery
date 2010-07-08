alert("activity diagram Class loaded");

//require arrows.js

ActivityDiagramClass = [];
ActivityDiagramClass.defaultActivityLabel = "Activity";
ActivityDiagramClass.activities = [];
ActivityDiagramClass.connectors = [];
ActivityDiagramClass.feedbackContainer = "#process_diagram_feedback";
ActivityDiagramClass.currentMode = null;
ActivityDiagramClass.scrollLeft = 0;
ActivityDiagramClass.scrollTop = 0;
ActivityDiagramClass.defaultPosition = {top:50, left:30};
ActivityDiagramClass.relatedTree = null;
// ActivityDiagramClass.errors = {
	// activities: [],
	// arrows:[]
// }


//get positions of every activities
ActivityDiagramClass.feedDiagram = function(processData){
	
	
	var positionData = [];
	var arrowData = [];
	
	
	//start of test data//
	// var processData = [];
	// processData.children = [
		// {
			// data: 'Activity N1',
			// attributes:{
				// id: 'NS%23activity1_id'
			// },
			// isInitial: true,
			// children : [
				// {
					// data: 'nulsqdqsl',
					// attributes:{
						// id: 'NS%23nullty1_id'
					// }
				// },
				// {
					// data: 'connector 1',
					// attributes:{
						// id: 'NS%23connector1_id',
						// class: 'node-connector'
					// },
					// children:[
						// {
							// data: 'nevermind',
							// attributes:{
								// rel:'NS%23activity2_id',
								// class:'node-activity-goto'
							// },
							// port: '0'
						// }
					// ],
					// type: 'split'
				// }
			// ]
		// },
		// {
			// data: 'Activity N2',
			// attributes:{
				// id: 'NS%23activity2_id'
			// },
			// isLast: true
		// }
	// ];
	
	// positionData['activity1_id'] = {top: 50, left: 150};
	// positionData['activity2_id'] = {top: 250, left: 200};
	// positionData['connector1_id'] = {top: 150, left: 160};
	
	// origin_connector1 = ActivityDiagramClass.getActivityId('connector', 'connector1_id', 'bottom', '0');//put port='next'??
	// arrowData[origin_connector1] = {targetObject:'activity2_id', type:'top'};
	// arrowData[ActivityDiagramClass.getActivityId('activity', 'activity1_id', 'bottom')] = {targetObject:'connector1_id', type:'top'};
	//end of test data//
	
	//activityData sent by treeservice:
	var activities = processData.children;
	console.dir(activities);
	
	if(processData.diagramData){
		var diagramData = processData.diagramData; 
		if(diagramData.positionData){
			for(var i=0; i<diagramData.positionData.length; i++){
				positionData[diagramData.positionData[i].id] = diagramData.positionData[i];
			}
		}
		if(diagramData.arrowData){
			for(var i=0; i<diagramData.arrowData.length; i++){
				arrowData[diagramData.arrowData[i].id] = diagramData.arrowData[i];
			}
		}
	}
	
	for(var i=0; i<activities.length; i++){
	
		var activityData = activities[i];
		if(!activityData.attributes){
			throw 'the activity data has no attributes';
			continue;
		}else{
			ActivityDiagramClass.feedActivity(activityData, positionData, arrowData);
		}
		
	}
	
	//debug:
	console.log('activities:');console.dir(ActivityDiagramClass.activities);
	console.log('connectors:');console.dir(ActivityDiagramClass.connectors);
	console.log('arrows:');console.dir(ArrowClass.arrows);
}

ActivityDiagramClass.feedActivity = function(activityData, positionData, arrowData){
		
	
	if(activityData.attributes.id){
		
		var activityId = ActivityDiagramClass.getIdFromUri(activityData.attributes.id);
		//search in the coordinate list, if coordinate exist
		
		//if case there is no position:
		var position = ActivityDiagramClass.defaultPosition;//{top:30, left:20};
		if(positionData){
			if(positionData[activityId]){
				position = positionData[activityId];
			}
		}
	
		//save coordinate in the object:
		ActivityDiagramClass.activities[activityId] = [];
		ActivityDiagramClass.activities[activityId].id = activityId;
		ActivityDiagramClass.activities[activityId].position = position;
		if(activityData.data){
			ActivityDiagramClass.activities[activityId].label = activityData.data;
		}
		
		//is first? is last?
		ActivityDiagramClass.activities[activityId].isInitial = false;
		if(activityData.isInitial){
			if(activityData.isInitial == true){
				ActivityDiagramClass.activities[activityId].isInitial = true;
			}
		}
		ActivityDiagramClass.activities[activityId].isLast = true;
		if(activityData.isLast){
			if(activityData.isLast == false){
				ActivityDiagramClass.activities[activityId].isLast = false;
			}
		}
		
		//find the connector of the activity
		var connectorData = null;
		
		if(activityData.children){
			//the activity has ch
			// console.log('act children:');console.dir(activityData.children);
			// console.log('activityData.children.length', activityData.children.length);
			for(var j=0;j<activityData.children.length;j++){
				var child = activityData.children[j];
				if(child.attributes){
					if(child.attributes.class == 'node-connector'){
						connectorData = child;
						break;//note: there can at most only be one connector for an activity
					}
				}
			}
			
			if(connectorData != null){
				
				connector = ActivityDiagramClass.feedConnector(connectorData, positionData, activityId, arrowData);
				
				if(connector == null){
					throw 'connector cannot be created for the activity '+activityId;
				}else{
					//add the arrow between the activity and its first connector:
					console.log('connector', connector);
					var activityBottomBorderPointId = ActivityDiagramClass.getActivityId('activity',activityId,'bottom');
					var connectorTopBorderPointId = ActivityDiagramClass.getActivityId('connector',connector.id,'top');
					if(arrowData){
						if(arrowData[activityBottomBorderPointId]){
							var arrow = arrowData[activityBottomBorderPointId];
							ArrowClass.feedArrow(activityBottomBorderPointId, connectorTopBorderPointId, connector.id, arrow.position, arrow.flex);
						}
					}
					
				}
			
			}
		}
		
		return ActivityDiagramClass.activities[activityId];
		
	}
}

ActivityDiagramClass.reloadDiagram = function(){
	console.log('reload ', ActivityDiagramClass.canvas);

	//empty diagram:
	$(ActivityDiagramClass.canvas).empty();
	
	//load diagram:
	ActivityDiagramClass.loadDiagram();
}


ActivityDiagramClass.loadDiagram = function(){
	//ajax call to get the model
	$.ajax({
		url: authoringControllerPath + 'getActivities',
		type: "POST",
		data: {"processUri": processUri, "diagramData": true},
		dataType: 'json',
		success: function(processData){
			// console.log(response);
			try{
				ActivityDiagramClass.feedDiagram(processData);
				ActivityDiagramClass.drawDiagram();
				
				//initiate the mode to initial:
				ModeController.setMode('ModeInitial');
			}
			catch(err){
				console.log('loading diagram exception', err);
			}
		}
	});
}


ActivityDiagramClass.saveDiagram = function(){
	//get activity and connector coordinate position data:
	var positionData = [];
	for(activityId in ActivityDiagramClass.activities){
		var position = {};
		if(ActivityDiagramClass.activities[activityId].position){
			var position = ActivityDiagramClass.activities[activityId].position;
			positionData.push({
				id: activityId,
				left: position.left,
				top: position.top
			});
		}
	}
	for(connectorId in ActivityDiagramClass.connectors){
		if(ActivityDiagramClass.connectors[connectorId].position){
			var position = ActivityDiagramClass.connectors[connectorId].position;
			positionData.push({
				id: connectorId,
				left: position.left,
				top: position.top
			});
		}
	}
	
	//get transfer arrow data:
	var arrowData = [];
	for(arrowId in ArrowClass.arrows){
		var arrow = ArrowClass.arrows[arrowId];
		// arrowData[arrowId] = {targetObject: 'target'};
		arrowData.push({
			id : arrowId,
			targetObject: arrow.targetObject,
			type: arrow.type,
			flex: arrow.flex
		});
	}
	
	//convert to json string and send it to server
	var data = JSON.stringify({
		arrowData: arrowData,
		positionData: positionData
	});
	
	// console.log('activities', ActivityDiagramClass.activities);
	// console.log('connectors', ActivityDiagramClass.connectors);
	// console.log('arrows', ArrowClass.arrows);
	
	// console.log('positionData', positionData);
	// console.log('arrowData', arrowData);
	// console.log('JSONstring', data);
	
	
	//global processUri value
	$.ajax({
		url: authoringControllerPath + 'saveDiagram',
		type: "POST",
		data: {"processUri": processUri, "data": data},
		dataType: 'json',
		success: function(response){
			// console.log(response);
			if (response.ok){
				console.log('diagram saved');
			}else{
				console.log('error in saving the diagram');
			}
		}
	});
	
}

ActivityDiagramClass.feedConnector = function(connectorData, positionData, prevActivityId, arrowData){

	//find recursively all connectors and create the associated arrows:
	
	if(!connectorData.attributes.id){
		throw 'no connector id found';
		return false;
	}
	if(!prevActivityId){
		throw 'no previous actiivty id found';
		return false;
	}
	
	var connectorId = ActivityDiagramClass.getIdFromUri(connectorData.attributes.id);
	ActivityDiagramClass.connectors[connectorId] = [];
	ActivityDiagramClass.connectors[connectorId].id = connectorId;
	
	//search in the positionData, if coordinate exist
	
	var position = ActivityDiagramClass.getConnectorDefaultPosition(prevActivityId);
	if(positionData){
		if(positionData[connectorId]){
			position = positionData[connectorId];
		}
	}
	
	//save coordinate in the object:
	ActivityDiagramClass.connectors[connectorId].position = position;
	if(connectorData.attributes.data){
		ActivityDiagramClass.connectors[connectorId].label = connectorData.data;
	}
	
	//get connected activities:
	//check type first:
	if(!connectorData.type){
		throw 'no connector type  found in connectorData';
	}
	ActivityDiagramClass.connectors[connectorId].type = connectorData.type;

	ActivityDiagramClass.connectors[connectorId].activityRef = prevActivityId;
	//do not draw connector here, feed them first until everything is fed:
	// ActivityDiagramClass.drawConnector(connectorId, position, type, prevActivityId);
	
	//inti port value:
	ActivityDiagramClass.connectors[connectorId].port = new Array();
	
	//check if the connector has another connector:
	if(connectorData.children){
		
		for(var i=0;i<connectorData.children.length; i++){
			var nextActivityData = connectorData.children[i];
			if(nextActivityData.attributes.id && nextActivityData.attributes.class=='node-connector'){
				//recursively continue with the connector of connector:
				ActivityDiagramClass.feedConnector(nextActivityData, arrowData, prevActivityId, positionData);
			}
			
			//build the arrows (the previous and next connectors must have already been created of course)
			if(nextActivityData.port){
				//check authorized port:
				//note: nextActivityData.port is an int{0,*}
				var originId =  ActivityDiagramClass.getActivityId('connector', connectorId, 'bottom', nextActivityData.port);
				// var originId = connectorData.attributes.id;//set the origin of the arrow as the id of the connector, then let the arrow calculate the real id
				
				
				var nextActivityId = '';
				var targetId = '';
				// console.log("fActivity class", nextActivityData.attributes.class);
				// console.dir(connectorData);
				
				if(nextActivityData.attributes.class == 'node-connector'){
					nextActivityId = ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.id);
				}else if(nextActivityData.attributes.class == 'node-activity-goto' || nextActivityData.attributes.class == 'node-connector-goto'){
					nextActivityId = ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.rel);
				}else{
					// throw 'unknown type of following activity';
					// return false;
					continue;//it could be an 'if' node
				}
				
				// if(nextActivityData.attributes.class == 'node-connector'){
					// nextActivityId = ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.id);
					// targetId =  ActivityDiagramClass.getActivityId('connector', nextActivityId, targetPosition);
				// }else if(nextActivityData.attributes.class == 'node-activity'){
					// nextActivityId = ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.rel);
					// targetId =  ActivityDiagramClass.getActivityId('activity', nextActivityId, targetPosition);
				// }else if(nextActivityData.attributes.class == 'node-connector-goto'){
					// nextActivityId = ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.rel);
					// targetId =  ActivityDiagramClass.getActivityId('connector', nextActivityId, targetPosition);
				// }else{
					// throw 'unknown type of following activity';
				// }
				
				//check the target:
				var onSync = false;
				var flex = null;
				var targetPosition = 'top';//default value
				if(processUtil.isset(arrowData[originId])){
					// console.log('arrowData[originId].targetObject=',arrowData[originId].targetObject);
					// console.log('nextActivityId=', nextActivityId);
					if(arrowData[originId].targetObject == nextActivityId){
						//on sync: prepare to draw the arrow:
						onSync = true;
						
						//get type, get flex:
						if(arrowData[originId].type){
							targetPosition  = arrowData[originId].type;
						}
						if(arrowData[originId].flex){
							flex = arrowData[originId].flex;
						}
					}else{
						//rebuild a new arrow with matching data: do not take into account the saved 'type' and 'flex'
					
					}
				}
				
				if(nextActivityData.attributes.class == 'node-connector' || nextActivityData.attributes.class == 'node-connector-goto'){
					targetId =  ActivityDiagramClass.getActivityId('connector', nextActivityId, targetPosition);
				}else if(nextActivityData.attributes.class == 'node-activity-goto'){
					targetId =  ActivityDiagramClass.getActivityId('activity', nextActivityId, targetPosition);
				}
				
				//the activities must have been drawn before:
				//if type and flex not set, let it as null and take the data of connection and build new arrows with default type and flex value:
				
				ArrowClass.feedArrow(originId, targetId, nextActivityId, targetPosition, flex);
				//note: do not calculate/draw arrow here since the target element is unilikely to have already been build.
				// ArrowClass.calculateArrow($('#'+originId), $('#'+targetId), type, flex);
				
				//add related port
				ActivityDiagramClass.connectors[connectorId].port[nextActivityData.port] = nextActivityId;
			}
			
		}
	}
	return ActivityDiagramClass.connectors[connectorId];
}


ActivityDiagramClass.getConnectorDefaultPosition = function(prevActivityId){
	var position = ActivityDiagramClass.defaultPosition;
	
	//check if the activity has been drawn:
	// var activityElt = $('#'+ActivityDiagramClass.getActivityId('activity',prevActivityId));
	// if(activityElt.length){
		// var activityPos = activityElt.position();
	// }
	
	return position;
}

ActivityDiagramClass.positionNewActivity = function(originContainer, targetContainer, offset){
	
	var offsetValue = "0 35";
	if(offset){
		if(!processUtil.isset(offset.left) && !processUtil.isset(offset.top)){
			offsetValue = parseInt(offset.left)+' '+parseInt(offset.top);
		}
	}
	
	if(!originContainer.length){
		throw 'the container of the origin element does not exist';
	}
	if(!targetContainer.length){
		throw 'the container of the target element does not exist';
	}
	
	//position the new activity or connector container relative to the origin one:
	targetContainer.position({
		my: "center top",
		at: "center bottom",
		of: "#"+originContainer.attr('id'),
		offset: offsetValue
	});
	
	//save the new position of the target:
	var targetEltId = targetContainer.attr('id');
	// var endIndex = targetEltId.indexOf('_pos_');
	var targetId = 'empty';
	var newPosition = ActivityDiagramClass.getActualPosition(targetContainer);//{"left": targetContainer.position().left, "top":targetContainer.position().top};
	if(targetEltId.substring(0,9)=='activity_'){
		targetId = targetEltId.substring(9);
		ActivityDiagramClass.activities[targetId].position = newPosition;
	}else if(targetEltId.substring(0,10)=='connector_'){
		targetId = targetEltId.substring(10);
		ActivityDiagramClass.connectors[targetId].position = newPosition;
	}else{
		throw 'wrong id format for the target element';
	}
	
}

ActivityDiagramClass.getActualPosition = function(targetContainer){
	if(!targetContainer.length){
		throw 'the target element does not exist';
	}
	
	return {
		"left": targetContainer.position().left + ActivityDiagramClass.scrollLeft, 
		"top": targetContainer.position().top + ActivityDiagramClass.scrollTop
	};
}

ActivityDiagramClass.drawDiagram = function(){
	//to be executed after all feeds: activities, connectors, arrows
	//check isfed? array ActivityDiagramClass.activities empty?
	
	// console.log('Activities:');
	// console.log(ActivityDiagramClass.activities.length);
	// if(ActivityDiagramClass.activities.length<=0){
		// throw 'The activities array is empty. Please feed it first.';
		// return false;
	// }
	
	
	//draw all actvities:
	for(activityId in ActivityDiagramClass.activities){
		ActivityDiagramClass.drawActivity(activityId);
		ActivityDiagramClass.setActivityMenuHandler(activityId);
	}
	
	
	// if(ActivityDiagramClass.connectors.length<=0){
		// throw 'The connectors array is empty. Please feed it first.';
		// return false;
	// }
	for(connectorId in ActivityDiagramClass.connectors){
		if(ActivityDiagramClass.connectors[connectorId].position){
			ActivityDiagramClass.drawConnector(connectorId);
			ActivityDiagramClass.setConnectorMenuHandler(connectorId);
			//do not draw the first connector of an activity, only the connector of the connector, since the first one will de drawn with drawActivity
		}
	}
	
	// if(ArrowClass.arrows.length<=0){
		// throw 'The arrows array is empty. Please feed it first.';
		// return false;
	// }
	for(arrowId in ArrowClass.arrows){
		targetId = ArrowClass.arrows[arrowId].target;
		if(arrowId && targetId){
			// console.log('the element do not exists =#', element);
			ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId),$('#'+targetId));
			console.log('calculated arrows:');
			console.dir(ArrowClass.arrows);
			ArrowClass.drawArrow(arrowId, {
				container: ActivityDiagramClass.canvas,
				arrowWidth: 2
			});
			ActivityDiagramClass.setArrowMenuHandler(arrowId);
		}else{
			console.log('arrow cant be drawn:', arrowId);
		}
	}
	
}

ActivityDiagramClass.createTempActivity = function(position){
	
	//delete old one if exists
	var tempActivityId = 'tempActivity';
	ActivityDiagramClass.activities[tempActivityId] = [];
	var tempActivity = ActivityDiagramClass.activities[tempActivityId];
	
	//create it in model
	tempActivity.id = tempActivityId;
	tempActivity.label = ActivityDiagramClass.defaultActivityLabel;
	tempActivity.isIntial = false;
	tempActivity.isLast = true;
	
	if(position){
		tempActivity.position = position;
	}else{
		tempActivity.position = {top:10, left:10};
	}
	// console.log('tempActivity', tempActivity);
	
	return tempActivity;
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
		case 'free':{
			prefix = position;
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
				//port 1, 2, 3... next(''), then, else
				break;
			}
			case '':{
				suffix = '';
				break;
			}
			default:{
				return returnValue;
			}
		}
	}
	
	returnValue = prefix+'_'+body+suffix;
	return returnValue;
}

ActivityDiagramClass.drawActivity  = function (activityId, position, activityLabel){
	
	if(!ActivityDiagramClass.canvas){
		return false
	}
	
	var pos = null;
	if(position){
		pos = position;
	}else if(ActivityDiagramClass.activities[activityId].position){
		pos = ActivityDiagramClass.activities[activityId].position;
	}else{
		throw 'no position specified';
		//or default position {0, 0}???
	}
	
	var leftValue = 0;
	if(pos.left){
		leftValue = pos.left;
	}
	
	var topValue = 0;
	if(pos.top){
		topValue = pos.top;
	}
	
	//elementActivityContainer:
	// var containerId = activityId+'_container';
	var containerId = ActivityDiagramClass.getActivityId('container', activityId);
	var elementContainer = $('<div id="'+containerId+'"></div>');
	elementContainer.css('position', 'absolute');
	elementContainer.css('left', Math.round(leftValue)+'px');
	elementContainer.css('top', Math.round(topValue)+'px');
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
	
	//add "border points" to the activity
	var positions = ['top', 'right', 'left', 'bottom'];
	for(var i in positions){
		ActivityDiagramClass.setBorderPoint(activityId, 'activity', positions[i]);
	}
	
	//element activity label:
	var label = 'Act';
	if(activityLabel){
		label = activityLabel;
	}else if( ActivityDiagramClass.activities[activityId] ){
		if(ActivityDiagramClass.activities[activityId].label){
			label = ActivityDiagramClass.activities[activityId].label;
		}
	}else if(ActivityDiagramClass.defaultActivityLabel){
		label = ActivityDiagramClass.defaultActivityLabel;
	}
	
	var elementLabelId = ActivityDiagramClass.getActivityId('activityLabel', activityId);
	var elementLabel = $('<span id="'+elementLabelId+'"></span>');
	elementLabel.html(label);
	elementLabel.addClass('diagram_activity_label');
	elementLabel.addClass(elementActivityId);
	elementLabel.appendTo('#'+elementActivityId);
	$('#'+elementLabel.attr('id')).position({
		my: "center center",
		at: "center center",
		of: '#'+elementActivityId
	});
		
	//if it is not a terminal activity, element connector, according to the type:
	//if not final activity: final==false && connector exists
	//else (is a final activity: final==true
	
	if(ActivityDiagramClass.activities[activityId]){
		//the activity is defined in the global activity array, so must either be last or have a connector
		
		var hasConnector = false;
		if(ActivityDiagramClass.activities[activityId].isLast == false){
			
		}
		
		if(hasConnector == false){
			//TODO: replace by "last actiivty" class instead
		}
	

		//is first or not?
		if(ActivityDiagramClass.activities[activityId].isInitial == true){
			//TODO: replace by first activity class instead:
			
			//create the link element:
			var elementLinkFirstId = ActivityDiagramClass.getActivityId('free', activityId, 'link_first');
			var elementLinkFirst = $('<div id="'+elementLinkFirstId+'"></div>');//put connector id here instead
			elementLinkFirst.addClass('diagram_link');
			elementLinkFirst.addClass(elementActivityId);
			elementLinkFirst.appendTo('#'+containerId);
			$('#'+elementLinkFirst.attr('id')).position({
				my: "center bottom",
				at: "center top",
				of: '#'+elementActivityId
			});
		
			//consider it to be the last activity: build the end element
			var elementFirstId = ActivityDiagramClass.getActivityId('free', activityId, '_first');
			var elementFirst = $('<div id="'+elementFirstId+'"></div>');//put connector id here instead
			elementFirst.addClass('diagram_activity_first');
			elementFirst.addClass(elementActivityId);
			elementFirst.appendTo('#'+containerId);//containerId
			$('#'+elementFirst.attr('id')).position({
				my: "center bottom",
				at: "center top",
				of: '#'+elementLinkFirstId
			});
		}
	
	}
	
}

ActivityDiagramClass.removeActivity = function(activityId){
	var containerId = ActivityDiagramClass.getActivityId('container', activityId);
	$('#'+containerId).remove();
}

ActivityDiagramClass.removeConnector = function(connectorId){
	var containerId = ActivityDiagramClass.getActivityId('connector', connectorId);
	$('#'+containerId).remove();
}

ActivityDiagramClass.setActivityMenuHandler = function(activityId){
	var containerId = ActivityDiagramClass.getActivityId('activity', activityId);
	if($('#'+containerId).length){
		
		 
		
		$('#'+containerId).bind('click', {id:activityId}, function(event){
			event.preventDefault();
			//should eventually use ActivityDiagramClass.relatedTree instead of 'tree-activity'
			//issue: the tree may not be initiated yet
			ActivityTreeClass.setCurrentNode('tree-activity', ActivityDiagramClass.getActivityUri(activityId));
			ModeController.setMode('ModeActivityMenu', {type:'activity', target: event.data.id});
		});
		
		var activityLabel = ActivityDiagramClass.getActivityId('activityLabel', activityId);
		$('#'+activityLabel).bind('dblclick', {id:activityId}, function(event){
			event.preventDefault();
			ModeController.setMode('ModeActivityLabel', {activityId:event.data.id});
		});
	}
}

ActivityDiagramClass.unsetActivityMenuHandler = function(activityId){
	var containerId = ActivityDiagramClass.getActivityId('activity', activityId);
	if($('#'+containerId).length){
		$('#'+containerId).unbind('click');
	}
}

ActivityDiagramClass.unsetConnectorMenuHandler = function(connectorId){
	var containerId = ActivityDiagramClass.getActivityId('connector', connectorId);
	if($('#'+containerId).length){
		$('#'+containerId).unbind('click');
	}
}

ActivityDiagramClass.setConnectorMenuHandler = function(connectorId){
	var containerId = ActivityDiagramClass.getActivityId('connector', connectorId);
	if($('#'+containerId).length){
		$('#'+containerId).bind('click', {id:connectorId}, function(event){
			event.preventDefault();
			ModeController.setMode('ModeActivityMenu', {type:'connector', target: event.data.id});
		});
	}
}

ActivityDiagramClass.setArrowMenuHandler = function(arrowId){
	if(ArrowClass.arrows[arrowId]){
		$('.arrow.'+arrowId).click(function(){
			ModeController.setMode('ModeArrowEdit', {arrowId: arrowId});
			// ModeArrowEdit.on(arrowId);
		});
	}
}


ActivityDiagramClass.drawConnector = function(connectorId, position, connectorType, previousActivityId){
	
	if(!ActivityDiagramClass.canvas){
		throw 'no canvas defined';
		return false
	}
	
	var pos = '';
	if(position){
		pos = position;
	}else if(ActivityDiagramClass.connectors[connectorId].position){
		pos = ActivityDiagramClass.connectors[connectorId].position;
	}else{
		throw 'no position found';
		//or default position {0, 0}???
	}
	
	var type = '';
	if(connectorType){
		type = connectorType;
	}else if(ActivityDiagramClass.connectors[connectorId].type){
		type = ActivityDiagramClass.connectors[connectorId].type;
	}else{
		throw 'no connector type found';
	}
	
	var prevActivityId = '';
	if(previousActivityId){
		prevActivityId = previousActivityId;
	}else if(ActivityDiagramClass.connectors[connectorId].activityRef){
		prevActivityId = ActivityDiagramClass.connectors[connectorId].activityRef;//issue in case of connector of connector
	}else{
		throw 'no activity  reference id found';
	}
	
	var connectorTypeDescription = ActivityDiagramClass.getConnectorTypeDescription(type);
	if(connectorTypeDescription == null){
		throw 'wrong type of connector';
		return false;
	}
	
	//define id:
	var elementActivityId = ActivityDiagramClass.getActivityId('activity', prevActivityId);
	var elementConnectorId = ActivityDiagramClass.getActivityId('connector', connectorId);
	
	var elementConnector = $('<div id="'+elementConnectorId+'"></div>');//put connector id here instead
	elementConnector.addClass('diagram_connector');
	elementConnector.addClass(connectorTypeDescription.className);
	elementConnector.addClass(elementActivityId);
	
	if(pos == 'activity'){
		//connect to the activity as the first connector
		elementConnector.appendTo('#'+ActivityDiagramClass.getActivityId('container', prevActivityId));//containerId
		$('#'+elementConnector.attr('id')).position({
			my: "center top",
			at: "center bottom",
			of: '#'+ActivityDiagramClass.getActivityId('free', prevActivityId, 'link')
		});
	}else{
		//position according to
		elementConnector.css('position', 'absolute');
		elementConnector.css('left', Math.round(pos.left)+'px');
		elementConnector.css('top', Math.round(pos.top)+'px');
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
	if(connectorTypeDescription.portNumber%2){
		//odd:
		offsetStep = width/connectorTypeDescription.portNumber;
		offsetStart = offsetStep/2;
	}else{
		//even:
		offsetStep = width/(connectorTypeDescription.portNumber+1);
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
	for(i=0; i<connectorTypeDescription.portNumber; i++){
		ActivityDiagramClass.setBorderPoint(connectorId, 'connector', 'bottom', Math.round(offsetStart+i*offsetStep), i);
	}
	
}

ActivityDiagramClass.getConnectorsByActivity = function(activityId){
	var connectors = [];
	for(connectorId in ActivityDiagramClass.connectors){
		var connector = ActivityDiagramClass.connectors[connectorId];
		if(connector.activityRef == activityId){
			connectors.push(connectorId);
		}
	}
	
	return connectors;
}


ActivityDiagramClass.getActivityUri = function(activityId){
	return ActivityDiagramClass.localNameSpace + activityId;
}


ActivityDiagramClass.getConnectorTypeDescription = function(connectorType){
	var portNumber =0;
	var className = '';
	var portNames = []
	switch(connectorType.toLowerCase()){
		case 'sequence':{
			portNumber = 1;
			className = 'connector_sequence';
			portNames[0] = 'Next';
			break;
		}
		case 'conditional':{
			portNumber = 2;
			className = 'connector_conditional';
			portNames[0] = 'Then';
			portNames[1] = 'Else';
			break;
		}
		case 'parallel':{
			portNumber = 3;//current number + 1 new
			className = 'connector_parallel';
			break;
		}
		case 'join':{
			portNumber = 1;
			className = 'connector_join';
			break;
		}
		default:
			return null;
	}
	
	return {portNumber: portNumber, className: className, portNames:portNames};
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

ActivityDiagramClass.setFeedbackMenu = function(mode){
	
	var eltContainer = $(ActivityDiagramClass.feedbackContainer);
	if(!eltContainer.length){
		throw 'no feedback container found';
	}
	
	//empty it:
	eltContainer.empty();
	
	// set message:
	$('<div id="feedback_message_container"><span id="feedback_message" class="feedback_message"></span></div>').appendTo(eltContainer);
	
	//set menu save/cancel:
	$('<div id="feedback_menu_list_container"><ul id="feedback_menu_list" class="feedback_menu_list"/></div>').appendTo(eltContainer);
	eltList = $('#feedback_menu_list');
	eltList.append('<li><a id="feedback_menu_save" class="feedback_menu_list_element" href="#">Save</a></li>');
	eltList.append('<li><a id="feedback_menu_cancel" class="feedback_menu_list_element" href="#">Cancel</a></li>');
	
	//destroy related event (usefulnes??):
	// $("#feedback_menu_save").unbind();
	// $("#feedback_menu_cancel").unbind();
				

	switch(mode){
		case 'ModeInitial':{
			$("#feedback_message").text('Initial Mode');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeInitial.save();
			});
			break;
		}
		case 'ModeActivityAdd':{
			$("#feedback_message").text('activity adding mode');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeActivityAdd.save();
			});
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeActivityAdd.cancel();
			});
			break;
		}
		case 'ModeActivityLabel':{
			$("#feedback_message").text('Edit the activity label then press enter');
			break;
		}
		case 'ModeLinkedActivityAdd':{
			$("#feedback_message").text('linked activity adding mode');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeLinkedActivityAdd.save();
			});
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeLinkedActivityAdd.cancel();
			});
			break;
		}
		case 'ModeActivityMenu':{
			$("#feedback_message").text('activity memu: select an action');
			$("#feedback_menu_save").parent().remove();
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeActivityMenu.cancel();
			});
			break;
		}
		case 'ModeArrowLink':{
			$("#feedback_message").text('Connect to an activity or a connector');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeArrowLink.save();
			});
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeArrowLink.cancel();
			});
			break;
		}
		case 'ModeActivityMove':{
			$("#feedback_message").text('Drag the selected activity around');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeActivityMove.save();
				
			});
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeController.setMode('ModeInitial');
			});
			break;
		}
		case 'ModeConnectorMove':{
			$("#feedback_message").text('Drag the selected connector around');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeConnectorMove.save();
			});
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeController.setMode('ModeInitial');
			});
			break;
		}
		case 'ModeArrowEdit':{
			$("#feedback_message").text('Move flex poins or arrow tip');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeArrowEdit.save();
			});
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeArrowEdit.cancel();
			});
			break;
		}
		default:{
			throw 'unknown mode: '+mode;
			eltContainer.empty();
			return false;
		}ModeActivityMove
	}
	
	return true;
}

ActivityDiagramClass.unsetFeedbackMenu = function(){
	var eltContainer = $(ActivityDiagramClass.feedbackContainer);
	if(!eltContainer.length){
		throw 'no feedback container found';
	}else{
		eltContainer.empty();
	}
}
/*
ActivityDiagramClass.createDroppablePoint = function(targetId, position){
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
*/

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