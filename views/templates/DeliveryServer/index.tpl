<?php
use oat\tao\helpers\Template;

print Template::inc('DeliveryServer/blocks/header.tpl');
?>
    <div id="content">
        <h1>
            <span class="icon-delivery"></span>
            <?= __("My Tests"); ?>
        </h1>
        <?php if (count(get_data('startedDeliveries')) > 0) : ?>
            <div class="header">
                <?php echo __("Paused Tests"); ?>
                <span class="counter">(<?php echo count($startedDeliveries); ?>)</span>
            </div>
            <div class="deliveries resume">
                <?php foreach ($startedDeliveries as $deliveryExecution): ?>
                    <div class="tile clearfix">
                        <div class="tileLabel">
                            <?= _dh($deliveryExecution->getLabel()) ?>
                        </div>
                        <div class="tileDetail">
                            <?php echo __("Started at "); ?><?php echo tao_helpers_Date::displayeDate(
                                $deliveryExecution->getStartTime()
                            ); ?>
                        </div>
                        <a class="btn-info small rgt" href="<?= _url(
                            'runDeliveryExecution',
                            'DeliveryServer',
                            null,
                            array('deliveryExecution' => $deliveryExecution->getIdentifier())
                        ) ?>">
                            <?php echo __("Resume"); ?><span class="icon-continue r"></span>

                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (count(get_data('availableDeliveries')) > 0) : ?>
            <div class="header">
                <?php echo __("Assigned Tests"); ?> <span class="counter">(<?php echo count($availableDeliveries); ?>
                    )</span>
            </div>
            <div class="deliveries start">
                <?php foreach ($availableDeliveries as $delivery) : ?>
                    <div class="tile clearfix">
                        <div class="tileLabel">
                            <?= _dh($delivery["compiledDelivery"]->getLabel()) ?>
                        </div>
                        <div class="tileDetail">
                            <?php if ($delivery["settingsDelivery"][TAO_DELIVERY_START_PROP] != "") { ?>
                                Available from <?php echo tao_helpers_Date::displayeDate(
                                    @$delivery["settingsDelivery"][TAO_DELIVERY_START_PROP]
                                ); ?>
                            <?php } ?>
                            <?php if ($delivery["settingsDelivery"][TAO_DELIVERY_END_PROP] != "") { ?>
                                <br/>until <?php echo tao_helpers_Date::displayeDate(
                                    $delivery["settingsDelivery"][TAO_DELIVERY_END_PROP]
                                ); ?>
                            <?php } ?>
                        </div>
                        <div class="tileDetail">
                            <?php echo __('Attempt(s)'); ?>
                            [ <?php echo $delivery["settingsDelivery"]["TAO_DELIVERY_USED_TOKENS"]; ?>
                            / <?php echo ($delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP] != 0) ? $delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP] : __(
                                'Unlimited'
                            ); ?> ]
                        </div>
                        <a accesskey="" class="btn-info small rgt <?= ($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled" ?>"
                           href="<?= ($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? _url(
                               'initDeliveryExecution',
                               'DeliveryServer',
                               null,
                               array('uri' => $delivery["compiledDelivery"]->getUri())
                           ) : '#' ?>">
                            <?php echo __("Start"); ?><span class="icon-play r"></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?= Template::inc('DeliveryServer/blocks/footer.tpl'); ?>