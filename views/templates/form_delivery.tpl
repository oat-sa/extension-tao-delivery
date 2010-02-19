<?include('header.tpl')?>

<div id="subject-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select testees to be <b>excluded</b>')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="subject-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-subject" type="button" value="<?=__('Save')?>" />
	</div>
</div>

<div id="test-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select delivery campaign')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="campaign-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-campaign" type="button" value="<?=__('Save')?>" />
	</div>
</div>

<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<script type="text/javascript">

$(function(){
	new GenerisTreeFormClass('#subject-tree', "/taoDelivery/Delivery/getSubjects", {
		actionId: 'subject',
		saveUrl : '/taoDelivery/Delivery/saveSubjects',
		checkedNodes : <?=get_data('excludedSubjects')?>
	});
	new GenerisTreeFormClass('#campaign-tree', "/taoDelivery/Delivery/getCampaigns", {
		actionId: 'campaign',
		saveUrl : '/taoDelivery/Delivery/saveCampaigns',
		checkedNodes : <?=get_data('relatedCampaigns')?>
	});
	
	/*
	//test form ajax
	uri="http%3A%2F%2F127__0__0__1%2Fmiddleware%2Fdemo__rdf%23i1264519278021093500";
	classUri="http%3A%2F%2Fwww__tao__lu%2FOntologies%2FTAODelivery__rdf%23Delivery";
	 $.ajax({
	   type: "POST",
	   url: "/taoDelivery/Delivery/formPlus",
	   data: {uri: uri, classUri: classUri},
	   success: function(msg){
			$("#form-container2").html(msg);
	   }
	 });
	 */
	 
	 
});

</script>

<script type="text/javascript">
/*
$(function(){
	// $("textarea").attr("rows","50");
	// $("textarea").attr("cols","80");
	$("div.xhtml_form div textarea").css("height",500);
	$("div.xhtml_form div textarea").css("width",420);
	$("div.main-container").css("height",550);
	// alert($("textarea").attr("cols"));
});
*/
$("div.main-container").css("height",550);

		
</script>

<?include('footer.tpl');?>
