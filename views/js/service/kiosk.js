/**
 * Kiosk{ed} helpers for delivery server bundles (no taoTestRunnerPlugins dependency).
 */
define([], function () {
    'use strict';

    function readKioskedFromWindow(win) {
        if (!win) {
            return null;
        }

        try {
            if (win.kiosked) {
                return { kind: 'kiosked', api: win.kiosked };
            }
        } catch (err) {
            // cross-origin
        }

        return null;
    }

    function readLegacyKioskFromWindow(win) {
        if (!win) {
            return null;
        }

        try {
            if (win.kiosk && typeof win.kiosk.isLocked === 'function') {
                return { kind: 'kiosk', api: win.kiosk };
            }
        } catch (err) {
            // cross-origin
        }

        return null;
    }

    function resolveApiHandle() {
        var candidates = [window, window.parent, window.top];
        var i;
        var handle;

        for (i = 0; i < candidates.length; i++) {
            handle = readKioskedFromWindow(candidates[i]);

            if (handle) {
                return handle;
            }
        }

        for (i = 0; i < candidates.length; i++) {
            handle = readLegacyKioskFromWindow(candidates[i]);

            if (handle) {
                return handle;
            }
        }

        return null;
    }

    function exitKiosk() {
        var handle = resolveApiHandle();

        if (!handle) {
            return;
        }

        if (handle.kind === 'kiosked' && typeof handle.api.exit === 'function') {
            handle.api.exit();

            return;
        }

        if (handle.kind === 'kiosk' && typeof handle.api.exit === 'function') {
            handle.api.exit(false);
        }
    }

    return {
        resolveApiHandle: resolveApiHandle,
        isKioskEnvironment: function isKioskEnvironment() {
            return !!resolveApiHandle();
        },
        exitKiosk: exitKiosk
    };
});
