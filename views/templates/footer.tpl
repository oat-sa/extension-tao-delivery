<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){
	
	setAthoringModeButtons();
	
	UiBootstrap.tabs.bind('tabsshow', function(event, ui) {
		if(ui.index>0){
			$("form[name=form_1]").html('');
		}
	});
	
	<?if(get_data('uri') && get_data('classUri') && (get_data('authoringMode')=='advanced') ):?>
		updateTabUrl(UiBootstrap.tabs, 'delivery_authoring', "<?=_url('authoring', 'Delivery', 'taoDelivery', array('uri' => get_data('uri'), 'classUri' => get_data('classUri') ))?>");
	<?else:?>
		UiBootstrap.tabs.tabs('disable', getTabIndexByName('delivery_authoring'));
	<?endif;?>
	
	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif;?>
	
	EventMgr.bind('actionInitiated', function(event, response){
		setAthoringModeButtons();
	});
});

function setAthoringModeButtons(){
	$('#action_advanced_mode').parent().hide();
	$('#action_simple_mode').parent().hide();
	<?if(get_data('uri') && get_data('classUri')):?> 
		<?if(get_data('authoringMode')=='advanced'):?>
			$('#action_simple_mode').parent().show();
			$('#action_simple_mode').unbind('click');
			$('#action_simple_mode').click(function(e){
				// e.preventDefault();
				if(!confirm('Are you sure to switch back to the simple mode? \n The delivery process will be linearized.')){
					// $(this).find('a').click();
					return false;
				}
				// return false;
			});
		<?else:?>
			$('#action_advanced_mode').parent().show();
		<?endif;?>
	<?endif;?>
}
</script>