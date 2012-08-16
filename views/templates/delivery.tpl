<div id="test-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select delivery')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="delivery-tree<?=get_data('index')?>"></div>
		<div class="breaker"></div>
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
	if (ctx_extension) {
		url = root_url + '/' + ctx_extension + '/' + ctx_module + '/';
	}

	require(['require', 'jquery', 'generis.tree.select'], function(req, $, GenerisTreeSelectClass) {
		new GenerisTreeSelectClass('#delivery-tree<?=get_data('index')?>', url + 'getDeliveries', {
			actionId: 'test<?=get_data('index')?>',
			saveUrl : url + 'saveDeliveries',
			checkedNodes : <?=get_data('relatedDeliveries')?>
		});
	});
});
</script>

