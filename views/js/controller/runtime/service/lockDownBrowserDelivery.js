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
 * Lock down browser exit menu on delivery server pages.
 */
define([
    'context',
    'tao/lockDownBrowser/lockDownBrowserService'
], function (context, lockDownBrowserModule) {
    'use strict';

    const ui = lockDownBrowserModule.uiProtocol;
    let ldb;

    function getClientConfig() {
        return context && context.lockDownBrowser;
    }

    function createService() {
        const clientConfig = getClientConfig();

        if (!clientConfig) {
            return null;
        }

        if (!ldb) {
            ldb = lockDownBrowserModule.createLockDownBrowserService(clientConfig);
        }

        return ldb;
    }

    function showExitMenuIfNeeded() {
        const service = createService();

        if (!service || !lockDownBrowserModule.isLockDownBrowserContext(service)) {
            return Promise.resolve(false);
        }

        lockDownBrowserModule.mountExitMenu({
            onExitClick: function () {
                ui.clearPendingExitInStorage(sessionStorage);
                service.exitApplication();
            }
        });

        return service.whenAvailable().then(function () {
            return true;
        });
    }

    return {
        showExitMenuIfNeeded: showExitMenuIfNeeded,
        isLockDownBrowserSessionActive: function isLockDownBrowserSessionActive() {
            const service = createService();

            return service ? service.isSessionActive() : false;
        }
    };
});
