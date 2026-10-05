# File 13 Traceability

## Governing precedence applied

1. Latest explicit central/File-13 amendments.
2. File 13 final governing specification.
3. Consolidated central platform constitution.
4. Current verified companion repository contracts.
5. Historical package/notes only as evidence, never as present runtime truth.

The final File 13 amendment supersedes the historical orange-primary and forced-eight-second wording. The active implementation therefore uses Sabri Green and does not enforce an eight-second animation.

## Requirement-to-code map

| Requirement | Implementation |
|---|---|
| F13-FR-001 eligibility resolver | `Eligibility::public_request_eligible()` |
| F13-FR-002 recurrence/session state | `assets/js/welcome-intro.js` |
| F13-FR-003 restrained sequence | `Renderer::markup()` + CSS/JS |
| F13-FR-004 Skip/Escape | JS click + keydown handlers |
| F13-FR-005 reduced motion | media query + JS reduced path |
| F13-FR-006 screen reader/focus | status region, no focus stealing, semantic button |
| F13-FR-007 no sound | no audio capability exists |
| F13-FR-008 tiny/local assets | inline critical CSS/SVG + local footer JS |
| F13-FR-009 File 20 shell integration | exact `sabri_shell_welcome_intro_invoke` hook |
| F13-FR-010 File 25 tokens | `sabri_shell_file25_visual_contract` + governed green fallback |
| F13-FR-011 admin preview | authenticated `/welcome-intro-preview/` states |
| F13-FR-012 config governance | Settings validation, revision lock, bounded audit |
| F13-FR-013 failure behavior | hidden-by-default + JS fail-open |
| F13-FR-014 aggregate analytics | `Analytics`, off by default, no identifiers |
| F13-FR-015 kill switch | File 20 SafeMode + `SWI_DISABLE_INTRO` + assurance filter |

## Companion boundaries

### File 20
Current repository code invokes File 13 through `sabri_shell_welcome_intro_invoke` with owner `file-20-shell-placement`, a semantic contract version, route eligibility, layout mode, and explicit preference ownership by File 13. File 13 rejects fabricated/non-File-20 invocation context.

### File 25
File 13 consumes the same validated visual contract surface used by File 20. If File 25 is absent or invalid, only continuity fallback tokens are used; no second theme/design system is created.

### File 00
File 13 does not clone identity, roles or entitlement data. Administrative authorization is capability-based and exposes `swi_intro_authorization_decision` as the adapter point for a stricter File 00 provider.

### File 24
File 13 retains native security controls and exposes `swi_intro_safe_mode_active` for assurance-level suppression. It never creates a second incident/security center.

## Status law

Source implementation is not proof of packaging, CI, staging, production deployment, or operational readiness. Those statuses require their own evidence.
