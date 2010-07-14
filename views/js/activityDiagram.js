alert("activity diagram Class loaded");

//require arrows.js

ActivityDiagramClass = new Object();
ActivityDiagramClass.defaultActivityLabel = "Activity";
ActivityDiagramClass.activities = [];
ActivityDiagramClass.connectors = [];
ActivityDiagramClass.feedbackContainer = "#process_diagram_feedback";
ActivityDiagramClass.currentMode = null;
ActivityDiagramClass.scrollLeft = 0;
ActivityDiagramClass.scrollTop = 0;
ActivityDiagramClass.defaultPosition = {top:50, left:30};
ActivityDiagramClass.relatedTree = null;


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
				
				var connector = ActivityDiagramClass.feedConnector(connectorData, positionData, activityId, arrowData, activityId);
				
				if(connector == null){
					throw 'connector cannot be created for the activity '+activityId;
				}else{
					//add the arrow between the activity and its first connector:
					console.log('connector', connector);
					var activityBottomBorderPointId = ActivityDiagramClass.getActivityId('activity',activityId,'bottom');
					var connectorTopBorderPointId = ActivityDiagramClass.getActivityId('connector',connector.id,'top');
					var arrow = null;
					if(arrowData){
						if(arrowData[activityBottomBorderPointId]){
							arrow = arrowData[activityBottomBorderPointId];
							ArrowClass.feedArrow(activityBottomBorderPointId, connectorTopBorderPointId, connector.id, arrow.position, arrow.flex);
						}
					}
					if(!arrow){
						ArrowClass.feedArrow(activityBottomBorderPointId, connectorTopBorderPointId, connector.id, 'top', null);
					}
					
				}
			
			}
		}
		
		return ActivityDiagramClass.activities[activityId];
		
	}
}

ActivityDiagramClass.reloadDiagram = function(){
	console.log('reload ', ActivityDiagramClass.canvas);

	//load diagram:
	ActivityDiagramClass.loadDiagram();
}


ActivityDiagramClass.loadDiagram = function(){
	console.log('loading diagram');
	//ajax call to get the model
	$.ajax({
		url: authoringControllerPath + 'getActivities',
		type: "POST",
		data: {"processUri": processUri, "diagramData": true},
		dataType: 'json',
		success: function(processData){
			// console.log(response);
			try{
				// $(ActivityDiagramClass.canvas).scrollTop(0);
				// $(ActivityDiagramClass.canvas).scrollLeft(0);
				$(ActivityDiagramClass.canvas).empty();
				var oldScrollLeft = ActivityDiagramClass.scrollLeft;
				var oldScrollTop = ActivityDiagramClass.scrollTop;
				ActivityDiagramClass.scrollLeft = $(ActivityDiagramClass.canvas).scrollLeft();//0
				ActivityDiagramClass.scrollTop = $(ActivityDiagramClass.canvas).scrollTop();
				
				ActivityDiagramClass.activities = [];
				ActivityDiagramClass.connectors = [];
				ActivityDiagramClass.feedDiagram(processData);
				
				ActivityDiagramClass.drawDiagram();
				
				//restore:
				$(ActivityDiagramClass.canvas).scrollLeft(oldScrollLeft);
				$(ActivityDiagramClass.canvas).scrollTop(oldScrollTop);
				
				//initiate the mode to initial:
				ModeController.setMode('ModeInitial');
			}
			catch(err){
				console.log('loading diagram exception', err);
			}
		}
	});
}


ActivityDiagramClass.editConnector = function(connectorId, port, value, multiplicity){
	var connector = ActivityDiagramClass.getConnector(connectorId);
	if(connector.port){
		if(value){
			var num = 1;
			if(multiplicity){
				num = multiplicity;
			}
			connector.port[port] = {
				"targetId": value,
				"multiplicity": num,
				"label": 'newly edited'
			}
		}
	}
}

ActivityDiagramClass.getConnector = function(connectorId){
	var connector = ActivityDiagramClass.connectors[connectorId];
	if(!connector){
		throw 'the connector does not exist: '+connectorId;
		return null;
	}
	return connector;
}


ActivityDiagramClass.isConnector = function(connectorId){
	var isConnector = false;
	if(ActivityDiagramClass.connectors[connectorId]){
		isConnector = true;
	}
	return isConnector;
}

ActivityDiagramClass.isActivity = function(activityId){
	var isActivity = false;
	if(ActivityDiagramClass.activities[activityId]){
		isActivity = true;
	}
	return isActivity;
}

