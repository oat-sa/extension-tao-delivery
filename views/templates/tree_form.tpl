<?$sectionName=get_data("section");?>
<?$sectionName="serviceDefinition";//for test only?>

<div id="<?=$sectionName?>-form">
	<?=get_data("formPlus")?>
	<input type="button" name="submit-<?=$sectionName?>" id="submit-<?=$sectionName?>" value="save"/>
</div>
<script type="text/javascript">
$(function(){
	//change to submit event interception would be "cleaner" than adding a button
	$("#submit-<?=$sectionName?>").click(function(){
		// alert($("#<?=$sectionName?> :input").serialize());
		$.ajax({
			url: '/taoDelivery/DeliveryAuthoring/editInstance',
			type: "POST",
			data: $("#<?=$sectionName?>-form :input").serialize(),
			dataType: 'html',
			success: function(response){
				$("#<?=$sectionName?>-form").html(response);
				//if ok, then reload the tree!!
				loadSectionTree("<?=$sectionName?>");
			}
		});
	});
	
});

</script>
