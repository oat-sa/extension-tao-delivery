ModeActivityLabel = [];

ModeActivityLabel.on = function(){

}

ModeActivityLabel.off = function(){

}

ModeActivityLabel.createLabelTextbox = function(activityId){
	var returnValue = null;
	var targetId = ActivityDiagramClass.getActivityId('activity', activityId);
	var elementActivity = $('#'+targetId);//id of the activity
	
	if(elementActivity.length){
	
		var elementLabelId = ActivityDiagramClass.getActivityId('activityLabel', activityId);
		var elementLabel = $('#'+elementLabelId);
		// console.log('lbl', elementLabelId);
		
		if(elementLabel.length){
			var currentLabel = elementLabel.text();
			//get from the model instead?:
			
			var elementTextbox = $('<input type="text" id="'+elementLabelId+'_input"/>');
			elementTextbox.addClass('diagram_activity_label_input');
			elementTextbox.addClass(targetId);
			elementLabel.empty();
			elementTextbox.val(currentLabel);
			elementTextbox.appendTo('#'+elementLabelId);
		
			if(currentLabel==ActivityDiagramClass.defaultActivityLabel || currentLabel==''){
				//focus
				elementTextbox.select();
			}
			elementTextbox.focus();
			
			returnValue = elementTextbox;
		}
	}
	
	return returnValue;
}

ModeActivityLabel.destroyLabelTextbox = function(activityId){
	var returnValue = '';
	var targetId = ActivityDiagramClass.getActivityId('activity', activityId);
	var elementActivity = $('#'+targetId);//id of the activity
	
	if(elementActivity.length){
		var elementLabelId = ActivityDiagramClass.getActivityId('activityLabel', activityId);
		var elementLabel = $('#'+elementLabelId);
		if(elementLabel.length){
			//if the textbox exists:
			var elementTextbox = $('#'+elementLabelId+'_input');
			if(elementTextbox.length){
				var currentLabel = elementTextbox.val();
				if(currentLabel != ''){
					elementLabel.empty();
					elementLabel.text(currentLabel);
					textCutter('#'+elementLabelId, 10);
					elementLabel.attr('title', currentLabel);
					
					elementLabel.removeClass('diagram_activity_label');
					elementLabel.addClass('diagram_activity_label');
					
					//set in the model:
					
					//return:
					returnValue = currentLabel;
				}
			}
		}
	}
	
	return returnValue;
}