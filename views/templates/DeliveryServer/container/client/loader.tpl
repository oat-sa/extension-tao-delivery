<?php
use oat\tao\helpers\Template;
?>
<script src="<?= Template::js('lib/require.js', 'tao') ?>"></script>
<script>
    (function() {
        requirejs.config({waitSeconds: <?=get_data('client_timeout')?>});
        require(['<?=get_data('client_config_url')?>'], function () {
            require(['taoDelivery/controller/runtime/client/deliveryExecution'], function(deliveryExecution) {
                deliveryExecution.start({
                    exitUrl: '<?=get_data('returnUrl')?>',
                    finishUrl: '<?=get_data('finishUrl')?>',
                    deliveryExecution: '<?=get_data('deliveryExecution')?>',
                    deliveryServerConfig: <?= json_encode(get_data('deliveryServerConfig')) ?>
                });
            });
        });
    }());
</script>
