alert('ModeActivityMenu loaded');

ModeActivityMenu = [];

ModeActivityMenu.on = function(activityId){
	//create top menu for the activity: first, last, edit, delete

	var containerId = ActivityDiagramClass.getActivityId('activity', activityId);
	actions = [{
			label: "Define as the first activity",
			icon: img_url + "flag-green.png",
			action: function(actId){
				console.log('isFirst => ',actId);
			}
		}];
	actions.push({
		label: "Define as a last activity",
		icon: img_url + "flag-red.png",
		action: function(actId){
			console.log('islast => ',actId);
		}
	});
	actions.push({
		label: "Edit",
		icon: img_url + "pencil.png",
		action: function(actId){
			console.log('edit',actId);
		}
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
	
}

ModeActivityMenu.createMenu = function(targetId, containerId, position, actions){
	
	//container = activity or connector:
	var container = $('#'+containerId);
	if(!container.length){
		throw 'no such container element in the DOM';
	}
	
	//think about destroying old menu:
	
	var menuId = containerId+'_menu';
	var menuContainerId = menuId+'_container';
	var menuContainer = $('<div id="'+menuContainerId+'"/>').appendTo(container);
	var calculatedWith = (10+5+16+5)*parseInt(actions.length);
	var calculatedHeight = (3+16+3);
	menuContainer.width(calculatedWith+"px");
	menuContainer.height(calculatedHeight+"px");
	
	var menu = $('<ul id="'+menuId+'"/>').appendTo(menuContainer);
	menu.addClass('activity_menu_horizontal');
	// menu.position({
		// my: "center center",
		// at: "center center",
		// of: '#'+menuContainerId,
	// });
	
	for(var i=0; i<actions.length; i++){
		var action = actions[i];
		
		if(targetId && action.label && action.icon && action.action){
			
			var anchorId = menuId+'_action_'+i;
			var anchor = $('<a id="'+anchorId+'"/>').appendTo($('<li/>').appendTo(menu));
			anchor.attr('title', action.label);
			anchor.attr('rel', targetId);
			// anchor.width('22px');
			// anchor.height('22px');
			anchor.append('<ins style="background-image: url(\''+action.icon+'\');">&nbsp;</ins>');
			// menu.append('<li><a id="'+eltId+'" title="'+action.label+'"><ins style="img_url">&nbsp;<:ins></a></li>');
			
			//set callback action:
			// anchor.click(function(event){
				// event.preventDefault();
				// action.action($(this).attr('rel'));
			// });
			
			anchor.bind('click', {id:targetId, action:action.action}, function(event){
				event.preventDefault();
				event.data.action(event.data.id);
			});
			
		}
	}
	
	//position the menu with respect to the container:
	var offset = 22;
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
