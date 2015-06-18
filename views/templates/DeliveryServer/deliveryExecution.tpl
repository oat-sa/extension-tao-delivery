<?php
use oat\tao\helpers\Template;
?><!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
    <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao')?>"/>
    <link rel="stylesheet" href="<?= Template::css('tao-3.css', 'tao')?>"/>
    <link rel="stylesheet" href="<?= Template::css('runtime/deliveryExecution.css', 'taoDelivery') ?>"/>
    <script src="<?= Template::js('lib/require.js', 'tao')?>"></script>
    <script>
        (function(){
            requirejs.config({waitSeconds : <?=get_data('client_timeout')?>});
            require(['<?=get_data('client_config_url')?>'], function(){
                require(['taoDelivery/controller/runtime/deliveryExecution', 'serviceApi/ServiceApi', 'serviceApi/StateStorage', 'serviceApi/UserInfoService',],
                    function(deliveryExecution, ServiceApi, StateStorage, UserInfoService){

                        deliveryExecution.start({
                            serviceApi : <?=get_data('serviceApi')?>,
                            finishDeliveryExecution : '<?=_url('finishDeliveryExecution')?>',
                            deliveryExecution : '<?=get_data('deliveryExecution')?>'
                        });

                    });
            });
        }());
    </script>
</head>


<body>

<div class="content-wrap">
    <header class="dark-bar clearfix">
        <a href="" class="lft" target="_blank">
            <img src="<?= Template::img('tao-logo.png', 'tao')?>" alt="TAO Logo" id="tao-main-logo">
        </a>
        <nav>
            <!-- snippet: dark bar left menu -->
            <!-- snippet: dark bar right menu -->
        </nav>
    </header>

    <!-- snippet: feedback box -->
    <div class="section-container">
        <div class="clear content-wrapper content-panel">
            <section class="content-container">

                <!-- blue bar, no buttons - replace with snippet:  -->
                <div class="plain action-bar content-action-bar horizontal-action-bar">

                </div>
                <div class="content-block">
                    <!-- content goes here -->
                </div>
            </section>
        </div>
    </div>
</div>

<footer class="dark-bar">
    © 2013 - 2015 · <a href="http://taotesting.com" target="_blank">Open Assessment Technologies S.A.</a>
    · All rights reserved.
</footer>

</body>
</html>



<body class="tao-scope">
<?php if (get_data('showControls')) :?>
     <ul id="control" class="dark-bar">
         
         <li class="actionControl">
                <a id="home" href="<?=_url('index', 'DeliveryServer')?>">
                    <span class="icon-delivery"></span>
                    <?php echo __("My Tests"); ?></a>
         </li>
            
         <li class="separator">|</li>
         <li class="infoControl">
                <span class="icon-test-taker"></span>
                <?php echo get_data('userLabel'); ?>
            </li>   
            
                     
            <li class="actionControl">
                <a id="logout" class="" href="<?=_url('logout', 'DeliveryServer')?>">
                    <span class="icon-logout"></span>
                    <?php echo __("Logout"); ?>
                </a>
            </li>
      </ul>
<?php endif; ?>
    <div id="content" class='ui-corner-bottom'>
        <div id="tools">
            <iframe id="iframeDeliveryExec" class="toolframe" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>
    <div id="overlay"></div>
    <div id="loading"><div></div></div>
        <!-- End of content -->
</body>
</html>