ActivityDiagramClass.saveConnector = function(connectorId){
	
	var connector = ActivityDiagramClass.getConnector(connectorId);
	var connectorDescription = ActivityDiagramClass.getConnectorTypeDescription(connector);
	var connectorUri = ActivityDiagramClass.getActivityUri(connectorId);
	var prevActivityId = connector.activityRef;//real activityRef required
	var prevActivityUri = ActivityDiagramClass.getActivityUri(prevActivityId);
	var postData = '';
	switch(connectorDescription.typeUri){
		case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:{
			

			if(connector.port[0]){
				if(connector.port[0].targetId){
					var targetId = connector.port[0].targetId;
					if(ActivityDiagramClass.isActivity(targetId)){
						postData += '&next_activityOrConnector=activity';
						postData += '&next_activityUri=' + ActivityDiagramClass.getActivityUri(targetId);
					}else if(targetId == 'newActivity'){
						postData += '&next_activityOrConnector=activity';
						postData += '&next_activityUri=newActivity';
					}else if(ActivityDiagramClass.isConnector(targetId)){
						postData += '&next_activityOrConnector=connector';
						postData += '&next_connectorUri=' + ActivityDiagramClass.getActivityUri(targetId);
					}else if(targetId == 'newConnector'){
						postData += '&next_activityOrConnector=connector';
						postData += '&next_connectorUri=newConnector';
					}
				}
			}
			
			//default: delete the link to the next activity:
			if(postData==''){
				postData += '&next_activityOrConnector=delete';
			}
			
			postData += '&'+PROPERTY_CONNECTORS_TYPE+'=' + INSTANCE_TYPEOFCONNECTORS_SEQUENCE;
			break;
		}
		case INSTANCE_TYPEOFCONNECTORS_SPLIT:{
			
			for(var i=0; i<connectorDescription.portNumber; i++){
				var prefix = connectorDescription.portNames[i].toLowerCase();
				var postDataTemp = '';
				
				if(connector.port[i]){
					if(connector.port[i].targetId){
						var targetId = connector.port[i].targetId;
						// console.log()
						if(ActivityDiagramClass.isActivity(targetId)){
							postDataTemp += '&'+prefix+'_activityOrConnector=activity';
							postDataTemp += '&'+prefix+'_activityUri=' + ActivityDiagramClass.getActivityUri(targetId);
						}else if(targetId == 'newActivity'){
							postDataTemp += '&'+prefix+'_activityOrConnector=activity';
							postDataTemp += '&'+prefix+'_activityUri=newActivity';
						}else if(ActivityDiagramClass.isConnector(targetId)){
							postDataTemp += '&'+prefix+'_activityOrConnector=connector';
							postDataTemp += '&'+prefix+'_connectorUri=' + ActivityDiagramClass.getActivityUri(targetId);
						}else if(targetId == 'newConnector'){
							postDataTemp += '&'+prefix+'_activityOrConnector=connector';
							postDataTemp += '&'+prefix+'_connectorUri=newConnector';
						}
					}
				}
				
				//default: delete the link to the next activity:
				if(postDataTemp==''){
					postData += '&'+prefix+'_activityOrConnector=delete';
				}else{
					postData += postDataTemp;
				}
			}
			
			postData += '&'+PROPERTY_CONNECTORS_TYPE+'=' + INSTANCE_TYPEOFCONNECTORS_SPLIT;
			break;
		}
		case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
			
			console.log('parallel connecor not implemented yet');
			/*
			for(var i=0; i<connectorDescription.portNumber; i++){
				var prefix = connectorDescription.portNames[i].toLowerCase();
				var postDataTemp = '';
				
				if(connector.port[i]){
					if(connector.port[i].targetId){
						var targetId = connector.port[i].targetId;
						// console.log()
						if(ActivityDiagramClass.isActivity(targetId)){
							postDataTemp += '&'+prefix+'_activityOrConnector=activity';
							postDataTemp += '&'+prefix+'_activityUri=' + ActivityDiagramClass.getActivityUri(targetId);
						}else if(targetId == 'newActivity'){
							postDataTemp += '&'+prefix+'_activityOrConnector=activity';
							postDataTemp += '&'+prefix+'_activityUri=newActivity';
						}else if(ActivityDiagramClass.isConnector(targetId)){
							postDataTemp += '&'+prefix+'_activityOrConnector=connector';
							postDataTemp += '&'+prefix+'_connectorUri=' + ActivityDiagramClass.getActivityUri(targetId);
						}else if(targetId == 'newConnector'){
							postDataTemp += '&'+prefix+'_activityOrConnector=connector';
							postDataTemp += '&'+prefix+'_connectorUri=newConnector';
						}
					}
				}
				
				//default: delete the link to the next activity:
				if(postDataTemp==''){
					postData += '&'+prefix+'_activityOrConnector=delete';
				}else{
					postData += postDataTemp;
				}
			}
			
			postData += '&'+PROPERTY_CONNECTORS_TYPE+'=' + INSTANCE_TYPEOFCONNECTORS_SPLIT;
			*/
			break;
		}
		case INSTANCE_TYPEOFCONNECTORS_JOIN:{
			
			for(var i=0; i<connectorDescription.portNumber; i++){
				var prefix = connectorDescription.portNames[i].toLowerCase();
				var postDataTemp = '';
				
				if(connector.port[i]){
					if(connector.port[i].targetId){
						var targetId = connector.port[i].targetId;
						// console.log()
						if(ActivityDiagramClass.isActivity(targetId)){
							postDataTemp += '&join_activityUri=' + ActivityDiagramClass.getActivityUri(targetId);
						}else if(targetId == 'newActivity'){
							postDataTemp += '&join_activityUri=newActivity';
						}
					}
				}
				
				//default: delete the link to the next activity:
				if(postDataTemp==''){
					postData += '&join_activityUri=delete';
				}else{
					postData += postDataTemp;
				}
			}
			
			postData += '&'+PROPERTY_CONNECTORS_TYPE+'=' + INSTANCE_TYPEOFCONNECTORS_JOIN;
			break;
		}
	}
	
	// console.log('postData', postData);
	
	//call gateway:
	if(postData != ''){
		// console.log(postData);
		GatewayProcessAuthoring.saveConnector(authoringControllerPath+"saveConnector", connectorUri, prevActivityUri, postData)
	}else{
		return false;
	}
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

