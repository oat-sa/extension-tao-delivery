<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){

	UiBootstrap.tabs.bind('tabsshow', function(event, ui) {
		if(ui.index>0){
			$("form[name=form_1]").html('');
		}
	});
	
	<?if(get_data('action') != 'authoring'):?>
		<?if(get_data('uri') && get_data('classUri')):?>
		index = getTabIndexByName('delivery_authoring');
		UiBootstrap.tabs.tabs('url', index, "/taoTests/Tests/authoring?uri=<?=get_data('uri')?>&classUri=<?=get_data('classUri')?>");
		UiBootstrap.tabs.tabs('enable', index);
		<?else:?>
			UiBootstrap.tabs.tabs('disable', getTabIndexByName('delivery_authoring'));
		<?endif?>
	<?endif?>
	
	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>

});
</script>