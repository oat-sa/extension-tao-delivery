<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
use oat\tao\model\theme\Theme;

?><!doctype html>
<html class="no-js no-version-warning" lang="<?=tao_helpers_I18n::getLangCode()?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= Layout::renderThemeTemplate(Theme::CONTEXT_FRONTOFFICE, 'head') ?>

        <title><?= __("TAO - An Open and Versatile Computer-Based Assessment Platform") ?></title>

        <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao')?>"/>
        <link rel="stylesheet" href="<?= Template::css('tao-3.css', 'tao')?>"/>
        <link rel="stylesheet" href="<?= Template::css('delivery.css', 'taoDelivery') ?>"/>

        <link rel="stylesheet" href="<?= Layout::getThemeStylesheet(Theme::CONTEXT_FRONTOFFICE) ?>" />

        <?= has_data('additional-header')
            ? get_data('additional-header')->render()
            : '' ?>
    </head>
    <body class="delivery-scope">
    <?php Template::inc('blocks/requirement-check.tpl', 'tao'); ?>

        <div class="content-wrap<?php if (!get_data('showControls')) :?> no-controls<?php endif; ?>">

            <?php if (get_data('showControls')): ?>
                <?php $themeTemplate = Layout::renderThemeTemplate(Theme::CONTEXT_FRONTOFFICE, 'delivery-header', ['logout' => get_data('logout'), 'userLabel' => get_data('userLabel')]); ?>
                <?php if ($themeTemplate): ?>
                    <?= $themeTemplate ?>
                <?php else: ?>
                    <?php Template::inc('DeliveryServer/blocks/header.tpl', 'taoDelivery'); ?>
                <?php endif; ?>
            <?php endif; ?>

            <div id="feedback-box"></div>

            <?php /* actual content */
            Template::inc(get_data('content-template'), get_data('content-extension')); ?>
        </div>

        <?php if (get_data('showControls')){
            echo Layout::renderThemeTemplate(Theme::CONTEXT_FRONTOFFICE, 'footer');
        }?>
        <div class="loading-bar"></div>
    </body>
</html>
