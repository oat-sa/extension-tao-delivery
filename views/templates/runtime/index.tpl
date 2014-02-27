<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xml:lang="<?=tao_helpers_I18n::getLangCode()?>"
      lang="<?=tao_helpers_I18n::getLangCode()?>">
    <head>
        <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
        <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/tao-main-style.css"/>
        <link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>taoDelivery/views/css/deliveryServerIndex.css"/>
        <link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css" />
        <script src="<?=TAOBASE_WWW?>js/lib/require.js"></script>
        <script id='amd-loader' 
                type="text/javascript" 
                src="<?=TAOBASE_WWW?>js/lib/require.js" 
                data-main="<?=BASE_WWW?>js/controller/runtime/index"
        data-config="<?=get_data('client_config_url')?>"></script>

    </head>
    <body class="tao-scope">
        <ul id="control" >
            <li>
                <span class="icon-test-taker" /> <?php echo get_data('login'); ?>
            </li>
            <li>
                <a id="logout" href="<?=_url('logout', 'DeliveryServer')?>">
                    <span class="icon-logout"><?php echo __("Logout"); ?>
                </a>
            </li>
        </ul>


        <div id="content">
            <!--
            <div class="contentHeader">
              <?php echo __("Welcome"); ?> <?php echo $login; ?>!
            </div>
            !-->
            <?php if(count(get_data('startedDeliveries')) > 0) : ?>
            <div class="header">
                <?php echo __("Paused Tests"); ?> 
                <span class="counter">(<?php echo count($startedDeliveries); ?>)</span>
            </div>
            <div class="deliveries resume">

                <?php foreach ($startedDeliveries as $deliveryExecution): ?>

                <div class="grid-row">
                    
                    <div class="deliveryLabel col-2">
                        <?php 
                        echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($deliveryExecution->getLabel());
                        ?>
                    </div>
                    <div class="validPeriod col-8">
                        <?php echo __("Started at "); ?><?php echo tao_helpers_Date::displayeDate($deliveryExecution->getStartTime()); ?>
                    </div>
                    <div class="col-2">   
                        <a href="<?=_url('runDeliveryExecution', 'DeliveryServer', null, array('deliveryExecution' => $deliveryExecution->getIdentifier()))?>">
                            <button class="btn-info small" type="button">
                                <?php echo __("Resume"); ?><span class=" icon-right r"></span>
                            </button>
                        </a>
                    </div>
                </div>

                <?php endforeach;  ?>
            </div>
            <?php endif; ?>

            <!-- End of Active Processes -->
            <?php if(count(get_data('availableDeliveries')) > 0) : ?>
            <div class="header">
                <?php echo __("Assigned Tests"); ?> <span class="counter">(<?php echo count($availableDeliveries); ?>)</span>
            </div>
            <div class="deliveries start">
                <?php foreach($availableDeliveries as $delivery) : ?>
                <div class="grid-row">
                    <div class="deliveryLabel col-2">
                        <span class="deliveryLabel">
                            <?php echo wfEngine_helpers_GUIHelper::sanitizeGenerisString($delivery["compiledDelivery"]->getLabel()); ?>
                        </span>
                    </div>
                    <div class="validPeriod col-4">
                        <?php if ($delivery["settingsDelivery"][TAO_DELIVERY_START_PROP] != "") {?>
                                          Available from
                                              <?php echo $delivery["settingsDelivery"][TAO_DELIVERY_START_PROP]; ?>
                                          <?php }?>
                        <?php if ($delivery["settingsDelivery"][TAO_DELIVERY_END_PROP] != "") {?>
                                          until <?php echo $delivery["settingsDelivery"][TAO_DELIVERY_END_PROP]; ?>
                                          <?php }?>
                    </div>
                    <div class="tokens col-4">
                         <?php echo __('Attempt(s)');?> [ <?php echo $delivery["settingsDelivery"]["TAO_DELIVERY_USED_TOKENS"]; ?> / <?php echo ($delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP]!=0) ? $delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP] : __('Unlimited'); ?> ]
                    </div>
                    
                    <div class="col-2"> 
                        <a accesskey="" 
                           href="<?=($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? _url('initDeliveryExecution', 'DeliveryServer', null, array('uri' => $delivery["compiledDelivery"]->getUri())) : '#'?>" >
                           <button class="btn-info small <?= ($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled" ?>" type="button">
                                <?php echo __("Start"); ?><span class="icon-right r" />
                            </button>
                        </a>
                    </div>
            </div>
            <?php endforeach;  ?>

        </div>
        <?php endif; ?>

        <!-- End of New Processes -->
        <!--
        <?php if(count(get_data('finishedDeliveries')) > 0) : ?>
                 <div class="header">
                <?php echo __("Completed Tests"); ?> <span class="counter">(<?php echo count($finishedDeliveries); ?>)</span>
                 </div>
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
        !-->
    </div>
    <!-- End of content -->
    <!--<? include TAO_TPL_PATH .'layout_footer.tpl';?>!-->
</body>
</html>