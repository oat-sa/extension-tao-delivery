<div id="interactiveService-form">
	
	<?=get_data("formInteractionService")?>
	<input type="button" name="submit-interactiveService" id="submit-interactiveService" value="save"/>
</div>

<script type="text/javascript">

$(function(){
	//get the initial selected value, if exists: 
	var initalSelectedValue = $("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").val();
	
	// alert($("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").html());
	$("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").change(function(e){
		if(confirm(__("Sure?"))){
			//send the form
			$.ajax({
				url: '/taoDelivery/DeliveryAuthoring/saveCallOfService',
				type: "POST",
				data: $("#<?=get_data("formId")?>").serialize(),
				dataType: 'json',
				success: function(response){
					if(response.saved){
						//call ajax function again to get the new form
						
					}else{
						$("#interactiveService-form").html("save failed:" + response);
					}
				}
			});
		}else{
			//reset the option:
			$("#<?=get_data("formId")?> option[value="+initalSelectedValue+"]").attr("selected","selected");
		}
		// alert(confirmed+" and "+JSON.stringify(e));
	});
	
	$("#submit-interactiveService").click(function(){
		$.ajax({
			url: '/taoDelivery/DeliveryAuthoring/saveCallOfService',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					$("#interactiveService-form").html("saved");
				}else{
					$("#interactiveService-form").html("save failed:" + response);
				}
			}
		});
	});
	
	// $('#submit-interactiveService').click(function(){
		// alert($("#callOfServiceEditor").serialize());
	// });
	
});

</script>
