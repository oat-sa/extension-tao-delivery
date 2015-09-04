<?php if (count(get_data('startedDeliveries')) > 0) : ?>
<h2 class="info">
    <?= __("In progress") ?>: <?= count(get_data('startedDeliveries')); ?>
</h2>

<ul class="entry-point-box plain">
    <?php foreach (get_data('startedDeliveries') as $deliveryExecution): ?>
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
