# Rollback Guide

## Immediate containment

- Set `SWI_DISABLE` to `true`, or activate File 20/24 safe mode, or disable the module in Settings.
- The overlay is hidden by default, so deactivation leaves the underlying site usable.

## Package rollback

1. Preserve current database/files backup and 1.0.0 ZIP/checksum.
2. Deactivate 1.0.0.
3. Restore the last approved package without deleting `swi_config`, audit or preference metadata.
4. Purge LiteSpeed/CDN/browser caches as approved.
5. Smoke test Home, login/recovery, clinical/emergency routes and admin access.
6. Record rollback reason, timestamps and affected versions.

## Data rule

Uninstall is non-destructive. Purge occurs only when the operator explicitly defines `SWI_PURGE_ON_UNINSTALL` as `true` after approved retention/export review. Normal rollback never purges data.
