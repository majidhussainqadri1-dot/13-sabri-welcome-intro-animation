# File 13 — Sabri Welcome Intro Animation

Production-oriented source implementation for **File 13** of the Sabri Social Homeopathy Platform.

## Governing scope

File 13 owns the accessible, non-blocking welcome intro and its privacy-minimal recurrence/session preference state. It does **not** own the global shell, navigation, theme, profile/timeline design system, authentication, clinical routes, or security assurance center.

Current governing integration:
- File 20 provides route/layout eligibility and invokes File 13 through `sabri_shell_welcome_intro_invoke`.
- File 25 provides visual/design tokens; Sabri Green `#087A4E` is the canonical primary fallback.
- File 13 fails open: if JavaScript, storage, dependencies, or configuration fail, ordinary page content remains available.
- Skip/Escape and reduced-motion behavior are mandatory.
- A skip/completion dismissal suppresses reappearance for at least 30 days.
- The superseded orange-primary / forced-eight-second behavior is not implemented.

## Status

This repository represents source-code implementation only. It is not, by itself, evidence of staging acceptance, live deployment, or operational completion.

## Package

Canonical plugin folder: `sabri-welcome-intro-13`

See:
- `docs/TRACEABILITY.md`
- `docs/TWENTY-PASS-AUDIT.md`
- `docs/MIGRATION.md`
- `docs/ROLLBACK.md`
