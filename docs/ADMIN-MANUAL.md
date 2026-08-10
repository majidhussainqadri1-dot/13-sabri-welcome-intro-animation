# Administrator Manual — File 13 / 1.1.0

1. Open `Settings → Sabri Welcome Intro`.
2. Keep recurrence at **30 days or longer**. Values below 30 are rejected; same-session/multi-tab suppression remains additional protection.
3. Keep automatic close at `0` unless a separate Founder-approved visual specification explicitly authorizes a nonzero timing.
4. Keep eligible routes narrowly allowlisted. The default is `/` only; `*` requires explicit cross-file review.
5. Do not remove login, registration, account/recovery/support, booking/task, clinical, emergency or transaction suppressions. File 20/24 may impose stricter suppression.
6. Future feature switches control adaptive variants, Never Show Again, signed replay, version replay, guest→account reconciliation, instant exit, Data Saver/static mode, performance circuit breaking, accessibility profiles, PWA precache adaptation and rollback snapshots. Disabling a Future convenience must never weaken fail-open, route, privacy or safe-mode protection.
7. Set a realistic performance circuit budget. If client startup exceeds the budget, File 13 degrades to a static presentation rather than blocking the page.
8. Use **Advanced Preview Lab** to inspect default/reduced/skipped/disabled/error/data-saver/offline states, full/light/static variants and accessibility profiles. Signed previews are noindex/no-cache and must not alter public recurrence state.
9. **Founder visual approval** is a separate approval gate. A stored approval becomes stale automatically when the source visual baseline hash changes. Do not treat an old approval hash as approval of changed CSS/JS/logo/renderer source.
10. Rollback snapshots are bounded configuration snapshots, not a substitute for full site/database backup and restore rehearsal. Restore only a reviewed snapshot and re-test after restoration.
11. Keep aggregate analytics disabled unless approved. When enabled, only allowlisted bounded aggregates are recorded; File 13 does not collect IP address, user agent or fingerprint identifiers.
12. Account preferences can reset recurrence/`Never Show Again` and select a File-13-only accessibility profile. These preferences participate in privacy export/erasure.
13. Review **Repository/runtime health snapshot** before release, but remember that it is repository/runtime evidence only. `degraded`, missing/stale Founder approval, missing companion integration or any unresolved defect blocks the relevant release gate.
14. Use File 20/24 safe mode or `SWI_DISABLE` for emergency containment. Signed replay never overrides safe-mode, route, schedule or non-visual-request denials.
15. Complete every item in `docs/STAGING-ACCEPTANCE.md`, including real File 00/20/24/25 integration, browser/accessibility/cache and rollback checks, before any staging-accepted/live claim.
