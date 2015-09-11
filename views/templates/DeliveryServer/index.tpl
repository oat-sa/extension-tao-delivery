<?php
$startedDeliveries     = get_data('startedDeliveries');
$availableDeliveries = get_data('availableDeliveries');
?>
<div class="test-listing">
    <h1><?= __("My Tests"); ?></h1>
    <?php if (count($startedDeliveries) > 0) : ?>
        <h2 class="info">
            <?= __("In progress") ?>: <?= count($startedDeliveries); ?>
        </h2>

        <ul class="entry-point-box plain">
            <?php foreach ($startedDeliveries as $deliveryExecution): ?>
                <?php $url = _url('runDeliveryExecution', 'DeliveryServer', null, array('deliveryExecution' => $deliveryExecution->getIdentifier())); ?>
                <li>
                    <a class="block entry-point entry-point-started-deliveries" href="<?= $url ?>">
                        <h3><?= _dh($deliveryExecution->getLabel()) ?></h3>

                        <p><?php echo __("Started at "); ?><?php echo tao_helpers_Date::displayeDate(
                                $deliveryExecution->getStartTime()
                            ); ?>
                        </p>

                        <div class="clearfix">

                            <span class="text-link" href="<?= $url ?>"><span class="icon-continue"></span> <?= __("Resume") ?> </span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (count($availableDeliveries) > 0) : ?>
        <h2>
            <?= __("Available") ?>: <?= count($availableDeliveries); ?>
        </h2>
        <ul class="entry-point-box plain">
            <?php foreach ($availableDeliveries as $delivery) : ?>
                <?php $url = ($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? _url(
                    'initDeliveryExecution',
                    'DeliveryServer',
                    null,
                    array('uri' => $delivery["compiledDelivery"]->getUri())
                ) : '#'?>
                <li>
                    <a class="block entry-point entry-point-all-deliveries <?= ($delivery["settingsDelivery"]["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled" ?>" href="<?= $url ?>">
                        <h3><?= _dh($delivery["compiledDelivery"]->getLabel()) ?></h3>

                        <p><?php if(!empty($delivery["settingsDelivery"][TAO_DELIVERY_START_PROP])) : ?>
                                <?= __('Available from %s', tao_helpers_Date::displayeDate($delivery["settingsDelivery"][TAO_DELIVERY_START_PROP])); ?>
                            <?php endif; ?>

                            <?php if (!empty($delivery["settingsDelivery"][TAO_DELIVERY_END_PROP])) : ?>
                                <?= __('to %s', tao_helpers_Date::displayeDate($delivery["settingsDelivery"][TAO_DELIVERY_END_PROP])); ?>
                            <?php endif; ?>
                        </p>
                        <p><?php if($delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP] !== ''): ?>
                                <?= $delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP] === 1 ? __('Attempt') : __('Attempts') ?>
                                <?=  __('%s of %s', $delivery["settingsDelivery"]["TAO_DELIVERY_USED_TOKENS"],
                                    !empty($delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP])
                                        ? $delivery["settingsDelivery"][TAO_DELIVERY_MAXEXEC_PROP]
                                        : __('unlimited'));
                                ?>
                            <?php endif; ?>
                        </p>

                        <div class="clearfix">

                            <span class="text-link" href="<?= $url ?>"><span class="icon-play"></span> <?= __('Start') ?> </span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
