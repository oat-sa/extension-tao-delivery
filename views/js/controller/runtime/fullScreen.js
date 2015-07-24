/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 *
 * @author dieter <dieter@taotesting.com>
 */
define([
    'jquery',
    'i18n',
    'ui/feedback',
    'ui/modal',
    'tpl!taoDelivery/tpl/modal-feedback'
], function ($, __, feedback, modal, dialogTpl) {
    'use strict';

    var $dialog;
    var $body;
    var $content;
    var shouldBeFullScreen = true;

    var isFullScreen = function() {
        // use .fullscreen HTML5
        return (screen.availHeight || screen.height - 30) <= window.innerHeight;
    };

    /**
     * Toggle full screen
     */
    function toggleFullScreen() {
        if (!document.fullscreenElement &&    // alternative standard method
            !document.mozFullScreenElement &&
            !document.webkitFullscreenElement &&
            !document.msFullscreenElement) {  // current working methods
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            }
            else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            }
            else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            }
            else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
            shouldBeFullScreen = true;
        }
        else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
            else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            }
            else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
            shouldBeFullScreen = false;
        }
    }



    $(document).on('keypress click', function(e) {
        console.log('received event %s, shouldBeFullScreen %s, isFullScreen %s', e.type, shouldBeFullScreen, isFullScreen())
        if(shouldBeFullScreen && !isFullScreen()){
            fsPrompt();
        }
    });

    /**
     * Prompt for full screen
     */
    var fsPrompt = function fsPrompt () {
        $content.css('visibility', 'hidden');
        $dialog.modal({ width: 500, disableClosing:true });

        $dialog.find('.enter-full-screen').off('click keydown').on('click keydown', function () {
            toggleFullScreen();
            $content.css('visibility', 'visible');
            $dialog.modal('close');
        }).focus();
    };

//fs-check-required = false

    /**
     * Initialize full screen
     */
    var init = function init() {
        $body = $(document.body);
        $content = $body.find('.content-wrap, footer, .loading-bar');
        modal($body);
        $body.append(dialogTpl());
        $dialog = $('.full-screen-modal');
        fsPrompt();// displays popup
        setInterval(function(){
            console.log('check')
        }, 1000)
        //popup.onclose set state to fs-check-required = true
        // set interval to probe fs state
        // if (fs-state = fullscreen) {
        // cancel interval
    };

    /**
     * @exports
     */
    return {
        init: init
    };
});
