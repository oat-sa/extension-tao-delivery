<?php
$delivery = get_data('delivery');
$url = ($delivery["TAO_DELIVERY_TAKABLE"]) ? _url(
    'initDeliveryExecution',
    'DeliveryServer',
    null,
    array('uri' => $delivery[CLASS_COMPILEDDELIVERY]->getUri())
) : '#';
?>
<li>
    <a class="block entry-point entry-point-all-deliveries <?= ($delivery["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled" ?>" href="<?= $url ?>">
        <h3><?= _dh($delivery[CLASS_COMPILEDDELIVERY]->getLabel()) ?></h3>

        <p><?php if(!empty($delivery[TAO_DELIVERY_START_PROP])) : ?>
                <?= __('Available from %s', tao_helpers_Date::displayeDate($delivery[TAO_DELIVERY_START_PROP])); ?>
            <?php endif; ?>

            <?php if (!empty($delivery[TAO_DELIVERY_END_PROP])) : ?>
                <?= __('to %s', tao_helpers_Date::displayeDate($delivery[TAO_DELIVERY_END_PROP])); ?>
            <?php endif; ?>
        </p>
        <p><?php if($delivery[TAO_DELIVERY_MAXEXEC_PROP] !== ''): ?>
                <?= $delivery[TAO_DELIVERY_MAXEXEC_PROP] === 1 ? __('Attempt') : __('Attempts') ?>
                <?=  __('%s of %s', $delivery["TAO_DELIVERY_USED_TOKENS"],
                    !empty($delivery[TAO_DELIVERY_MAXEXEC_PROP])
                        ? $delivery[TAO_DELIVERY_MAXEXEC_PROP]
                        : __('unlimited'));
                ?>
            <?php endif; ?>
        </p>

        <div class="clearfix">
            <span class="text-link" href="<?= $url ?>"><span class="icon-play"></span> <?= __('Start') ?> </span>
        </div>
    </a>
</li>
