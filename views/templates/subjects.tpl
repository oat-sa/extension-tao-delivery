<div id="subject-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select test takers to be <b>excluded</b>')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="subject-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-subject" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(function(){
	require(['require', 'jquery', 'generis.tree.select'], function(req, $, GenerisTreeSelectClass) {
		if (ctx_extension) {
			url = root_url + ctx_extension + '/' + ctx_module + '/';
		}

		new GenerisTreeSelectClass('#subject-tree', url + 'getSubjects', {
			actionId: 'subject',
			saveUrl: url + 'saveSubjects',
			checkedNodes: <?=get_data('excludedSubjects')?>,
			paginate: 10
		});
	});
});
</script>