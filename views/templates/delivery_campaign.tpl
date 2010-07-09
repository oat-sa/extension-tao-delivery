<div id="campaign-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select delivery campaign')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="campaign-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-campaign" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(document).ready(function(){
	
	if(ctx_extension){
		url = root_url + '/' + ctx_extension + '/' + ctx_module + '/';
	}
	getUrl = url + 'getCampaigns';
	setUrl = url + 'saveCampaigns';
	new GenerisTreeFormClass('#campaign-tree', getUrl, {
		actionId: 'campaign',
		saveUrl : setUrl,
		checkedNodes : <?=get_data('relatedCampaigns')?>
	});
});
</script>
