<?$sectionName = get_data("sectionName");//should be either activity or process?>

<div id="<?=$sectionName?>-property-form">
	<?if(!get_data("saved")):?>
		<?=get_data("myForm")?>
		<input type="button" name="submit-<?=$sectionName?>-property" id="submit-<?=$sectionName?>-property" value="save"/>
	
		<script type="text/javascript">
			$(function(){
				//edit the id of the tag of uri:
				$("#<?=$sectionName?>-property-form input[id=uri]").attr("name","<?=$sectionName?>Uri");

				//change to submit event interception would be "cleaner" than adding a button
				$("#submit-<?=$sectionName?>-property").click(function(){
					$.ajax({
						url: '/taoDelivery/DeliveryAuthoring/edit<?=ucfirst($sectionName)?>Property',
						type: "POST",
						data: $("#<?=$sectionName?>-property-form :input").serialize(),
						dataType: 'html',
						success: function(response){
							$("#<?=$sectionName?>-property-form").html(response);
							<?if($sectionName=="process"):?>
							processProperty();
							<?endif;?>
						}
					});
				});
			});
		</script>
	<?else:?>
		<p><?=ucfirst($sectionName)?> property saved</p>
	<?endif;?>
</div>


