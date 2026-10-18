# File 13 — Sabri Welcome Intro Animation

Compatibility and migration implementation for historical **File 13** of the Sabri Social Homeopathy Platform.

## Governing scope

Current cross-file governance classifies File 13 as **historical compatibility only**. File 20 owns welcome invocation, route eligibility and recurrence/session/frequency state. File 25 owns the accessible green responsive presentation. File 13 owns neither active public rendering nor frequency state.

The historical public runtime is **fail-closed in this source candidate**. Version 1.0.1 removes its public hook, public analytics endpoint, writable configuration surfaces and persistence behavior. An authenticated preview remains solely for migration inspection. The original approved File 13 plan assigns active welcome/session ownership to File 13; permanent ownership transfer still requires the dated Founder-approved change-control evidence required by that plan.

Current governing integration:
- File 01 registry integration is explicit/operator-triggered and records File 13 as compatibility/migration scope only.
- File 24 receives `spcrc/file13_contract_state`; compatibility also requires current schema, synced File 01 registry and no configuration audit gap. This source-runtime compatibility signal is not deployment or governance approval.
- File 20 must implement invocation/frequency under its own current contract; File 13 no longer registers `sabri_shell_welcome_intro_invoke`.
- File 25 remains the only owner of active welcome presentation and accessibility.
- Historical orange-primary, forced-eight-second, File-13 frequency storage and File-13 public rendering are inactive.

## Status

This repository represents source-code implementation only. It is not, by itself, evidence of staging acceptance, live deployment, or operational completion.

## Package

Current source candidate plugin folder: `sabri-welcome-intro-13` (compatibility-only release `1.0.1`). Original File 13 plan specifies `13-sabri-welcome-intro`; package-identity change-control approval remains unverified.

See:
- `docs/TRACEABILITY.md`
- `docs/TWENTY-PASS-AUDIT.md`
- `docs/CROSS-CONTRACT-AUDIT-2026-10-07.md`
- `docs/MIGRATION.md`
- `docs/ROLLBACK.md`
