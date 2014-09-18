<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
<link rel="stylesheet" type="text/css" href="<?= Template::css('form_delivery.css')?>" />

<div class="tao-scope grid-container">
    <div class="grid-row">
        <div class="col-12 ui-state-default topbox">
            <span style="font-style: italic">
        	   <?=__('%1s has been published on the %2s', get_data('label'), tao_helpers_Date::displayeDate(get_data('date')))?>
        	</span>
        </div>
    </div>
    
    <div class="grid-row">
        <div class="col-12">
    	   <div id="form-title-history" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
            	<?=__("Attempts")?>
            </div>
            <div id="form-history" class="ui-widget-content ui-corner-bottom">
            	<div id="history-link-container" class="ext-home-container">
            	    <?php if(has_data('exec')):?>
                		<p>
                		<?php if(get_data('exec') == 0):?>
                			<?=__('No attempt has been started yet.')?>
                		<?php elseif(get_data('exec') == 1) :?>
                			<?=__('There is currently 1 attempt')?>.
            			<?php else:?>
                            <?=__('There are currently %s attempts', get_data('exec'))?>.
            			<?php endif;?>
            		    </p>
        		    <?php else:?>
                        <?=__('No information available')?>.
        			<?php endif;?>
            	</div>
            	<div>
            		<table id="history-list"></table>
            		<div id="history-list-pager"></div>
            	</div>
            </div>
        </div>
    </div>
    
    <div class="grid-row">
        <div class="col-6">
            <h2>
        		<?=get_data('formTitle')?>
        	</h2>
        	<div class="form-content">
        		<?=get_data('myForm')?>
        	</div>
        </div>
        <div class="col-6">
            <?=get_data('groupTree')?>
            <?php Template::inc('widget_exclude.tpl');?>
            <?= has_data('campaign') ? get_data('campaign') : '';?>
        </div>
    </div>
</div>
<?php
Template::inc('footer.tpl');
?>