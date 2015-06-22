<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;

$releaseMsgData = Layout::getReleaseMsgData();
?><!doctype html>
<html class="no-js" lang="<?=tao_helpers_I18n::getLangCode()?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
    <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao')?>"/>
    <link rel="stylesheet" href="<?= Template::css('tao-3.css', 'tao')?>"/>
    <link rel="stylesheet" href="<?= Template::css('delivery.css', 'taoDelivery') ?>"/>

    <?php if (($themeUrl = Layout::getThemeUrl()) !== null): ?>
        <link rel="stylesheet" href="<?= $themeUrl ?>" />
    <?php endif; ?>

    <?php if(get_data('jsBlock') === 'runtime') : ?>
        <script src="<?= Template::js('lib/require.js', 'tao')?>"></script>
        <script>
            (function(){
                requirejs.config({waitSeconds : <?=get_data('client_timeout')?>});
                require(['<?=get_data('client_config_url')?>'], function(){
                    require([
                        'taoDelivery/controller/runtime/deliveryExecution',
                        'serviceApi/ServiceApi',
                        'serviceApi/StateStorage',
                        'serviceApi/UserInfoService'
                    ],
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
    <?php endif; ?>
</head>
<body class="delivery-scope">
<!-- content wrap -->
<div class="content-wrap">
    <header class="dark-bar clearfix">
        <a href="<?= $releaseMsgData['link'] ?>" title="<?=$releaseMsgData['msg'] ?>" class="lft" target="_blank">
            <img src="<?= $releaseMsgData['logo']?>" alt="<?= $releaseMsgData['branding']?> Logo" id="tao-main-logo">
        </a>
        <nav>
            <!-- snippet: dark bar left menu -->

            <div class="settings-menu rgt">
                <!-- Hamburger -->
                <span class="reduced-menu-trigger">
                    <span class="icon-mobile-menu"></span>
                    <?= __('More')?>
                </span>

                <ul class="clearfix plain">

                    <!-- Example without sub menu -->
                    <li>
                        <a id="home" href="<?=_url('index', 'DeliveryServer')?>">
                            <span class="icon-home"></span>
                        </a>
                    </li>
                    <li class="infoControl sep-before">
                        <a id="home" href="<?=_url('index', 'DeliveryServer')?>">
                            <span class="icon-test-taker"></span>
                            <span><?= get_data('userLabel'); ?></span>
                        </a>
                    </li>
                    <?php if (!get_data('deliveryExecution')): ?>
                    <li>
                        <a id="logout" class="" href="<?=_url('logout', 'DeliveryServer')?>">
                            <span class="icon-logout"></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <div id="feedback-box"></div>