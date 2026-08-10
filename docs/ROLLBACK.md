# Rollback Guide — File 13 / 1.1.0

## Immediate containment

- Set `SWI_DISABLE` to `true`, activate an accepted File 20/24 safe-mode suppression, or set File 13 `enabled = 0`.
- Signed replay does not bypass these hard gates.
- The overlay is hidden by default and runtime failures are fail-open, so containment/deactivation must leave the underlying page usable.

## Configuration rollback

File 13 keeps at most five bounded configuration snapshots when enabled. They are a convenience for configuration recovery, not a substitute for full database/files backup.

1. Capture the current exact deployed package/version/checksum, schema/config version, active configuration and incident evidence.
2. Disable public File 13 rendering if configuration integrity is uncertain.
3. In the admin rollback panel, select a reviewed snapshot and restore it through the normal guarded command. Restore creates a new configuration version and audit entry; it does not silently rewrite history.
4. Re-test Home eligibility, suppression routes, recurrence, replay/Never Show Again, adaptive/accessibility behavior and File 00/20/24/25 integration before reopening.

## Package rollback

1. Preserve a verified current backup plus both the current 1.1.0 package and the last known-good approved package/checksum.
2. Disable/deactivate File 13 while preserving its data.
3. Restore the last known-good package; do **not** destructively downgrade the 1.1 schema/config or delete user/device preference state merely to make an old package appear compatible.
4. If old code cannot safely read the newer configuration, keep File 13 disabled and restore a reviewed full backup/configuration snapshot rather than improvising a destructive downgrade.
5. Purge/warm LiteSpeed/CDN/browser caches as approved.
6. Smoke test Home, login/account/recovery, clinical/emergency/task/transaction routes, admin access, fail-open behavior and companion contracts.
7. Record rollback reason, timestamps, source/deployed versions, package checksums, schema/config versions and retest outcome.

## Data / uninstall law

Normal deactivate/rollback/uninstall is non-destructive. Purge occurs only when an authorized operator explicitly defines `SWI_PURGE_ON_UNINSTALL === true` after retention/export review. A purge is not a rollback mechanism.

## Re-release law

After a rollback, a corrected candidate must start again from exact source → tests → deterministic package → staging upgrade/fresh-install → original-symptom re-test → Founder acceptance where visual baseline changed → controlled live deployment. Historical green CI from the rolled-back package does not prove the new candidate.
