<?include('header.tpl')?>

<div id="subject-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select group testees')?>
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
		<?=__('Select group test')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="test-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-test" type="button" value="<?=__('Save')?>" />
	</div>
</div>
	<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom ui-state-default">
		<?=get_data('myForm')?>
	</div>
</div>
<script type="text/javascript">
/*
$(function(){
	new GenerisTreeFormClass('#subject-tree', "/taoGroups/Groups/getMembers", {
		actionId: 'subject',
		saveUrl : '/taoGroups/Groups/saveMembers',
		checkedNodes : <?=get_data('relatedSubjects')?>
	});
	new GenerisTreeFormClass('#test-tree', "/taoGroups/Groups/getTests", {
		actionId: 'test',
		saveUrl : '/taoGroups/Groups/saveTests',
		checkedNodes : <?=get_data('relatedTests')?>
	});
});
*/
</script>

<?include('footer.tpl');?>
