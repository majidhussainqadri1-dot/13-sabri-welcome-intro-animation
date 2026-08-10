# Architecture and Ownership — File 13 / 1.1.0

File 13 owns only the welcome-intro semantic experience, its governed configuration, privacy-minimal device/account preference projection, optional aggregate events, visual-baseline approval evidence and local operational diagnostics.

It does **not** own global navigation/layout (File 20), identity/roles (File 00), assurance governance (File 24), design-system truth (File 25), global notification truth, or service-worker registration. It consumes versioned restriction-only contracts and maintains a safe standalone/fail-open fallback.

## Components

- `SWI_Config`: versioned safe defaults, sanitization, site-time schedules, optimistic writes, atomic write lock, bounded audit, Future feature flags, Founder visual-approval metadata and bounded rollback snapshots.
- `SWI_Experience`: locale resolver, accessibility/variant allowlists, account preference projection, deterministic source visual-baseline hashing and PWA precache-asset declaration.
- `SWI_Eligibility`: server-side route/request/schedule/account/never-show/version/safe-mode decision. Signed replay bypasses recurrence only after hard server denials have passed.
- `SWI_Renderer`: hidden-by-default semantic dialog, Future runtime contract attributes, signed Preview Lab and signed Replay Welcome route handling.
- `welcome-intro.js`: same-session/multi-tab/30-day state; legacy + version-aware timestamp parsing; Never Show Again; guest→account handoff; adaptive full/light/static variants; Data Saver/connection/memory/offline degradation; performance circuit; accessibility profile; instant interaction exit; focus/inert/fail-open lifecycle.
- `SWI_REST`: authenticated dismissal and preference reconciliation, idempotency, rate limits, optional public aggregate events and privileged health endpoint.
- `SWI_Analytics`: bounded allowlisted aggregate counters only; no IP/user-agent/fingerprint collection.
- `SWI_Privacy`: WordPress export/erasure plus account reset/accessibility preference controls.
- `SWI_Admin`: governed settings, Future switches, Preview Lab, Founder visual approval, rollback snapshot controls, health and audit views.
- `SWI_Contracts`: File 20 registry/slot, File 24 safe-mode/assurance adapters and PWA precache asset filter; no duplicate shell/service worker.
- `SWI_System_Check`: approval-aware asset/config/contract/Future status with explicit staging/live truth boundary.
- `SWI_Activator`: idempotent legacy migration, current schema registration, initial safe snapshot and rewrite lifecycle.

## State models

- Intro: `hard server gates → eligible → CSS ready → variant selected → shown → skipped|completed|closed|never_show → recurrence/preference suppression`.
- Replay: `same-origin signed replay request → hard server gates remain enforced → recurrence/never-show bypass → shown`.
- Device/account reconciliation: `guest state read → authenticated canonical account state compared → only newer/stricter preference handoff → idempotent account preference endpoint`.
- Configuration: `active version N → serialization lock → optimistic version check → validated command → optional pre-update snapshot → active version N+1 → audit`; stale/concurrent writes fail closed.
- Founder visual approval: `current source visual hash → explicit approval capability → stored approval hash`; any source visual change causes `approval_current = false` until re-approved.
- Rollback: `bounded snapshot → explicit restore command → normal configuration version increment/audit → re-test`; it does not replace full backup/restore.
- Failure: `asset/storage/runtime/performance problem → static degrade or overlay removal → underlying page remains usable`.

## Architectural invariants

No duplicate canonical identity, table, queue, navigation wrapper, design-system store, service worker or content backend is created. Companion filters may restrict File 13 behavior but cannot broaden locally governed eligibility, recurrence, schedule, analytics, copy or kill-state decisions.
