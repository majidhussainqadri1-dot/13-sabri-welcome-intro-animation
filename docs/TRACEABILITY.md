# File 13 Traceability

## Governing precedence applied

1. Latest explicit central/File-13 amendments.
2. File 13 final governing specification.
3. Consolidated central platform constitution.
4. Current verified companion repository contracts.
5. Historical package/notes only as evidence, never as present runtime truth.

Current File 24/25 source contracts assign invocation/frequency to File 20, presentation to File 25, and historical compatibility to File 13. The original Founder-approved File 13 plan v1.0 assigns active welcome/session ownership to File 13 and requires dated Founder approval for a permanent ownership change. Until the change-control record is verified, 1.0.1 suppresses the legacy public runtime as fail-closed containment, not proof that the permanent ownership transfer was authorized.

## Requirement-to-code map

| Requirement | Implementation |
|---|---|
| F13-COMPAT-001 public runtime suppression | no public renderer/eligibility registration; `Settings::active_now()` is always false |
| F13-COMPAT-002 frequency ownership | no File 13 session/localStorage/cookie or analytics behavior |
| F13-COMPAT-003 presentation ownership | no active File 13 visual output; File 25 owns presentation |
| F13-COMPAT-004 historical preview | authenticated, no-store, non-persistent migration inspection only |
| F13-COMPAT-005 File 24 assurance | `spcrc/file13_contract_state` returns compatible only when legacy runtime is disabled, schema migration is current, File 01 registry is synced, and no audit-gap marker remains |
| F13-COMPAT-006 File 20 boundary | File 13 does not register `sabri_shell_welcome_intro_invoke` |
| F13-FR-011 admin preview | authenticated `/welcome-intro-preview/` semantic states with auto/320px small-mobile/360px mobile/768px tablet/1280px desktop responsive iframe presets; translated, keyboard-visible, 44px viewport links |
| F13-COMPAT-007 configuration migration | legacy values are preserved for audit but activation/analytics sanitize to disabled |
| F13-COMPAT-008 registry | File 01 manifest declares compatibility/migration scope and no active-render capability |
| F13-COMPAT-009 release truth | source, CI, package, staging and live states remain separately reported |

## Companion boundaries

### File 01
File 13 provides an explicit, authenticated File 01 registry synchronization path. It registers compatibility/migration scope and the private preview route only. An existing active registry record is downgraded truthfully to `degraded`; it is never silently retained as active.

### File 20
File 20 main (reviewed SHA `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`) still contains a historical handoff naming File 13 as the preference owner. File 13 no longer registers that legacy hook. The separate, current open draft PR #41 (reviewed SHA `340d9864b9d77add92777b04826b4eeb6f408fcf`) implements File 20's canonical 30-day welcome frequency, File 25 presentation handoff, and strict version/token validation. These draft changes are **not merged to File 20 main**. Neither branch is evidence of the live deployment; actual installed parity, Founder's ownership approval and staging/browser acceptance remain independent release gates.

**Distinct contract diagnostic:** File 13 reports `file20_available` for the installed File 20 plugin/class presence only, and separately reports `file20_welcome_contract.valid` after confirming File 20's loaded canonical `Sabri\\UnifiedShell\\WelcomeFrequency` class and exact registered WordPress `WelcomeFrequency::contract` callback on `sabri_shell_welcome_frequency_contract`, then validating contract owner, semver 1.x, exact 30-day interval, File 25 presentation, guest/member preference boundaries, and suppression of the old File 13 hook. A third-party filter supplying the same payload without the native File 20 callback must be rejected. The older main and newer draft both report File 20 plugin version `1.4.17`; version alone is not proof of the canonical 30-day implementation. An absent contract does **not** automatically reclassify File 13's independent historical-suppression/ File 24 compatibility state. A positive contract does **not** certify deployed SHA, database state or live user journeys.

### File 25
File 25 is the canonical active presentation owner. File 13 may read its tokens only inside the authenticated historical preview; it produces no public presentation.

The legacy preview's File 25 contract diagnostics now use the exact version compatibility boundary required by the reviewed File 20 draft owner bridge: File 25 >=1.9.0 and <2.0.0, plus three validated six-digit hex color tokens. Versions outside that range fail closed to historical fallback colors; source parity does not prove installed plugin parity.

