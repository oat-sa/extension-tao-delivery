<div class="ui-widget-content ui-corner-bottom">
	
	<div id="tree-activity" ></div>
	
</div>
			
<script type="text/javascript">
	$(function(){
		initActivityTree();
	});
	
	function initActivityTree(){
		new ActivityTreeClass('#tree-activity', "/taoDelivery/DeliveryAuthoring/getActivities", {
			formContainer: 			"#activity_form",
			createActivityAction: "/taoDelivery/DeliveryAuthoring/addActivity",
			createInteractiveServiceAction: "/taoDelivery/DeliveryAuthoring/addInteractiveService",
			editInteractiveServiceAction: "/taoDelivery/DeliveryAuthoring/editCallOfService",
			editActivityPropertyAction: "/taoDelivery/DeliveryAuthoring/editActivityProperty",
			editConnectorAction: "/taoDelivery/DeliveryAuthoring/editConnector"
		});
	}
	/*
	$(function(){
		new ActivityTreeClass('#activity_tree', "/taoDelivery/DeliveryAuthoring/getActivityTree", {
			formContainer: 			"#activity_form",
			actionId: 				"activity",
			// editInstanceAction: 	"/taoDelivery/DeliveryAuthoring/editInstance",
			// createInstanceAction: 	"/taoDelivery/DeliveryAuthoring/addInstance",
			deleteAction: 			"/taoDelivery/DeliveryAuthoring/delete",
			// duplicateAction: 		"/taoDelivery/DeliveryAuthoring/cloneInstance",
			editActivityPropertyAction:			"/taoDelivery/DeliveryAuthoring/",
			editConnectorAction:	"/taoDelivery/DeliveryAuthoring/",
			editInteractiveServiceAction: "/taoDelivery/DeliveryAuthoring/",
			createActivityAction: "/taoDelivery/DeliveryAuthoring/",
			createInteractiveServiceAction: "/taoDelivery/DeliveryAuthoring/"
		});
	});
	*/
</script>