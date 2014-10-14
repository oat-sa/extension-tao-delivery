<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
<!--<link rel="stylesheet" type="text/css" href="<?=Template::css('form_delivery.css')?>" />-->

<div class="main-container flex-container-main-form">
	<h2><?=get_data('formTitle')?></h2>
	<div id="form-container">
		<?=get_data('myForm')?>
	</div>

<?php if(!tao_helpers_Context::check('STANDALONE_MODE')):?>
    <br>
	<h2><?=__("Publishing")?></h2>
        
    <?php if(get_data('hasContent')):?>
        <a id='compileLink' class='btn btn-info' href="<?=_url('index', 'Compilation', null, array('uri' => get_data('uri'), 'classUri' => get_data('classUri')))?>">
            <span class="icon-link" <?=Template::img('compile_small.png')?>"></span>
                <?=__('Create Delivery')?>
        </a>
    <?php endif;?>
<?php endif;?>
</div>

<div class="data-container-wrapper flex-container-remainer">
   	<?= get_data('contentForm')?>
	<?= has_data('campaign') ? get_data('campaign') : '';?>
</div>

<?php
Template::inc('footer.tpl');
?>
