alert('ModeActivityMenu loaded');

ModeActivityMenu = [];
ModeActivityMenu.existingMenu = new Array();

ModeActivityMenu.on = function(type, targetId){
	//for precaution only, delete all existing menu (should be none):
	ModeActivityMenu.removeAllMenu();
	
	switch(type){
		case 'activity':{
			//insert information in the feedback 'div'
			if(!ActivityDiagramClass.setFeedbackMenu('ModeActivityMenu')){
				return false;
			}
			ModeActivityMenu.createActivityMenu(targetId);
			break;
		}
		case 'connector':{
			ModeActivityMenu.createConnectorMenu(targetId);
			break;
		}
		case 'arrow':{
			ModeActivityMenu.createArrowMenu(targetId);
			break;
		}
	}
}

ModeActivityMenu.createActivityMenu = function(activityId){
	//create top menu for the activity: first, last, edit, delete
	var containerId = ActivityDiagramClass.getActivityId('activity', activityId);
	actions = [];
	actions.push({
		label: "Define as the first activity",
		icon: img_url + "flag-green.png",
		action: function(actId){
			console.log('isFirst => ',actId);
		}
	});
	actions.push({
		label: "Define as a last activity",
		icon: img_url + "flag-red.png",
		action: function(actId){
			console.log('islast => ',actId);
		}
	});
	actions.push({
		label: "Move",
		icon: img_url + "pencil.png",
		action: function(actId){
			console.log('move => ',actId);
		}
	});
	actions.push({
		label: "Edit",
		icon: img_url + "pencil.png",
		action: function(actId){
			console.log('edit',actId);
		},
		autoclose: false
	});
	actions.push({
		label: "Delete",
		icon: img_url + "delete.png",
		action: function(actId){
			console.log('delete => ',actId);
		}
	});
	// console.log('actions',actions);
	
	ModeActivityMenu.createMenu(
		activityId,
		containerId,
		'top',
		actions
	);
	// ModeActivityMenu.existingMenu = new Array();
	ModeActivityMenu.existingMenu[containerId] = containerId;
	// console.log("created menus:", ModeActivityMenu.existingMenu);
}

ModeActivityMenu.createConnectorMenu = function(connectorId){
	var topContainerId = ActivityDiagramClass.getActivityId('connector', connectorId, 'top');
	actions = [];
	var isFirstConnector = true;
	if(!isFirstConnector){
		actions.push({
			label: "Move",
			icon: img_url + "pencil.png",
			action: function(actId){
				console.log('move => ',actId);
			}
		});
	}
	actions.push({
		label: "Edit",
		icon: img_url + "pencil.png",
		action: function(actId){
			console.log('edit',actId);
		},
		autoclose: false
	});
	actions.push({
		label: "Delete",
		icon: img_url + "delete.png",
		action: function(actId){
			console.log('delete => ',actId);
		}
	});
	ModeActivityMenu.createMenu(
		connectorId,
		topContainerId,
		'top',
		actions,
		{offset:10}
	);
	ModeActivityMenu.existingMenu[topContainerId] = connectorId;
	
	//get the type of connector, and thus the name of all 'port'
	
	//for each port i, get the id and create a menu with one single option (autoclose set to false)
	//on the callback action of each menu, build a second contextual one 'onclick':
	
	//check if an arrow (=connection) exists:
	
	//if so, create a menu 2 item : delete or edit (link to editArrowMode)
	
	//else, menu with 3 items: new activity, new connector, free connection
}


ModeActivityMenu.createMenu = function(targetId, containerId, position, actions, options){
	
	//container = activity or connector:
	var container = $('#'+containerId);
	if(!container.length){
		throw 'no such container element in the DOM';
	}
	
	//think about destroying old menu:
	
	//set default options value:
	var offset = 20;
	var autoclose = true;
	if(options){
		console.log('autoclose', autoclose);
		if(options.offset){
			offset = options.offset;
		}
		if(options.autoclose){
			autoclose = options.autoclose;
		}
	}

	
	var menuId = containerId+'_menu';
	var menuContainerId = menuId+'_container';
	var menuContainer = $('<div id="'+menuContainerId+'"/>').appendTo(container);
	var calculatedWith = (10+5+16+5)*parseInt(actions.length);
	var calculatedHeight = (3+16+3);
	menuContainer.width(calculatedWith+"px");
	menuContainer.height(calculatedHeight+"px");
	menuContainer.css('z-index',1000);//always on top
	var menu = $('<ul id="'+menuId+'"/>').appendTo(menuContainer);
	menu.addClass('activity_menu_horizontal');
	
	for(var i=0; i<actions.length; i++){
		var action = actions[i];
		
		if(targetId && action.label && action.icon && action.action){
			
			var anchorId = menuId+'_action_'+i;
			var anchor = $('<a id="'+anchorId+'"/>').appendTo($('<li/>').appendTo(menu));
			anchor.attr('title', action.label);
			anchor.attr('rel', targetId);
			anchor.append('<ins style="background-image: url(\''+action.icon+'\');">&nbsp;</ins>');
			
			initialAutoclose = autoclose;
			if(action.autoclose!=null){
				autoclose = action.autoclose;//if the autoclose option is set, overwrite the value
			}
			// console.log('i:',i);
			// console.log('action',action);
			// console.log('autoclose',autoclose);
			anchor.bind('click', {id:targetId, action:action.action, autoclose: autoclose}, function(event){
				event.preventDefault();
				event.stopPropagation();
				event.data.action(event.data.id);
				if(event.data.autoclose){
					ModeActivityMenu.cancel();
				}
			});
			autoclose = initialAutoclose;//restore intial value, useful only when action.autoclose is set
		}
	}
	
	//position the menu with respect to the container:
	
	
	switch(position){
		case 'top':{
			menuContainer.position({
				my: "center bottom",
				at: "center top",
				of: '#'+containerId,
				offset: "0 -"+offset
			});
			break;
		}
		case 'bottom':{
			menuContainer.position({
				my: "center top",
				at: "center bottom",
				of: '#'+containerId,
				offset: "0 "+offset
			});
			break;
		}
		case 'left':{
			menuContainer.position({
				my: "right center",
				at: "left center",
				of: '#'+containerId,
				offset: "-"+offset+" 0"
			});
			break;
		}
		case 'right':{
			menuContainer.position({
				my: "left center",
				at: "right center",
				of: '#'+containerId,
				offset: offset+" 0"
			});
			break;
		}
		default:{
			//destroy all and return error:
			menu.remove();
			return false
		}
	}
	
	
	return true;
}

ModeActivityMenu.removeMenu = function(containerId){
	if(containerId){
		var menuId = containerId+'_menu';
		var menuContainerId = menuId+'_container';
		if($('#'+menuContainerId).length){
			$('#'+menuContainerId).remove();
		}
		
	}
}

ModeActivityMenu.removeAllMenu = function(){
	// console.log('menus to delete', ModeActivityMenu.existingMenu);
	if(ModeActivityMenu.existingMenu){
		for(containerId in ModeActivityMenu.existingMenu){
			ModeActivityMenu.removeMenu(containerId);
			delete ModeActivityMenu.existingMenu[containerId];
		}
	}
}

ModeActivityMenu.cancel = function(){
	//update feedback box:
	
	//delete old menu
	ModeActivityMenu.removeAllMenu();
	ActivityDiagramClass.unsetFeedbackMenu();
}
