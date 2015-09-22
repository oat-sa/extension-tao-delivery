<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;

$releaseMsgData = Layout::getReleaseMsgData();
?><!doctype html>
<html class="no-js no-version-warning" lang="<?=tao_helpers_I18n::getLangCode()?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>
    <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao')?>"/>
    <link rel="stylesheet" href="<?= Template::css('tao-3.css', 'tao')?>"/>
    <link rel="stylesheet" href="<?= Template::css('delivery.css', 'taoDelivery') ?>"/>
    <link rel="shortcut icon" href="<?= Template::img('favicon.ico', 'tao')?>"/>

    <?php if (($themeUrl = Layout::getThemeUrl()) !== null): ?>
        <link rel="stylesheet" href="<?= $themeUrl ?>" />
    <?php endif; ?>

    <?php if(get_data('jsBlock') === 'runtime') : ?>
        <script>
            var require = {
                config : {
                    'taoDelivery/controller/runtime/deliveryExecution' : {
                        serviceApi : <?=json_encode(get_data('serviceApi'))?>,
                        finishDeliveryExecution : '<?=get_data('finishUrl')?>',
                        deliveryExecution : '<?=get_data('deliveryExecution')?>',
                        deliveryServerConfig : <?=get_data('deliveryServerConfig')?>
                    }
                }
            };
        </script>
        <?= Layout::getAmdLoader(Template::js('deliveryExecution.js', 'taoDelivery'), Template::js('deliveryExecution.min.js', 'taoDelivery'))  ?>
    <?php endif; ?>
</head>
<body class="delivery-scope">
<!-- content wrap -->
<div class="content-wrap<?php if (!get_data('showControls')) :?> no-controls<?php endif; ?>">
    <?php if (get_data('showControls')) :?>
    <header class="dark-bar clearfix">
        <span class="lft">
            <img src="<?= $releaseMsgData['logo']?>" alt="<?= $releaseMsgData['branding']?> Logo" id="tao-main-logo">
        </span>
        <div class="lft title-box"></div>
        <nav class="rgt">
            <!-- snippet: dark bar left menu -->

            <div class="settings-menu">

                <ul class="clearfix plain">
                    <li data-control="home">
                        <a id="home" href="<?=_url('index', 'DeliveryServer')?>">
                            <span class="icon-home"></span>
                        </a>
                    </li>
                    <li class="infoControl sep-before">
                        <span class="a">
                            <span class="icon-test-taker"></span>
                            <span><?= get_data('userLabel'); ?></span>
                        </span>
                    </li>
                    <li class="infoControl sep-before" data-control="logout">
                        <a id="logout" class="" href="<?=_url('logout', 'DeliveryServer')?>">
                            <span class="icon-logout"></span>
                            <span class="text"><?= __("Logout"); ?></span>
                        </a>
                    </li>
                    <li class="infoControl sep-before hidden" data-control="exit">
                        <a href="#">
                            <span class="icon-logout"></span>
                            <span class="text"><?= __("Exit"); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <?php endif ?>
    <div id="feedback-box"></div>
