<?php use oat\tao\helpers\Template; ?>
<script id="amd-loader"
        src="<?= Template::js('lib/require.js', 'tao') ?>"
        data-main="<?= Template::js('loader/controller.js', 'taoDelivery'); ?>"
        data-config="<?= get_data('client_config_url') ?>"
        data-controller="taoDelivery/controller/runtime/client/deliveryExecution"
        data-params="<?= json_encode([
            'exitUrl' => get_data('returnUrl'),
            'testDefinition' => get_data('testDefinition'),
            'testCompilation' => get_data('testCompilation'),
            'serviceCallId' => get_data('serviceCallId'),
            'deliveryServerConfig' => get_data('deliveryServerConfig'),
            ]); ?>"
></script>
