<?php
use oat\tao\helpers\Template;
print Template::inc('DeliveryServer/blocks/header.tpl', 'taoDelivery');
?>
    <div class="section-container">
        <div class="clear content-wrapper content-panel">
            <section class="content-container">

                <div class="content-block iframe-block" id="outer-delivery-iframe-container">

                    <iframe id="iframeDeliveryExec" class="toolframe" frameborder="0" scrolling="no"></iframe>
                </div>

            </section>
        </div>
    </div>
<script> window.deliveryContextIsTao = true; </script>
<?= Template::inc('DeliveryServer/blocks/footer.tpl', 'taoDelivery'); ?>