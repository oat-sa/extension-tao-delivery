ModeActivityAdd = [];

ModeActivityAdd.on = function(){
	//insert information in the feedback 'div'
	ActivityDiagramClass.setFeedbackMenu('Activity Adding Mode');
	
	/*
	//create an activity temp
	tempActivity = ActivityDiagramClass.createTempActivity();
	
	//make it draggable (set handler to its container):
	containerId = ActivityDiagramClass.getActivityId('container', tempActivity.id);
	if(!$('#'+containerId).length){
		throw 'The activity container '+containerId+' do not exists.';
	}
	$('#'+containerId).draggable({
		containment: ActivityDiagramClass.canvas
	});
	
	//make the label editable
	ModeActivityLabel.createLabelTextbox(tempActivity.id);
	*/
}

ModeActivityAdd.off = function(){

}


