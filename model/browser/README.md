# Lock down browser (taoDelivery)

Persists lock down browser **provider** on delivery execution at runner init (INV-1263).

## Backend

| Component | Role |
|-----------|------|
| `LockDownBrowserExecutionService` | Detect via `HttpRequestLockDownBrowserDetector`, set provider on execution |
| `LockDownBrowserAwareDeliveryExecutionInterface` | `getLockDownBrowser()` / `setLockDownBrowser()` |
| RDS | Column `lock_down_browser`; migration `Version202610091630002236_taoDelivery` |

`recordAtInit($deliveryExecutionId, $psrRequest)` merges `clientKioskApiOk` from `$_POST` into the PSR body when missing, then runs detection.

**Call site:** `taoQtiTest` `Runner::init()` delegates to this service (no LDB logic in the controller beyond that call).

Detection rules: **tao** `HttpRequestLockDownBrowserDetector` (`tao/models/classes/lockDownBrowser/README.md`).

## Delivery UI

| Module | Role |
|--------|------|
| `views/js/controller/runtime/service/lockDownBrowserDelivery.js` | Exit bar on delivery index after test finish redirect |
| `views/js/controller/runtime/service/deliveryExecution.js` | Defer finish / exit postMessage handling |

Bundles depend on `tao/loader/lockDownBrowser.min`. Rebuild: `npx grunt taodeliverybundle` from `tao/views/build`.
