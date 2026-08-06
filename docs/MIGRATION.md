# Migration Guide

## Supported source migration

`0.1.1/0.2.0 → 1.0.0`

1. Create a verified database/files backup.
2. Record current plugin version, folder, `swi_enabled` value and installed package SHA-256.
3. Install/upgrade the 1.0.0 ZIP on staging.
4. Activation creates `swi_config` only when absent and maps legacy `swi_enabled` to the new `enabled` field.
5. Existing legacy session cookie/storage names expire harmlessly; the 1.0.0 timestamp keys are independent.
6. Schema `1.0.0` is recorded; repeated activation/upgrade does not increment configuration version or duplicate data.
7. Rewrite rules are flushed once for `/welcome-intro-preview/`.
8. Verify System Check, public home journey, signed preview, 30-day state, suppressed routes and File 20/24/25 contracts.
9. Do not remove the prior package until rollback rehearsal succeeds.

No database table migration, bulk batch, dual-write or destructive cutover is required.
