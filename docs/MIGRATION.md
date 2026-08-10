# Migration Guide — File 13 / 1.1.0

## Supported source migration

`0.1.1 / 0.2.0 / 1.0.0 → 1.1.0`

1. Create and verify database/files backup before staging upgrade.
2. Record the installed/deployed plugin version, exact package SHA-256, current `swi_config`, schema option and relevant File 00/20/24/25 deployed contract versions.
3. Install/upgrade the **exact approved 1.1.0 ZIP** on staging; do not infer package identity from repository branch name.
4. Fresh legacy install: activation creates `swi_config` only when absent and maps legacy `swi_enabled` to the new `enabled` field.
5. Existing 1.0 configuration: migration performs an additive defaults merge + sanitization. Existing approved recurrence/routes/copy are preserved unless invalid under a stricter 1.1 rule. New Future switches/defaults, experience version and approval fields are added without creating database tables.
6. Schema `1.1.0` is recorded. Re-running the current migration is idempotent: it must not increment the governed configuration version merely because activation/upgrade runs again.
7. New account metadata (`experience_version`, Never Show Again and File-13 accessibility profile) is lazy/optional. No bulk user-meta migration is required; existing 1.0 recurrence timestamps remain valid and the version-aware layer can interpret legacy numeric device timestamps.
8. Guest first-party device state remains backward compatible: legacy numeric `swi_seen_at_v1` values are accepted; new writes store a version-aware JSON payload. Same-session and multi-tab keys remain privacy-minimal.
9. Founder visual approval is **not inherited as current approval** merely because an older configuration existed. A 1.1 visual baseline is current only after canonical Founder identity is asserted through File 00/institutional integration and the current source+visual/copy configuration is explicitly approved.
10. Activation creates/retains a bounded safe configuration snapshot and flushes the signed preview rewrite for `/welcome-intro-preview/`.
11. Verify System Check, public Home journey, suppressed routes, 30-day/same-session/multi-tab behavior, signed replay, Never Show Again, version replay, guest→account reconciliation, accessibility profiles, adaptive/static failure modes and File 00/20/24/25 integrations.
12. Re-run privacy export/erasure, preference reset, analytics-off default and rollback-snapshot tests after upgrade.
13. Do not remove the prior approved package/backup until an actual rollback rehearsal succeeds.

No database-table migration, bulk batch, dual-write, destructive cutover or service-worker migration is owned by File 13.

## Rollback compatibility

Code rollback may restore the prior plugin package, but a new 1.1 configuration/schema record must not be destructively downgraded. Use the documented configuration snapshot/full-backup procedure, disable the public intro if compatibility is uncertain, and re-test before reopening the release gate.
