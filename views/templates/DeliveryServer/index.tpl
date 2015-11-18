<?php
use oat\tao\helpers\Template;

$startedDeliveries = get_data('startedDeliveries');
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
                <?php Template::inc('DeliveryServer/delivery_entry.tpl', null, ['delivery' => $delivery]); ?>
                <?php
                    if (isset($delivery['TAO_DELIVERY_REPETITIONS'])) {
                        foreach ($delivery['TAO_DELIVERY_REPETITIONS'] as $repeatedDelivery) {
                            $data = array_merge($delivery, $repeatedDelivery);
                            unset($data['TAO_DELIVERY_REPETITIONS']);
                            Template::inc('DeliveryServer/delivery_entry.tpl', null, ['delivery' => $data]);
                        }
                    }
                ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
