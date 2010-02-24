<div id="test-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select delivery')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="delivery-tree<?=get_data('index')?>"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-test<?=get_data('index')?>" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(document).ready(function(){
	
	if(ctx_extension){
		url = '/' + ctx_extension + '/' + ctx_module + '/';
	}
	getUrl = url + 'getDeliveries';
	setUrl = url + 'saveDeliveries';
	new GenerisTreeFormClass('#delivery-tree<?=get_data('index')?>', getUrl, {
		actionId: 'test<?=get_data('index')?>',
		saveUrl : setUrl,
		checkedNodes : <?=get_data('relatedDeliveries')?>
	});
});
</script>