ActivityDiagramClass.feedConnector = function(connectorData, positionData, prevActivityId, arrowData, activityRefId){

	//find recursively all connectors and create the associated arrows:
	
	if(!connectorData.attributes.id){
		throw 'no connector id found';
		return false;
	}
	if(!prevActivityId){
		throw 'no previous activity id found';
		return false;
	}
	if(!activityRefId){
		throw 'no activity reference id found';
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

	ActivityDiagramClass.connectors[connectorId].activityRef = activityRefId;//get the real activity reference instead
	ActivityDiagramClass.connectors[connectorId].previousActivity = prevActivityId;
	
//do not draw connector here, feed them first until everything is fed:
	
	//inti port value:
	ActivityDiagramClass.connectors[connectorId].port = new Array();
	
	//check if the connector has another connector:
	if(connectorData.children){
		
		for(var i=0;i<connectorData.children.length; i++){
			var nextActivityData = connectorData.children[i];
			if(!nextActivityData.attributes){
				continue; //should be a non activity nor connector node:
				
				// console.log('attributes for next activity empty:', nextActivityData);
				// throw 'no attributes data found for the next activity';
			}
			if(nextActivityData.attributes.id && nextActivityData.attributes.class=='node-connector'){
				//recursively continue with the connector of connector:
				ActivityDiagramClass.feedConnector(nextActivityData, positionData, connectorId, arrowData, activityRefId);//use the current connector as the activityRef of the child connector
			}
			
			if(nextActivityData.portData){
				if(nextActivityData.portData.id >= 0){//it can only be node-connector,node-activity-goto or node-connector-goto
					//feed port data into connector storehouse, to allow later next activity editing:
					ActivityDiagramClass.connectors[connectorId].port[ nextActivityData.portData.id ] = {
						"targetId": ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.id),
						"label": nextActivityData.portData.label,
						"multiplicity": nextActivityData.portData.multiplicity
					};
					
					//draw arrow:
					var originId =  ActivityDiagramClass.getActivityId('connector', connectorId, 'bottom', nextActivityData.portData.id);
					var nextActivityId = '';
					var targetId = '';
					
					if(nextActivityData.attributes.class == 'node-connector'){
						nextActivityId = ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.id);
					}else if(nextActivityData.attributes.class == 'node-activity-goto' || nextActivityData.attributes.class == 'node-connector-goto'){
						nextActivityId = ActivityDiagramClass.getIdFromUri(nextActivityData.attributes.rel);
					}else{
						throw 'unknown type of following activity';
					}
					
					//check if the target in the arrow data matches the one recorded in the connector port data:
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
					
					ArrowClass.feedArrow(originId, targetId, nextActivityId, targetPosition, flex);
					
				}
			}
			
			/*
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
				
				//check if the target in the arrow data matches the one recorded in the connector port data:
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
			*/
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
	if(targetEltId.substring(0,10)=='container_'){//'activity_' 9
		targetId = targetEltId.substring(10);
		if(ActivityDiagramClass.activities[targetId]){
			ActivityDiagramClass.activities[targetId].position = newPosition;
		}else{
			throw 'the target activity does not exist';
		}
		
	}else if(targetEltId.substring(0,10)=='connector_'){
		targetId = targetEltId.substring(10);
		if(ActivityDiagramClass.connectors[targetId]){
			ActivityDiagramClass.connectors[targetId].position = newPosition;
		}else{
			throw 'the target connector does not exist';
		}
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
	
	// ActivityDiagramClass.drawActivity("ActivityTempId", {
		// left: 1500,
		// top: 1000
	// },
	// 'ActivityTemp');
	
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
		var targetId = ArrowClass.arrows[arrowId].target;
		if(arrowId && targetId){
			//check if target does exist:
			if($('#'+targetId).length){
				ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId),$('#'+targetId));
				// console.log('calculated arrows:');
				// console.dir(ArrowClass.arrows);
				ArrowClass.drawArrow(arrowId, {
					container: ActivityDiagramClass.canvas,
					arrowWidth: 2
				});
				ActivityDiagramClass.setArrowMenuHandler(arrowId);
			}else{
				//delete it:
				delete ArrowClass.arrows[arrowId];
			}	
		}else{
			console.log('the followgin arrow cannot be drawn:', arrowId);
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
	if(uri){
		var startIndex = uri.lastIndexOf('%23');//look for the encoded "#" in the uri
		if(startIndex>0){
			returnValue = uri.substr(startIndex+3);
		}
	}else{
		// throw 'invalid uri given';
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
	
	// ActivityDiagramClass.setBorderPoint(activityId, 'activity', 'top');
	// ActivityDiagramClass.setBorderPoint(activityId, 'activity', 'right');
	
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
		if(ActivityDiagramClass.activities[activityId].isLast){
			// elementActivity.addClass('diagram_activity_final');
		}
		
		if(hasConnector == false){
			//TODO: replace by "last actiivty" class instead
			// elementActivity.addClass('diagram_activity_final');
		}
	

		//is first or not?
		if(ActivityDiagramClass.activities[activityId].isInitial == true){
			//TODO: replace by first activity class instead:
			
			elementActivity.addClass('diagram_activity_initial');
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
			ActivityTreeClass.setCurrentNode('tree-activity', ActivityDiagramClass.getActivityUri(event.data.id));
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
	
	// var type = '';
	if(connectorType){
		ActivityDiagramClass.connectors[connectorId].type = connectorType;
		// type = connectorType;
	}else if(ActivityDiagramClass.connectors[connectorId].type){
		// type = ActivityDiagramClass.connectors[connectorId].type;
	}else{
		throw 'no connector type found';
	}
	
	var activityRefId = '';
	if(previousActivityId){
		activityRefId = previousActivityId;
	}else if(ActivityDiagramClass.connectors[connectorId].activityRef){//real activityRef Id required
		activityRefId = ActivityDiagramClass.connectors[connectorId].activityRef;
	}else{
		throw 'no activity  reference id found';
	}
	
	var connectorTypeDescription = ActivityDiagramClass.getConnectorTypeDescription(ActivityDiagramClass.connectors[connectorId]);
	//console.log('connectorTypeDescription', connectorTypeDescription);
	if(connectorTypeDescription == null){
		throw 'wrong type of connector';
		return false;
	}
	
	//define id:
	var elementActivityId = ActivityDiagramClass.getActivityId('activity', activityRefId);
	var elementConnectorId = ActivityDiagramClass.getActivityId('connector', connectorId);
	
	var elementConnector = $('<div id="'+elementConnectorId+'"></div>');//put connector id here instead
	elementConnector.addClass('diagram_connector');
	elementConnector.addClass(connectorTypeDescription.className);
	elementConnector.addClass(elementActivityId);
	
	//position according to
	elementConnector.css('position', 'absolute');
	elementConnector.css('left', Math.round(pos.left)+'px');
	elementConnector.css('top', Math.round(pos.top)+'px');
	//add directly to canvas:
	elementConnector.appendTo(ActivityDiagramClass.canvas);
	
	
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
		if(connector.previousActivity == activityId){//use .previousActivity instead
			connectors.push(connectorId);
		}
	}
	
	return connectors;
}


ActivityDiagramClass.getActivityUri = function(activityId){
	return ActivityDiagramClass.localNameSpace + activityId;
}


ActivityDiagramClass.getConnectorTypeDescription = function(connector){
	
	if(connector){
		if(connector.type){
			var portNumber =0;
			var className = '';
			var typeUri = '';
			var portNames = [];
			switch(connector.type.toLowerCase()){
				case '':{
					portNumber = 0;
					className = 'connector_sequence';
					portNames[0] = 'Next';
					typeUri = INSTANCE_TYPEOFCONNECTORS_SEQUENCE;
					break;
				}
				case 'sequence':{
					portNumber = 1;
					className = 'connector_sequence';
					portNames[0] = 'Next';
					typeUri = INSTANCE_TYPEOFCONNECTORS_SEQUENCE;
					break;
				}
				case 'conditional':{
					portNumber = 2;
					className = 'connector_conditional';
					portNames[0] = 'Then';
					portNames[1] = 'Else';
					typeUri = INSTANCE_TYPEOFCONNECTORS_SPLIT;
					break;
				}
				case 'parallel':{
					portNumber = connector.port.length + 1;
					
					for(var i=0; i<portNumber; i++){
						portNames[i] = 'Parallel_activityUri_'+i;
					}
					
					className = 'connector_parallel';
					typeUri = INSTANCE_TYPEOFCONNECTORS_PARALLEL;
					break;
				}
				case 'join':{
					portNumber = 1;
					className = 'connector_join';
					portNames[0] = 'Next';
					typeUri = INSTANCE_TYPEOFCONNECTORS_JOIN;
					break;
				}
				default:
					return null;
			}
		}
	}
	
	return {
		"portNumber": portNumber, 
		"className": className, 
		"portNames": portNames,
		"typeUri": typeUri
		};
}

ActivityDiagramClass.setBorderPoint = function(targetId, type, position, offset, port){
	
	// console.log("pos", position);
	// console.log('util',processUtil);
	
	// console.log('type',type);
	// console.log('targetId',targetId);
	
	var pos = '';
	var	my = '';
	var	at = '';
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
	// elementPoint.appendTo('#'+containerId);
	// $('#'+pointId).position({
		// my: my,
		// at: at,
		// of: '#'+containerId,
		// offset: offsetSet
	// });
	
	elementPoint.appendTo('#'+containerId).position({
		my: my,
		at: at,
		of: '#'+containerId,
		offset: offsetSet,
		collision: "none"
	});
	
	//console.log(type,targetId);
	//console.log('position',position);
	//console.log('my',my);
	//console.log('at',at);
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
			$("#feedback_message").text('Process Diagram');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeInitial.save();
			});
			$("#feedback_menu_cancel").parent().remove();
			break;
		}
		case 'ModeActivityLabel':{
			$("#feedback_message").text('Edit the activity label then press "enter" or "save".');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeActivityLabel.save();
			});
			break;
		}
		
		case 'ModeActivityMenu':{
			$("#feedback_message").text('Activity menu: select an action from the context menu.');
			$("#feedback_menu_save").parent().remove();
			break;
		}
		case 'ModeArrowLink':{
			$("#feedback_message").text('Drag the arrow tip and connect it to an activity or a connector.');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeArrowLink.save();
			});
			break;
		}
		case 'ModeActivityMove':{
			$("#feedback_message").text('Drag and drop the selected activity.');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeActivityMove.save();
			});
			break;
		}
		case 'ModeConnectorMove':{
			$("#feedback_message").text('Drag and drop the selected connector.');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeConnectorMove.save();
			});
			break;
		}
		case 'ModeArrowEdit':{
			$("#feedback_message").text('Move flex points or arrow tip.');
			$("#feedback_menu_save").click(function(event){
				event.preventDefault();
				ModeArrowEdit.save();
			});
			break;
		}
		default:{
			throw 'unknown mode: '+mode;
			eltContainer.empty();
			return false;
		}
		
		//the same cancel button for every mode:
		if($("#feedback_menu_cancel").length){
			$("#feedback_menu_cancel").click(function(event){
				event.preventDefault();
				ModeController.setMode('ModeInitial');
			});
		}
		
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

ActivityDiagramClass.refreshRelatedTree = function(){
	var anActivityTree = ActivityTreeClass.instances[ActivityDiagramClass.relatedTree];
	if(anActivityTree){
		console.log('refreshing tree');
		var aJsTree = anActivityTree.getTree();
		ActivityTreeClass.refreshTree({
			TREE_OBJ: aJsTree
		});
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