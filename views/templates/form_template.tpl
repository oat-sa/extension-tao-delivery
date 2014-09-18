<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
<link rel="stylesheet" type="text/css" href="<?=Template::css('form_delivery.css')?>" />

<div id="delivery-left-container">
   	<?= get_data('contentForm')?>
	<?= has_data('campaign') ? get_data('campaign') : '';?>
	<div class="breaker"></div>
</div>

<div class="main-container">
	<h2><?=get_data('formTitle')?></h2>
	<div class="form-content">
        <?=get_data('myForm')?>
	</div>
	<!-- compile box not available in standalone mode-->
	<?php if(!tao_helpers_Context::check('STANDALONE_MODE')):?>
	<h2<?=__("Publishing")?></h2>
	<div class="form-content">
		<div class="ext-home-container ui-state-highlight ui-state-highlight-delivery">

		<span>
			<?php if(get_data('hasContent')):?>
	            <a id='compileLink' class='nav' href="<?=_url('index', 'Compilation', null, array('uri' => get_data('uri'), 'classUri' => get_data('classUri')))?>">
                    <img id='compileLinkImg' src="<?=Template::img('compile_small.png')?>"/>
    					<?=__('Create Delivery')?>
                </a>
			<?php endif;?>
		</span>

		<br/>

		</div>
	</div>
	<?php endif;?>
	
</div>

<script>
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
<?php
Template::inc('footer.tpl');
?>