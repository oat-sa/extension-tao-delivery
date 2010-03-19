<div id="inferenceRule-form">
	
	<?=get_data("formInferenceRule")?>
	<input type="button" name="submit-inferenceRule" id="submit-inferenceRule-<?=get_data("formId")?>" value="save"/>
</div>

<script type="text/javascript">

$(function(){
		
	$("#submit-inferenceRule-<?=get_data("formId")?>").click(function(){
		$.ajax({
			url: authoringControllerPath+'saveInferenceRule',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					$("#connector-form").html("connector saved");
					refreshActivityTree();
				}else{
					$("#connector-form").html("connector save failed:" + response);
				}
			}
		});
	});
	
	// $("input:radio[name=else_choice]").change(function(){switchType();});
	$("input:radio[name=else_choice]").change(function(){
		if($("input:radio[name=else_choice]:checked").val() == 'assignment'){
			enable($("#else_assignment"));
		}else{
			disable($("#else_assignment"));
		}
	});
});

// function initSwitch(){
	
// }
function switchType(){
	var value = $("input:radio[name=else_choice]").val();
	alert(value);
	if($("input:radio[name=else_choice]").val() == 'assignment'){
		enable($("#else_assignment"));
	}else{
		disable($("#else_assignment"));
	}
}

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