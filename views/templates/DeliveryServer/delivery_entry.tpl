<?php
use oat\taoDelivery\helper\Delivery;
$delivery = get_data('delivery');
?>
<li>
    <div class="block entry-point entry-point-all-deliveries <?= ($delivery["TAO_DELIVERY_TAKABLE"]) ? "" : "disabled"?>"
        data-launch_url="<?= ($delivery["TAO_DELIVERY_TAKABLE"]) ? $delivery[Delivery::LAUNCH_URL] : "#" ?>"
        tabindex="-1">
        <h3><?= _dh($delivery[Delivery::LABEL]) ?></h3>

        <?php foreach ($delivery[Delivery::DESCRIPTION] as $desc) : ?>
        <p><?= $desc?></p>
        <?php endforeach; ?>
        <div class="clearfix">
            <span
                class="action"
                tabindex="0"
                role="button"
                aria-label="<?= __('Start this test')?>"
            >
                <span class="icon-play"></span> <?= __('Start') ?>
            </span>
        </div>
    </div>
</li>
