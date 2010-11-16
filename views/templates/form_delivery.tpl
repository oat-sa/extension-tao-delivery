<?include('header.tpl')?>

<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/form_delivery.css" />
   
<div id="delivery-left-container">
	<?if(get_data('authoringMode') == 'simple'):?>
	
	<?include('delivery_tests.tpl');?>
	<div class="breaker"></div>
	<?endif?>
	
	<?include('groups.tpl')?>
	<?include('subjects.tpl')?>
	<div class="breaker"></div>
	
	<?include('delivery_campaign.tpl')?>
	<div class="breaker"></div>
		
</div>

<div class="main-container" id="delivery-main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
	<!-- compile box not available in standalone mode-->
	<?if(!tao_helpers_Context::check('STANDALONE_MODE')):?>
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
		<?=__("Compilation")?>
	</div>
	<div id="form-compile" class="ui-widget-content ui-corner-bottom">
		<div class="ext-home-container ui-state-highlight">
		<p>
		<?if(get_data('isCompiled')):?>
			<?=__('The delivery was last compiled on')?> <?=get_data('compiledDate')?>.
		<?else:?>
			<?=__('The delivery is not compiled yet')?>
		<?endif;?>
		</p>
		
		<span>
			<a id='compileLink' class='nav' href="<?=BASE_URL.'/Delivery/CompileView?uri='.tao_helpers_Uri::encode(get_data('uri')).'&classUri='.tao_helpers_Uri::encode(get_data('classUri'))?>">
				<img id='compileLinkImg' src="<?=BASE_WWW?>img/compile_small.png"/>
				<?if(get_data('isCompiled')):?>
					<?=__('Recompile')?> 
				<?else:?>
					<?=__('Compile')?>
				<?endif;?>
				
			</a>
		</span>
		
		<br/>
		
		</div>
	</div>
	<?endif;?>
	
	<?include('delivery_history.tpl');?>
	
</div>

<?include('footer.tpl');?>