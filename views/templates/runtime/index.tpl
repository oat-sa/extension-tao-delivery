<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="<?=tao_helpers_I18n::getLangCode()?>"
    lang="<?=tao_helpers_I18n::getLangCode()?>">
<head>
    <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
    <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
    <link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/main.css" media="screen" />
    <script id='amd-loader' 
                type="text/javascript" 
                src="<?=TAOBASE_WWW?>js/lib/require.js" 
                data-main="<?=BASE_WWW?>js/controller/runtime/index"
                data-config="<?=get_data('client_config_url')?>"></script>
    
</head>
<body>
    <div id="process_view"></div>

    <ul id="control">
        <li>
	    <span id="connecteduser" class="icon" ><?php echo __("Logged in as:"); ?>
		<span
                id="username"><?php echo $login; ?></span>
		</span>
	    <span class="separator"></span>
	</li>
        <li><a id="logout" class="icon action"
            href="<?=_url('logout', 'DeliveryServer')?>"><?php echo __("Log out"); ?></a>
        </li>
    </ul>
    
    <div id="content">
        <div class="contentHeader">
	  <?php echo __("Welcome"); ?> <?php echo $login; ?>!
	</div>
        <div id="business" class="deliveries ">
	    <?php if(count(get_data('startedDeliveries')) > 0) : ?>
		<h2 class="section_title"><?php echo __("Paused Tests"); ?> <span class="counter">(<?php echo count($startedDeliveries); ?>)</span></h2>
		<div id="running_process" class="deliveries resume">
		    <ul>
		    <?php foreach ($startedDeliveries as $deliveryExecution): ?>
			<li>
			   
			    <a
			    href="<?=_url('runDeliveryExecution', 'DeliveryServer', null, array('deliveryExecution' => $deliveryExecution->getIdentifier()))?>">
				<span class="deliveryLabel">
			     <?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($deliveryExecution->getLabel()); ?>
				</span>
				<span class="actionsBox">
				    <span class="button">
				    <?php echo __("Resume Test"); ?>
				     </span>
				     <span class="validPeriod">
					 <?php echo __("Started at "); ?><?php echo tao_helpers_Date::displayeDate($deliveryExecution->getStartTime()); ?>
				    </span>
				</span>
			    </a>
			</li>
		    <?php endforeach;  ?>
		    </ul>
		</div>
	    <?php endif; ?>

	    <!-- End of Active Processes -->
	    <?php if(count(get_data('availableDeliveries')) > 0) : ?>
		<h2 class="section_title"><?php echo __("Assigned Tests"); ?> <span class="counter">(<?php echo count($availableDeliveries); ?>)</span></h2>
		<div id="new_process" class="deliveries start">
		    <ul>
			<?php foreach($availableDeliveries as $delivery) : ?>
			<li>
			    
                            <a accesskey="" 
                                href="<?=($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? _url('initDeliveryExecution', 'DeliveryServer', null, array('uri' => $delivery["compiledDelivery"]->getUri())) : '#'?>" >
				
                                <span class="deliveryLabel">
				<?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($delivery["compiledDelivery"]->getLabel()); ?>
				    

				</span>
				<span class="tokens">
					<?php echo __('Attempt(s)');?> [ <?php echo $delivery["settingsDelivery"]["TAO_DELIVERY_USED_TOKENS"]; ?> / <?php echo ($delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP]!=0) ? $delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP] : __('Unlimited'); ?> ]
				    </span>
				<span class="actionsBox">
				    
				    <span class="button <?= ($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled" ?>">
					<?php echo __("Take Test"); ?>
				    </span>
				    
				      <span class="validPeriod">
						<?php if ($delivery["settingsDelivery"][TAO_DELIVERY_START_PROP] != "") {?>
						Available from
						    <?php echo $delivery["settingsDelivery"][TAO_DELIVERY_START_PROP]; ?>
						<?php }?>
						<?php if ($delivery["settingsDelivery"][TAO_DELIVERY_END_PROP] != "") {?>
						until <?php echo $delivery["settingsDelivery"][TAO_DELIVERY_END_PROP]; ?>
						<?php }?>
				    </span>
				</span>
			    </a>
			</li>
			<?php endforeach;  ?>
		    </ul>
		</div>
	    <?php endif; ?>

	    <!-- End of New Processes -->



	    <?php if(count(get_data('finishedDeliveries')) > 0) : ?>
		    <h2 class="section_title"><?php echo __("Completed Tests"); ?> <span class="counter">(<?php echo count($finishedDeliveries); ?>)</span></h2>
		    <div id="old_process" class="deliveries finished">
			<ul>
			<?php foreach($finishedDeliveries as $delivery) : ?>
			<li>
				<?php echo $delivery->getLabel(); ?>
			</li>
			<?php endforeach;  ?>
			</ul>
		    </div>
	    <?php endif; ?>

	    </div>

    </div>
    <!-- End of content -->
<? include TAO_TPL_PATH .'layout_footer.tpl';?>
</body>
</html>