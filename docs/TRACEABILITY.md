# File 13 Traceability

## Governing precedence applied

1. Latest explicit central/File-13 amendments.
2. File 13 final governing specification.
3. Consolidated central platform constitution.
4. Current verified companion repository contracts.
5. Historical package/notes only as evidence, never as present runtime truth.

The latest verified companion contracts supersede File 13 active ownership. File 24 classifies File 13 as historical compatibility only; File 20 owns invocation/frequency and File 25 owns presentation. The 1.0.1 implementation therefore suppresses the legacy public runtime.

## Requirement-to-code map

| Requirement | Implementation |
|---|---|
| F13-COMPAT-001 public runtime suppression | no public renderer/eligibility registration; `Settings::active_now()` is always false |
| F13-COMPAT-002 frequency ownership | no File 13 session/localStorage/cookie or analytics behavior |
| F13-COMPAT-003 presentation ownership | no active File 13 visual output; File 25 owns presentation |
| F13-COMPAT-004 historical preview | authenticated, no-store, non-persistent migration inspection only |
| F13-COMPAT-005 File 24 assurance | `spcrc/file13_contract_state` returns compatible only when legacy runtime remains disabled |
| F13-COMPAT-006 File 20 boundary | File 13 does not register `sabri_shell_welcome_intro_invoke` |
| F13-FR-011 admin preview | authenticated `/welcome-intro-preview/` states |
| F13-COMPAT-007 configuration migration | legacy values are preserved for audit but activation/analytics sanitize to disabled |
| F13-COMPAT-008 registry | File 01 manifest declares compatibility/migration scope and no active-render capability |
| F13-COMPAT-009 release truth | source, CI, package, staging and live states remain separately reported |

## Companion boundaries

### File 01
File 13 provides an explicit, authenticated File 01 registry synchronization path. It registers compatibility/migration scope and the private preview route only. An existing active registry record is downgraded truthfully to `degraded`; it is never silently retained as active.

### File 20
Current File 20 source still contains a historical handoff that names File 13 as preference owner. File 13 no longer registers that hook. This fail-closed gap must be corrected in File 20 before the active welcome experience can be claimed complete.

### File 25
File 25 is the canonical active presentation owner. File 13 may read its tokens only inside the authenticated historical preview; it produces no public presentation.

### File 00
File 13 does not clone identity, roles or entitlement data. Administrative authorization is capability-based and exposes `swi_intro_authorization_decision` as the adapter point for a stricter File 00 provider.

### File 24
File 13 publishes the exact `spcrc/file13_contract_state` filter required by File 24. Any re-registration of legacy public rendering or analytics changes the state to `blocked`.

## Status law

Source implementation is not proof of packaging, CI, staging, production deployment, or operational readiness. Those statuses require their own evidence.

## Preview rewrite hardening (source-only correction)

The historical admin preview is accepted only when WordPress matched the exact File 13 rewrite rule, the request path equals the canonical site-relative preview path, the registered mapping remains File 13's expected target, and the preview query variable is present. Arbitrary public query-string parameters must not trigger administrator redirects. A foreign rewrite collision fails closed. This is source-level behavior, not staging/live verification.
