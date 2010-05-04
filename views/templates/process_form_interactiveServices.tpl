<div id="interactiveService-form">
	
	<?=get_data("formInteractionService")?>
	<div id="servicePositionningPreview"/>
	<input type="button" name="submit-interactiveService" id="submit-interactiveService" value="save"/>
	
</div>

<style type="text/css">
	.serviceEditorSlider{
		float: left;
		clear: left;
		width: 120px;
		height: 5px;
		margin: 15px;
	}
</style>


<script type="text/javascript">

$(function(){
	var otherServices = <?=json_encode(get_data("otherServices"))?>;
	// console.log('otherServices', otherServices);
	
	
	var eltHeight = $("input[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_HEIGHT)?>]");
	var eltWidth = $("input[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_WIDTH)?>]");
	var eltTop = $("input[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_TOP)?>]");
	var eltLeft = $("input[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_LEFT)?>]");
	
	
	//continue only if the four elements exists
	if(eltHeight.length && eltWidth.length && eltTop.length && eltLeft.length){
		
		
		
		//create sliders instead of the textbox:
		// eltHeight.hide();
		$('<div id="heightSlider_container" class="serviceEditorSlider"/>').appendTo(eltHeight.parent());
		$('<div id="heightSlider"/>').appendTo('#heightSlider_container');
		console.log('eltHeight',eltHeight);
		// $("#heightSlider").slider();
		$("#heightSlider").slider({
			orientation: 'horizontal',
			range: "min",
			max: 100,
			value: 10,
			slide: refreshPositionPreview,
			change: refreshPositionPreview
		});
		$("#heightSlider").slider("value", eltHeight.val());
		// $("#green").slider("value", 140);
		// $("#blue").slider("value", 60);

	}
	
	function refreshPositionPreview(){
		eltHeight.val($("#heightSlider").slider("value"));
	}

	//get the initial selected value, if exists: 
	var selectElement = $("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]");
	var initalSelectedValue = selectElement.val();
	
	// alert($("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").html());
	selectElement.change(function(e){
		if(confirm(__("Sure?"))){
			
			// $("#<?=get_data("formId")?> :INPUT :gt(3)").attr("disabled","disabled");
			// selectElement.removeAttr("disabled");
			$("#<?=get_data("formId")?>").append("<p>"+__('reloading form...')+"</p>");
			
			//send the form
			$.ajax({
				url: authoringControllerPath+'saveCallOfService',
				type: "POST",
				data: $("#<?=get_data("formId")?>").serialize(),
				dataType: 'json',
				success: function(response){
					if(response.saved){
						//call ajax function again to get the new form
						ActivityTreeClass.selectTreeNode($("#callOfServiceUri").val());
					}else{
						$("#interactiveService-form").html("save failed:" + response);//debug
					}
				}
			});
		}else{
			//reset the option:
			$("#<?=get_data("formId")?> option[value="+initalSelectedValue+"]").attr("selected","selected");
		}
		
	});
	
	$("#submit-interactiveService").click(function(){
		$.ajax({
			url: authoringControllerPath+'saveCallOfService',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					$("#interactiveService-form").html(__("interactive service saved"));
					refreshActivityTree();
				}else{
					$("#interactiveService-form").html("interactive service save failed:" + response);//debug
				}
			}
		});
	});
		
});

</script>
