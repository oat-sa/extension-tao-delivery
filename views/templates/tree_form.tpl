<?$sectionName=get_data("section");?>


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
				//reload the tree
				loadSectionTree("<?=$sectionName?>");
			}
		});
	});
	// alert($("input[id=http%3A%2F%2Fwww__tao__lu%2Fmiddleware%2Ftaoqual__rdf%23118588892919658_8]").attr("name"));
});

</script>
