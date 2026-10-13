/**
 * Kiosk{ed} exit bar on delivery server pages (test list after redirect).
 */
define([
    'jquery',
    'i18n',
    'taoDelivery/service/kiosk'
], function ($, __, kioskModule) {
    'use strict';

    var EXIT_BAR_ID = 'kiosk-exit-bar';
    var PENDING_EXIT_KEY = 'taoKioskLockdownPendingExit';

    function showExitBarIfNeeded() {
        var pending = false;

        try {
            pending = sessionStorage.getItem(PENDING_EXIT_KEY) === '1';
        } catch (err) {
            pending = false;
        }

        if (!pending && !kioskModule.isKioskEnvironment()) {
            return;
        }

        if (document.getElementById(EXIT_BAR_ID)) {
            return;
        }

        var $bar = $('<div id="' + EXIT_BAR_ID + '" class="kiosk-exit-bar">' +
            '<button type="button" class="kiosk-exit-button btn btn-primary">' +
            __('Esci dal browser di lockdown') +
            '</button></div>');

        $bar.css({
            position: 'fixed',
            bottom: 0,
            left: 0,
            right: 0,
            zIndex: 10000,
            padding: '12px 16px',
            textAlign: 'center',
            background: 'rgba(0, 0, 0, 0.85)'
        });

        $bar.find('button').css({ minWidth: '240px' }).on('click', function () {
            try {
                sessionStorage.removeItem(PENDING_EXIT_KEY);
            } catch (storageErr) {
                // ponytail: ignore
            }

            $bar.remove();
            kioskModule.exitKiosk();
        });

        $(document.body).append($bar);
    }

    return {
        showExitBarIfNeeded: showExitBarIfNeeded,
        isKioskEnvironment: kioskModule.isKioskEnvironment
    };
});
