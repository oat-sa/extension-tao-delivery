<div id="interactiveService-form">
	
	<?=get_data("formInteractionService")?>
	<input type="button" name="submit-interactiveService" id="submit-interactiveService" value="save"/>
</div>

<script type="text/javascript">
$(function(){
	// alert($("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").html());
	$("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").change(function(e){
		// var confirmed = confirm("sure?");
		// alert(confirmed+" and "+JSON.stringify(e));
	});
	
	$("#submit-interactiveService").click(function(){
		$.ajax({
			url: '/taoDelivery/DeliveryAuthoring/editCallOfService',
			type: "POST",
			data: $("#callOfServiceEditor").serialize(),
			// dataType: 'html',
			success: function(response){
				alert(response);
			}
		});
	});
	
	// $('#submit-interactiveService').click(function(){
		// alert($("#callOfServiceEditor").serialize());
	// });
	
});

</script>
