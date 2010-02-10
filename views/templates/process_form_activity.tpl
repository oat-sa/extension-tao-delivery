<?$sectionName = "activity-property"?>

<div id="<?=$sectionName?>-form">
	<?if(!get_data("saved")):?>
		<?=get_data("myForm")?>
		<input type="button" name="submit-<?=$sectionName?>" id="submit-<?=$sectionName?>" value="save"/>
	
		<script type="text/javascript">
			$(function(){
				//edit the id of the tag of uri:
				$("input[id=uri]").attr("name","activityUri");

				//change to submit event interception would be "cleaner" than adding a button
				$("#submit-<?=$sectionName?>").click(function(){
					$.ajax({
						url: '/taoDelivery/DeliveryAuthoring/editActivityProperty',
						type: "POST",
						data: $("#<?=$sectionName?>-form :input").serialize(),
						dataType: 'html',
						success: function(response){
							$("#<?=$sectionName?>-form").html(response);
						}
					});
				});
			});
		</script>
	<?else:?>
		<p>Activity property saved</p>
	<?endif;?>
</div>


