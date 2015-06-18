<?php
use oat\tao\helpers\Template;
print Template::inc('DeliveryServer/blocks/header.tpl');
?>
    <!-- snippet: feedback box -->
    <div class="section-container">
        <div class="clear content-wrapper content-panel">
            <section class="content-container">

                <div class="content-block iframe-block" id="outer-delivery-iframe-container">

                    <iframe id="iframeDeliveryExec" class="toolframe" frameborder="0" scrolling="no"></iframe>
                <!--div id="overlay"></div>
                <div id="loading"><div-->
                </div>

            </section>
        </div>
    </div>
<?= Template::inc('DeliveryServer/blocks/footer.tpl'); ?>