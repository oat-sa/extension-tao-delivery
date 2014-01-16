<?include('header.tpl')?>

<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/form_delivery.css" />

<div id="delivery-left-container">
   	<?= get_data('contentForm')?>
	<?=get_data('groupTree')?>
	<?=get_data('groupTesttakers')?>
	<?= has_data('campaign') ? get_data('campaign') : '';?>
	<div class="breaker"></div>
</div>

<div class="main-container medium" id="delivery-main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
	<!-- compile box not available in standalone mode-->
	<?if(!tao_helpers_Context::check('STANDALONE_MODE')):?>
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
		<?=__("Publishing Status")?>
	</div>
	<div id="form-compile" class="ui-widget-content ui-corner-bottom">
		<div class="ext-home-container <?php if(get_data('hasContent') && !get_data('isCompiled')):?>ui-state-highlight <?php endif;?>ui-state-highlight-delivery">
		<p>
		<? $assemblies = get_data('assemblies'); ?>
		<?if(!empty($assemblies)):?>
    		<ul id="lb" class="listbox">
    		<?php foreach (get_data('assemblies') as $assembly) : ?>
    		    <li><?=__('%1s published on %2s',$assembly['label'],$assembly['date'])?> <span class="icon-download compilationButton" style="cursor: pointer;" data-uri="<?=$assembly['uri']?>"></span></li>
    		<?php endforeach;?>
    		</ul>
		<?else:?>
			<?=__('Not yet published')?>
		<?endif;?>
		</p>

		<span>
			<?if(get_data('hasContent')):?>
	            <a id='compileLink' class='nav' href="<?=BASE_URL.'Compilation/index?uri='.tao_helpers_Uri::encode(get_data('uri')).'&classUri='.tao_helpers_Uri::encode(get_data('classUri'))?>">
                    <img id='compileLinkImg' src="<?=BASE_WWW?>img/compile_small.png"/>
    				<?if(!empty($assemblies)):?>
    					<?=__('Publish again')?>
    				<?else:?>
    					<?=__('Publish')?>
    				<?endif;?>
                </a>
			<?endif;?>
		</span>

		<br/>

		</div>
	</div>
	<?endif;?>

	<?include('delivery_history.tpl');?>

</div>

<script type="text/javascript">
$(function(){
	require(['jquery'], function($) {
		$('.compilationButton').click(function(){
    		$.ajax({
    			url: "<?=get_data('exportUrl')?>",
    			type: "POST",
    			data: {'uri': $(this).data('uri')},
    			dataType: 'json',
    			success: function(response) {
    				if (response.success) {
    					window.location.href = response.download; 
    				}
			    }
    		});			
		});
	});
		
});
</script>
<?include('footer.tpl');?>