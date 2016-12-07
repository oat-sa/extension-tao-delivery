<?php
use oat\tao\helpers\Layout;
use oat\tao\helpers\Template;
?>

<?= Layout::getAmdLoader( Template::js('loader/index.min.js', 'taoDelivery'), 'taoDelivery/controller/DeliveryServer/index', get_data('parameters')) ?>
