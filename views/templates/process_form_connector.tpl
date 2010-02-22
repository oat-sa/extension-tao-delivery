<div id="connector-form">
	
	<?=get_data("formConnector")?>
	<input type="button" name="submit-connector" id="submit-connector-<?=get_data("formId")?>" value="save"/>
</div>

<script type="text/javascript">

$(function(){

	//get the initial selected value, if exists: 
	var initalSelectedValue = $("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE)?>]").val();
	
	// alert($("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").html());
	$("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE)?>]").change(function(e){
		if(confirm(__("Sure?"))){
			
			$("#<?=get_data("formId")?> :INPUT :gt(3)").attr("disabled","disabled");
			$("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE)?>]").removeAttr("disabled");
			$("#<?=get_data("formId")?>").append("<p>reloading form...</p>");
			
			//send the form
			$.ajax({
				url: '/taoDelivery/DeliveryAuthoring/saveConnector',
				type: "POST",
				data: $("#<?=get_data("formId")?>").serialize(),
				dataType: 'json',
				success: function(response){
					if(response.saved){
						var selectedNode = $("#connectorUri").val();
						$("#connector-form").html("connector saved");
						// initActivityTree();
						refreshActivityTree();
						ActivityTreeClass.selectTreeNode(selectedNode);
					}else{
						$("#connector-form").html("save failed:" + response);
					}
				}
			});
		}else{
			//reset the option:
			$("#<?=get_data("formId")?> option[value="+initalSelectedValue+"]").attr("selected","selected");
		}
	});
	
	$("#submit-connector-<?=get_data("formId")?>").click(function(){
		$.ajax({
			url: '/taoDelivery/DeliveryAuthoring/saveConnector',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					var selectedNode = $("#connectorUri").val();
					$("#connector-form").html("connector saved");
					// initActivityTree();
					refreshActivityTree();
					// reselectActivityTree();
					// ActivityTreeClass.selectTreeNode(selectedNode);
				}else{
					$("#connector-form").html("connector save failed:" + response);
				}
			}
		});
	});
	
	if( $("#if").length ){
		//split connector:
		initActivitySwitch('then');
		initActivitySwitch('else');
	}else{
		initActivitySwitch('next');
	}

});

function initActivitySwitch(clazz){
	switchActivityType(clazz);
	$("input:radio[name="+clazz+"_activityOrConnector]").change(function(){switchActivityType(clazz);});
	$("#"+clazz+"_activityUri").change(function(){switchActivityType(clazz);});
}

function switchActivityType(clazz){
	var value = $("input:radio[name="+clazz+"_activityOrConnector]:checked").val();
	if(value == 'connector'){
		disable($("#"+clazz+"_activityUri"));
		disable($("#"+clazz+"_activityLabel"));
		enable($("#"+clazz+"_connectorUri"));
	}else if(value == 'activity'){
		enable($("#"+clazz+"_activityUri"));
		disable($("#"+clazz+"_activityLabel"));
		if($("#"+clazz+"_activityUri").val() == 'newActivity'){
			enable($("#"+clazz+"_activityLabel"));
		}
		disable($("#"+clazz+"_connectorUri"));
	}else{
		disable($("#"+clazz+"_activityUri"));
		disable($("#"+clazz+"_activityLabel"));
		disable($("#"+clazz+"_connectorUri"));
	}
}

function disable(object){
	object.parent().attr("disabled","disabled");
	object.parent().hide();
}

function enable(object){
	object.parent().removeAttr("disabled");
	object.parent().show();
}
</script>