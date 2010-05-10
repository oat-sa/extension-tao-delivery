ModeActivityMenu = [];

ModeActivityMenu.on = function(activityId){
	//create top menu for the activity: first, last, edit, delete

	var containerId = ActivityDiagramClass.getActivityId('activity', activityId);
	actions = new Array();
	actions[] = {
		label: "is first",
		icon: "the url",
		action: function(activityId){
			console.log('activityId=',activityId);
		}
	}
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
	
	var menuId = containerId+'_menu';
	var menu = $('<ul id="'+menuId+'"/>').appendTo(container);
	menu.addClass('activity_menu_horizontal');
	for(var i; i<actions.length; i++){
		var action = actions[i];
		if(targetId && action.label && action.icon && action.action){
			var anchorId = menuId+'_action_'+i;
			var anchor = $('#'+anchorId).appendTo(menu.append('<li/>'));
			anchor.attr('title', action.label);
			anchor.attr('rel', targetId);
			anchor.append('<ins style="background-image: url("'+action.icon+'");">&nbsp;<:ins>');
			// menu.append('<li><a id="'+eltId+'" title="'+action.label+'"><ins style="img_url">&nbsp;<:ins></a></li>');
			
			//set callback action:
			anchor.click(function(event){
				event.preventDefault();
				action.action($(this).attr('rel'));
			});
			
		}
	}
	
	//position the menu with respect to the container:
	var offset = 22;
	switch(position){
		case 'top':{
			menu.position({
				my: "center bottom",
				at: "center top",
				of: '#'+containerId,
				offset: "0 -"+offset
			});
			break;
		}
		case 'bottom':{
			menu.position({
				my: "center top",
				at: "center bottom",
				of: '#'+containerId,
				offset: "0 "+offset
			});
			break;
		}
		case 'left':{
			menu.position({
				my: "right center",
				at: "left center",
				of: '#'+containerId,
				offset: "-"+offset+" 0"
			});
			break;
		}
		case 'right':{
			menu.position({
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