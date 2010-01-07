<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){

	tabs.bind('tabsshow', function(event, ui) {
		if(ui.index>0){
			$("form[name=form_1]").html('');
		}
	});

	<?if(get_data('reload') === true):?>	
		
	loadControls();
	
	<?else:?>
	
	initActions();
	
	<?endif?>
	
	
});
</script>