### File 00
File 13 does not clone identity, roles or entitlement data. Administrative authorization is capability-based and exposes `swi_intro_authorization_decision` as the adapter point for a stricter File 00 provider.

### File 24
File 13 publishes the exact `spcrc/file13_contract_state` filter required by File 24. Legacy public rendering/analytics, pending schema migration, an unsynced File 01 registry, or a configuration audit gap each changes the state to `blocked`. This compatibility signal is not Founder approval, deployed parity, or production readiness.

## Status law

Source implementation is not proof of packaging, CI, staging, production deployment, or operational readiness. Those statuses require their own evidence.

## Preview rewrite hardening (source-only correction)

The historical admin preview is accepted only when WordPress matched the exact File 13 rewrite rule, the request path equals the canonical site-relative preview path, the registered mapping remains File 13's expected target, and the preview query variable is present. Arbitrary public query-string parameters must not trigger administrator redirects. A foreign rewrite collision fails closed. This is source-level behavior, not staging/live verification.

## Historical preview route health assurance (source-only)

File 13 health must not claim `healthy`, or return File 24 `compatible`, when the authenticated historical preview route was not registered by File 13 in this request or the canonical mapping is missing/mismatched in persisted WordPress rewrite rules. Registration in memory alone is not proof of a reachable URL. The route check also checks both slash variants against the first matching persisted rewrite rule: a broader foreign rule preceding File 13's exact mapping degrades health and blocks File 24 compatibility, even if the exact mapping is present later. The route check is read-only and never flushes rewrite rules during health evaluation. A successful source regression does not establish live rewrite or deployed-state parity.

## Historical preview forced reduced-motion assurance (source-only)

When the authenticated legacy preview selects `state=reduced`, JavaScript applies `swi-intro--reduced` before revealing the preview, and scoped CSS suppresses entrance, exit, transition and progress-line animation even if the operating system does not request reduced motion. The OS media query remains supported. The public runtime remains inert; File 25 still owns active presentation. Regression coverage: `tests/js-behavior.test.js` and `tests/visual-contract.php`. This is not browser, staging or live accessibility acceptance.

## One-off File 13 preview first-match route guard — source-only hardening (2026-10-12)

The authenticated historical preview now requires the same persisted and first-match-reachable canonical WordPress rewrite proof that the File 13 health/assurance contract already demands. Request-local `add_rewrite_rule()`, a manually supplied preview query variable, or an unpersisted/foreign/shadowed rewrite cannot be mistaken for a verified protected preview route. `tests/rewrite-lifecycle.php` now exercises the unpersisted, earlier-shadowing, mismatched persisted target, and restored valid route states. This is a narrowly owned File 13 correction. It is **not** an assertion that the production WordPress rewrite table is current, nor does it transfer File 20 frequency or File 25 presentation responsibilities.

Plan-to-code cross-owner exceptions remain recorded separately: the reviewed File 20 **main** still declares File 13 the historic session/frequency owner, while the separate open draft PR #41 has the newer File 20/25 arrangement. Approval of permanent ownership and package identity, merging and deploying the 30-day recurrence, the installed File 25 presentation, and WordPress staging/live acceptance are **not** established by a draft-source patch. Do not promote source-only passing CI into a claim of completed welcome experience.


### F13-NFR-009 Project compatibility baseline versus plugin minimums (2026-10-17)

The amended File13 plan explicitly requires a **fresh WordPress 7.0.1 / PHP 8.3 project-baseline re-verification** before release. Those are project *version targets*, distinct from this historical plugin's lower declared installation minimums (WordPress 6.0 / PHP 8.1). `Health::project_baseline_status()` reports the observed WordPress/PHP runtime strings, independently validates the two thresholds, and returns `acceptance_verification=not_assessed` regardless of version eligibility. The privileged Admin Health Panel makes this distinction visible; `tests/health-contract.php` exercises accepted, rejected and malformed version combinations. These fields do NOT mark real staging/browser/rollback or live acceptance as passed, do not change File13's historical legacy suppression/ File24 compatibility assurance, and do not prove deployed artifact or DB parity. Actual WordPress 7.0.1/PHP 8.3 staging acceptance remains a separate release gate.
