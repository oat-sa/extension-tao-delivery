/**
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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA ;
 *
 * Lock down browser exit bar on delivery server pages (test list after redirect).
 */
define([
    'lodash',
    'context',
    'tao/lockDownBrowser/lockDownBrowserService'
], function (_, context, lockDownBrowserModule) {
    'use strict';

    var ui = lockDownBrowserModule.uiProtocol;

    function getClientConfig() {
        return (context && context.lockDownBrowser) || {};
    }

    function createService() {
        var clientConfig = getClientConfig();

        return lockDownBrowserModule.createLockDownBrowserService(
            _.extend({}, clientConfig, { driver: lockDownBrowserModule.DRIVER_KIOSKED })
        );
    }

    function showExitBarIfNeeded() {
        var ldb = createService();

        if (!ldb) {
            return;
        }

        if (!ui.hasPendingExitInStorage(sessionStorage) && !ldb.isSessionActive()) {
            return;
        }

        lockDownBrowserModule.mountExitBar({
            onExitClick: function (ctx) {
                ui.clearPendingExitInStorage(sessionStorage);
                ctx.$bar.remove();
                ldb.exitApplication();
            }
        });
    }

    return {
        showExitBarIfNeeded: showExitBarIfNeeded,
        isLockDownBrowserSessionActive: function isLockDownBrowserSessionActive() {
            var ldb = createService();

            return ldb ? ldb.isSessionActive() : false;
        }
    };
});
