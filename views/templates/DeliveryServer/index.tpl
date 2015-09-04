<?php
use oat\tao\helpers\Template;

print Template::inc('DeliveryServer/blocks/header.tpl');
?>
    <div class="test-listing">
        <h1><?= __("My Tests"); ?></h1>

        <?= Template::inc('DeliveryServer/blocks/started-deliveries.tpl'); ?>

        <?= Template::inc('DeliveryServer/blocks/available-deliveries.tpl'); ?>

    </div>
<?= Template::inc('DeliveryServer/blocks/footer.tpl'); ?>
