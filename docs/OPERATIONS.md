# Operations Runbook — File 13 / 1.1.0

## Routine operator checks

1. Record exact deployed plugin/package version and checksum before diagnosing any symptom.
2. Inspect `Settings → Sabri Welcome Intro → Repository/runtime health snapshot` for local asset/config/approval state only; never treat it as staging/live proof by itself.
3. Confirm current deployed File 00 / 20 / 24 / 25 contract versions and active theme slot behavior.
4. Verify public Home eligibility plus suppression on login/account/recovery, appointment/task, clinical, emergency and transaction routes.
5. Verify 30-day recurrence, same-session/multi-tab suppression, Never Show Again, signed replay and version-aware replay separately.
6. Verify fail-open behavior with storage unavailable, JavaScript failure and CSS unavailable; underlying page must remain usable.
7. Verify adaptive full/light/static behavior on normal, Save-Data/slow, reduced-motion and constrained-device conditions.
8. Keep analytics disabled unless explicitly approved; when enabled, inspect only bounded aggregate events and retention.
9. Check Founder visual approval state after any source, copy, duration or visual-setting change. A changed baseline invalidates the prior approval automatically.
10. Retain at least one known-good full backup/package outside File 13's bounded configuration snapshots.

## Emergency containment

Use the narrowest applicable control:

- administrator `enabled = 0` for ordinary File 13 disable;
- `SWI_DISABLE` for scoped emergency containment;
- File 20/24 `sabri_platform_safe_mode` / `swi_force_disabled` contract for platform-coordinated suppression.

A signed replay never bypasses safe mode, hard route suppression, schedule denial or non-visual request denial. Disabling File 13 must not block public page rendering.

## Incident evidence

Capture, with timestamps:

- Repository HEAD and PR/source candidate SHA;
- exact deployed package/version/checksum;
- File 13 schema/config version;
- relevant File 00/20/24/25 deployed versions/contracts;
- active theme and cache/LiteSpeed state;
- exact route, account/guest state, browser/device, reduced-motion/Save-Data state;
- WordPress/PHP/runtime errors and File 13 bounded audit/health output;
- whether issue reproduces with File 13 disabled/safe-mode suppressed.

Do not call a defect resolved until the corrected package is deployed to the target environment and the original live/staging symptom is freshly re-tested with deployment parity confirmed.

## Maintenance

- Run the permanent exact-head CI before every package handoff.
- Regenerate POT after translatable-string changes.
- Rebuild deterministic ZIP/SHA/manifest after any source change.
- Re-open Founder visual approval after any baseline-affecting change.
- Rehearse rollback after material migration/config changes.
- Keep old CI/checksums as historical provenance only.
