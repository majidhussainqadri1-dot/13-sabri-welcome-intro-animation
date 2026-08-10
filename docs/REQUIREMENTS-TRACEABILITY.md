# Requirements Traceability — File 13 / 1.1.0

## Functional requirements

| ID | Capability | Implementation | Automated / acceptance evidence |
|---|---|---|---|
| F13-FR-001 | Eligibility resolver | `SWI_Eligibility`, route/schedule/safe-mode/account checks; replay bypass occurs only after hard server gates | PHP eligibility assertions; eighty-round route/safe-mode checks |
| F13-FR-002 | 30-day recurrence + same-session suppression | JS session/multi-tab claim + version-aware first-party timestamp; account metadata preferred with bounded guest handoff | JS same-session/29-day/31-day/version tests; PHP account tests |
| F13-FR-003 | Intro sequence and duration governance | local SVG, approved healing identity claim, exact-green cue; no forced historical eight-second auto-close; optional separately approved nonzero duration bounded | PHP duration assertions; static brand/duration checks; Founder/staging visual acceptance pending |
| F13-FR-004 | Skip/Continue/Close/Escape/preferences | immediate controls, Escape, Never Show Again, signed replay, persistence/cleanup | core/Future JS controls and persistence tests |
| F13-FR-005 | Reduced motion | media query + static runtime variant + preview | JS reduced-motion/static test; CSS/static contracts |
| F13-FR-006 | Screen reader/focus | dialog semantics, announcement, inert lifecycle, focus trap/restoration, accessibility profiles | JS focus/inert/profile tests; real AT staging pending |
| F13-FR-007 | No sound | no audio API/element or sound setting | static no-audio contract |
| F13-FR-008 | Asset loading | local deferred JS/CSS/SVG, no remote runtime dependency, hidden fail-open | static remote/hidden checks; deterministic package |
| F13-FR-009 | File 20 integration | registry/slot contracts, duplicate-render guard, restriction-only runtime config/eligibility filters | PHP governance contracts; static shell checks; real File 20 staging pending |
| F13-FR-010 | File 25 tokens | `--sabri-color-*` consumption, exact `#087A4E` fallback, contextual orange, logical RTL properties | static token check; real File 25/visual staging pending |
| F13-FR-011 | Admin Preview Lab | signed `/welcome-intro-preview/`; default/reduced/skipped/disabled/error/data-saver/offline states; full/light/static variants; accessibility profiles; no public persistence | renderer/static contracts; Preview Lab source; real browser acceptance pending |
| F13-FR-012 | Configuration governance | safe defaults, sanitization, site-time schedule, optimistic conflict + write lock, bounded audit/snapshots; runtime proposals only restrict | PHP save/concurrency/monotonic assertions |
| F13-FR-013 | Failure behavior | hidden markup, CSS readiness, runtime catch/fail-open, no-JS bypass | JS CSS/storage/runtime-failure tests; static hidden check |
| F13-FR-014 | Analytics | opt-in allowlisted aggregate, bounded retention/caps, separate nonce, no fingerprint fields, variant/replay aggregates | PHP analytics + REST tests; static privacy contracts |
| F13-FR-015 | Kill switch | admin flag, `SWI_DISABLE`, File 20/24 safe-mode filters; external filters cannot lift denials | PHP safe-mode/monotonic eligibility assertions |

## Future Welcome Experience Superset — 18 enhancements

| ID | Enhancement | Implementation | Regression evidence |
|---|---|---|---|
| F13-FUT-01 | Adaptive intro variants | `chooseVariant()` + full/light/static CSS variants | Future PHP + JS runtime + 80-round gate |
| F13-FUT-02 | Never Show Again | guest local key + authenticated account meta + user reset | Future PHP/JS + privacy lifecycle tests |
| F13-FUT-03 | Replay Welcome | same-origin signed replay URL + server replay eligibility | PHP replay helper/eligibility + JS replay tests |
| F13-FUT-04 | Version-aware replay | experience version in account/device state | PHP/JS version replay tests |
| F13-FUT-05 | Cross-device preference sync | authenticated idempotent `/preference` endpoint | PHP REST + Future JS request tests |
| F13-FUT-06 | Guest → account reconciliation | bounded newer-guest handoff | historical/Future JS account tests |
| F13-FUT-07 | Instant interaction exit | pointer/wheel intent handlers; existing explicit buttons remain | Future PHP/static + JS behavior |
| F13-FUT-08 | Data Saver static mode | Network Information `saveData` → static | Future JS runtime |
| F13-FUT-09 | Performance circuit breaker | measured startup budget → static degrade | Future JS runtime + system-check field |
| F13-FUT-10 | Accessibility profiles | auto/high-contrast/large-text/simple/screen-reader | Future JS/CSS + privacy/account preference tests |
| F13-FUT-11 | Localized governed copy | sanitized locale map + locale resolver | Future PHP/static; real Urdu/Arabic visual acceptance pending |
| F13-FUT-12 | Advanced Preview Lab | states × variants × accessibility profiles | admin/renderer static checks; real browser acceptance pending |
| F13-FUT-13 | Founder visual approval | separately filtered approval capability, approval state/hash/audit | PHP/system-check source; real Founder acceptance remains external gate |
| F13-FUT-14 | Deterministic visual baseline | source hashes for CSS/JS/logo/renderer | PHP/static + health evidence |
| F13-FUT-15 | Privacy-safe telemetry | allowlisted aggregate variant/replay events only | PHP/REST/static privacy tests |
| F13-FUT-16 | Health dashboard | approval-aware status + explicit production-truth boundary | PHP system-check tests |
| F13-FUT-17 | Rollback snapshots | bounded five-snapshot config history + restore | PHP config tests; real backup/rollback rehearsal pending |
| F13-FUT-18 | PWA/offline adapter | local precache asset filter only; no service-worker registration | Future/static contracts |

## Non-functional requirements

| ID | Evidence status |
|---|---|
| F13-NFR-001 Authorization | Native capability/nonce/REST permission plus File 00 filters; source tests; real File 00 staging pending. |
| F13-NFR-002 Privacy lifecycle | purpose/minimization/retention, account/device preference export/erasure/reset, aggregate telemetry only. |
| F13-NFR-003 Reliability | idempotency, optimistic config version + lock, bounded state/analytics/snapshots, fail-open degraded path. |
| F13-NFR-004 Performance | local assets, adaptive/static degrade and bounded startup circuit; real p75/p95/Core Web Vitals staging pending. |
| F13-NFR-005 Accessibility | semantic/focus/keyboard/reduced-motion/RTL/profile source tests; real AT/zoom/contrast staging pending. |
| F13-NFR-006 Observability | bounded audit, optional aggregates, approval-aware System Check and public browser events; production monitoring external. |
| F13-NFR-007 Migration/rollback | idempotent migration, non-destructive uninstall, bounded config snapshots; real backup restore/rehearsal pending. |
| F13-NFR-008 Operability | settings, health/status, Preview Lab, kill/safe mode, replay/reset and rollback controls implemented. |
| F13-NFR-009 Compatibility | declared PHP 7.4+ and current CI runtime syntax/behavior; target WordPress/PHP staging matrix pending. |
| F13-NFR-010 Localization | English-US base, deterministic POT, sanitized locale variants, `lang`, `dir=auto`, logical RTL CSS; real multilingual visual acceptance pending. |

## Status truth

Traceability proves requirement-to-source/test mapping only. Staging-Accepted, Live-Deployed and Operational remain separate gates and cannot be inferred from this matrix.
