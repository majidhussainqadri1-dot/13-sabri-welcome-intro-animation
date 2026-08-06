# Requirements Traceability

## Functional requirements

| ID | Capability | Implementation | Automated evidence |
|---|---|---|---|
| F13-FR-001 | Eligibility resolver | `SWI_Eligibility`, route/schedule/safe-mode/account checks | PHP eligibility assertions; static route/kill-switch checks |
| F13-FR-002 | Session/frequency state | JS session claim + timestamp cookie/local storage; account meta preferred; preview bypass | JS same-session/29-day/31-day tests; PHP account tests |
| F13-FR-003 | Intro sequence ≤8s | local SVG, copy, green/orange sequence, exact JS maximum timer | JS timer tests; static asset/brand checks |
| F13-FR-004 | Skip/Continue/Close/Escape | three controls, Escape, persistence and cleanup | JS continue/Escape/focus tests |
| F13-FR-005 | Reduced motion | media query + short runtime state + preview | JS reduced-motion test; static CSS |
| F13-FR-006 | Screen reader/focus | dialog semantics, single live announcement, inert lifecycle, focus restoration | JS focus/inert tests; staging AT checklist |
| F13-FR-007 | No sound | no audio API/element or sound setting | static no-audio check |
| F13-FR-008 | Asset loading | local deferred JS/CSS, path SVG, no remote dependency, hidden fail-open | static remote/hidden checks; package manifest |
| F13-FR-009 | File 20 integration | registry filter, slot action, duplicate render guard, runtime config/eligibility filters | PHP contract assertions; static shell checks |
| F13-FR-010 | File 25 tokens | `--sabri-color-*` consumption with accessible fallback and logical properties | static token/green checks; staging visual regression |
| F13-FR-011 | Admin preview | signed `/welcome-intro-preview/`, five states, no cache/index | static nonce/noindex check; staging previews |
| F13-FR-012 | Configuration governance | safe defaults, sanitization, schedule, version conflict, audit | PHP sanitization/save/audit assertions |
| F13-FR-013 | Failure behavior | hidden attribute, CSS readiness check, fail-open removal, no-JS rule | JS CSS/storage failure test; static hidden check |
| F13-FR-014 | Analytics | opt-in allowlisted aggregate, 90-day/day cap, no fingerprint fields | PHP analytics assertions; static privacy check |
| F13-FR-015 | Kill switch | admin flag, `SWI_DISABLE`, File 20/24 safe mode filters | PHP and static kill-switch tests |

## Non-functional requirements

| ID | Evidence status |
|---|---|
| F13-NFR-001 Authorization | Native capability/nonce/REST permission plus File 00 capability filter; source tests green; real File 00 staging pending. |
| F13-NFR-002 Privacy lifecycle | documented purpose/minimization/retention; export, erasure and account reset implemented. |
| F13-NFR-003 Reliability | idempotency, optimistic config version, bounded state/analytics, safe degraded path; no queue/background jobs applicable. |
| F13-NFR-004 Performance | conditional bounded work and source budgets; real p75/p95/Core Web Vitals staging measurement pending. |
| F13-NFR-005 Accessibility | semantic/focus/keyboard/reduced-motion/RTL source and runtime tests; real AT/zoom/contrast staging acceptance pending. |
| F13-NFR-006 Observability | bounded audit, optional aggregates, System Check and public browser events; alert ownership is operational staging work. |
| F13-NFR-007 Migration/rollback | idempotent legacy migration, rewrite flush, non-destructive uninstall and documented rollback; real backup restore pending. |
| F13-NFR-008 Operability | settings, health/status, preview, safe defaults, safe mode and runbooks implemented; no cache/index/queue owned by File 13. |
| F13-NFR-009 Compatibility | PHP 7.4 syntax/behavior and current runner tests; exact WordPress 7.0.1/PHP 8.3 staging verification pending. |
| F13-NFR-010 Localization | English US base, 55-string POT, text domain, `lang`, logical RTL CSS; real Urdu/Arabic glyph/reflow/date-time acceptance pending. |
