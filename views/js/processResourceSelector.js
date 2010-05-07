function resourceSelector(identifier, resourceType){

	/*
	 * Open the list editor: a tree in a dialog popup 
	 */
	var dialogId = resourceType + '_dialog';
	var treeId = resourceType + '_tree';
	var closerId = resourceType + '_closer';
	
	//dialog content
	elt = $(identifier).parent("div");
	elt.append("<div id='"+ dialogId +"' style='display:none;' > " +
					"<span class='ui-state-highlight'>" + __('Select a test') + "</span><br /><br />" +
					"<div id='"+treeId+"' ></div> " +
				"</div>");
			
	//init dialog events
	$("#"+dialogId).dialog({
		width: 350,
		height: 400,
		autoOpen: false,
		title: __('Select a test')
	});
	
	$("#"+dialogId).bind('dialogclose', function(event, ui){
		$.tree.reference("#"+treeId).destroy();
		$("#"+dialogId).dialog('destroy');
		$("#"+dialogId).remove();
	});
	$("#"+closerId).click(function(){
		$("#"+dialogId).dialog('close');
	});
	$("#"+dialogId).bind('dialogopen', function(event, ui){
		
		dataUrl = root_url + "/taoDelivery/DeliveryAuthoring/getTestData";
		 
		//create tree
		$("#"+treeId).tree({
			data: {
				type: "json",
				async : true,
				opts: {
					method : "POST",
					url: dataUrl
				}
			},
			types: {
			 "default" : {
					renameable	: true,
					deletable	: false,
					creatable	: false,
					draggable	: false
				}
			},
			ui: {
				theme_name : "custom"
			},
			callback: {
				onload: function(TREE_OBJ){
						TREE_OBJ.open_branch($("li.node-class:first"));
				},
				onselect: function(NODE, TREE_OBJ) {
					//select instance node only
					if($(NODE).hasClass('node-instance')){
						//set the value of the selected link in the textbox:
						$(identifier).val($(NODE).attr('val'));
						
						//close the dialog box:
						$("#"+dialogId).dialog('close');
					}
				}
			}
		});
	});
	$("#"+dialogId).dialog('open');
}