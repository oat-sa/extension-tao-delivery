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

        <title><?= Layout::getTitle() ?></title>

        <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao')?>"/>
        <link rel="stylesheet" href="<?= Template::css('tao-3.css', 'tao')?>"/>
        <link rel="stylesheet" href="<?= Template::css('delivery.css', 'taoDelivery') ?>"/>

        <link rel="stylesheet" href="<?= Layout::getThemeStylesheet(Theme::CONTEXT_FRONTOFFICE) ?>" />

        <?= has_data('additional-header')
            ? get_data('additional-header')->render()
            : '' ?>

        <style>
            .button {
                text-decoration: none !important;
                align-items: center;
                appearance: none;
                background-color: #fff;
                border-radius: 24px;
                border-style: none;
                box-shadow: rgba(0, 0, 0, .2) 0 3px 5px -1px,rgba(0, 0, 0, .14) 0 6px 10px 0,rgba(0, 0, 0, .12) 0 1px 18px 0;
                box-sizing: border-box;
                color: #3c4043;
                cursor: pointer;
                display: inline-flex;
                fill: currentcolor;
                font-family: "Google Sans",Roboto,Arial,sans-serif;
                font-size: 28px;
                font-weight: 500;
                height: 48px;
                justify-content: center;
                letter-spacing: .25px;
                line-height: normal;
                max-width: 100%;
                overflow: visible;
                padding: 2px 24px;
                position: relative;
                text-align: center;
                text-transform: none;
                transition: box-shadow 280ms cubic-bezier(.4, 0, .2, 1),opacity 15ms linear 30ms,transform 270ms cubic-bezier(0, 0, .2, 1) 0ms;
                user-select: none;
                -webkit-user-select: none;
                touch-action: manipulation;
                width: auto;
                will-change: transform,opacity;
                z-index: 0;
            }

            .button:hover {
                background: #F6F9FE;
                color: #174ea6;
            }

            .button:active {
                box-shadow: 0 4px 4px 0 rgb(60 64 67 / 30%), 0 8px 12px 6px rgb(60 64 67 / 15%);
                outline: none;
            }

            .button:focus {
                outline: none;
                border: 2px solid #4285f4;
            }

            .button:not(:disabled) {
                box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
            }

            .button:not(:disabled):hover {
                box-shadow: rgba(60, 64, 67, .3) 0 2px 3px 0, rgba(60, 64, 67, .15) 0 6px 10px 4px;
            }

            .button:not(:disabled):focus {
                box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
            }

            .button:not(:disabled):active {
                box-shadow: rgba(60, 64, 67, .3) 0 4px 4px 0, rgba(60, 64, 67, .15) 0 8px 12px 6px;
            }

            .button:disabled {
                box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
            }
        </style>
    </head>
    <body>
        <a class="button" href="<?= get_data('delivery-execution-url') ?>">Start test</a>
    </body>
</html>
