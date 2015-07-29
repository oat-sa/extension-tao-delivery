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
    'tpl!taoDelivery/tpl/fullscreen-modal-feedback'
], function ($, __, feedback, modal, dialogTpl) {
    'use strict';

    var $dialog;
    var $body;

    var fs = (function() {
        var d = document,
            dElem = d.documentElement,
            _fs = {
                isSupported: (!!d.exitFullscreen ||
                    !!d.msExitFullscreen ||
                    !!d.mozCancelFullScreen ||
                    !!d.webkitExitFullscreen),
                requestFullscreen: (dElem.requestFullscreen ||
                    dElem.msRequestFullscreen ||
                    dElem.mozRequestFullScreen ||
                    (function() {
                        dElem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                    })),
                fullscreenchange: (function() {
                    var prefixes = ['', 'ms', 'moz', 'webkit'],
                        i = prefixes.length;
                    while(i--) {
                        if('on' + prefixes[i] + 'fullscreenchange' in dElem) {
                            return prefixes[i] + 'fullscreenchange';
                        }
                    }
                    return 'myfullscreenchange';
                }()),
                fullScreen: function() {
                    return ((document.fullscreenElement && document.fullscreenElement !== null) ||
                        document.mozFullScreen ||
                        document.webkitIsFullScreen ||
                        (screen.availHeight || screen.height - 30) <= window.innerHeight);
                }
            };

        _fs.changeInterval = null;

        // on older browsers wait for a full screen change to happen
        // and fire the change event manually
        _fs.awaitFsChange = function() {
            var event = document.createEvent('Event');
            event.initEvent(_fs.fullscreenchange, true, true);
            _fs.changeInterval = setInterval(function() {
                if(!_fs.fullScreen()) {
                    document.dispatchEvent(event);
                }
            }, 2000);
        };

        if(!_fs.isSupported) {
            _fs.awaitFsChange();
        }

        return _fs;
    }());


    /**
     * React to user input on the prompt which is either
     * key press or click on the button
     *
     * @param evt
     */
    var handleUserInput = function handleUserInput (evt) {

        // full screen needs to be initiated by pressing
        // F11 (Windows/Linux) or Ctrl+Cmd+F (Mac)
        if(!fs.isSupported) {
            $dialog.modal('close');
            return;
        }

        // accept 'enter' as only valid key stroke
        if(evt.type === 'keydown' && (evt.keyCode || evt.which) !== 13) {
            return;
        }

        // in all other cases either 'enter' has been hit or the enter button has been clicked
        fs.requestFullscreen.call(document.documentElement);
        $dialog.modal('close');
    };


    /**
     * Initialize full screen
     */
    var init = function init() {

        $body = $(document.body);

        // listen either to the native or the change event created in the observer above
        document.addEventListener(fs.fullscreenchange, function() {
            if(!fs.fullScreen()) {
                $dialog.modal('open');
            }
        });

        modal($body);
        $dialog = $(dialogTpl({
            fsSupported: fs.isSupported,
            // while this is vague chances that any Mac browser gets here are very little
            launchButton: navigator.platform.toLowerCase().indexOf('Mac') === 0 ? 'Ctrl+⌘+F' : 'F11'
        }));

        $dialog[0].querySelector('button').addEventListener('click', function(e) {
            handleUserInput(e);
        });

        document.addEventListener('keydown', function(e) {
            if(!fs.fullScreen()) {
                handleUserInput(e);
            }
        });

        $dialog.on('opened.modal', function() {
            clearInterval(fs.changeInterval);
        });

        $dialog.on('closed.modal', function() {
            fs.awaitFsChange();
        });

        $body.append($dialog);

        // Note that when a page is on full screen already on load (after F5 normally)
        // fullscreenElement and therefor fs.fullScreen() will report the wrong value!
        if(false === ((screen.availHeight || screen.height - 30) <= window.innerHeight)) {
            $dialog.modal({
                width: 500,
                disableClosing: true
            });
        }
    };

    /**
     * @exports
     */
    return {
        init: init
    };
});
