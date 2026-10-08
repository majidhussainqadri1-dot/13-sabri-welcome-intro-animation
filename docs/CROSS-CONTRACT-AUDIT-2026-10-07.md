# File 13 Historical Cross-Contract Audit Baseline — 2026-10-07

> Dated evidence only. The SHAs below were current at this historical review, not necessarily now. Each future audit must re-read current main and companion HEADs; this report is not live or deployed-state evidence.

## Exact source evidence

This audit did not reuse a prior report as current truth. It re-read the exact default-branch heads:

- File 13: `3cc938f36bcc77c53bbd99d65bee761709f2f016`
- File 01: `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`
- File 20: `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`
- File 24: `a5b8d49968a7a5a7d6f3f4655bea541bf38a9acb`
- File 25: `2d02c93356b050313e30e29aeceb57080771c2a5`
- File 00: `2fa7c022ee9cd1b65432e900579512f304532442`

The decisive newer evidence is File 24's 00–26 integration matrix and File 25's current directive matrix. Both assign welcome invocation/frequency to File 20, active presentation to File 25, and File 13 to historical compatibility only.

## Twenty separate rounds

| Round | Independent lens | Finding and correction |
|---:|---|---|
| 1 | Fresh repository identity | Verified current File 13 `main`; no prior SHA assumed. |
| 2 | Fresh companion identity | Verified exact current heads for Files 00, 01, 20, 24 and 25. |
| 3 | Governing precedence | Found File 13's previous traceability stale against newer File 24/25 contracts; current precedence recorded. |
| 4 | Canonical ownership | Found active File 13 rendering/frequency ownership invalid; runtime converted to compatibility-only. |
| 5 | File 20 handoff | Found current File 20 still names File 13 as preference owner; File 13 no longer registers the legacy handoff and fails closed. |
| 6 | File 25 presentation | Found File 13 duplicating visual ownership; public File 13 presentation removed. |
| 7 | File 24 assurance | Found required `spcrc/file13_contract_state` absent; exact adapter added with blocked-on-reactivation semantics. |
| 8 | File 01 registry | Found manifest claiming active render/config capabilities; converted to compatibility/migration scope. |
| 9 | Registry transition safety | Found existing active state could be retained; sync now downgrades active/degraded records truthfully to `degraded`. |
| 10 | Public activation | Found manual settings/REST paths could enable legacy runtime; all legacy state now sanitizes and reads as disabled. |
| 11 | Browser persistence | Found File 13 owning session/localStorage recurrence; public persistence removed and preview made non-persistent. |
| 12 | Analytics/privacy | Found dormant-by-default but activatable public analytics; endpoint registration and writable toggle removed. |
| 13 | Authorization | Native capability plus stricter File 00 adapter retained for status, preview and registry sync. |
| 14 | Route exposure | Public intro remains absent; preview remains authenticated, no-store and noindex for migration inspection. |
| 15 | Sensitive routes | Removal of public renderer makes route-specific leakage structurally impossible in File 13. |
| 16 | Accessibility | Active presentation delegated to File 25; historical preview retains Skip/Escape and reduced-motion behavior only. |
| 17 | Performance | No File 13 public CSS/JS/analytics is enqueued; ordinary content path has zero File 13 rendering work. |
| 18 | Migration/rollback | Schema 1.0.1 forces suppression, clears historical cron and documents non-destructive migration. |
| 19 | QA/package | Added negative regression gates and updated deterministic package identity to 1.0.1. |
| 20 | Release truth | Source correction does not imply staging, deployment, DB migration, visual acceptance or live verification. |

## Root cause

The previous implementation treated File 20's older in-repository comments as the governing ownership rule and did not reconcile the later File 24/File 25 cross-file contracts. That allowed File 13 to duplicate frequency and visual ownership. The correction removes the duplicate capability rather than layering another conditional patch over it.

## Remaining cross-repository dependency

The active welcome feature is not complete at platform level: File 20's current source still delegates frequency to File 13, while the current governing contracts require File 20 to own it and invoke File 25's visual primitive. File 13 correctly fails closed until that owner-side correction is separately implemented and verified.

## Evidence boundaries

- Repository source: corrected candidate.
- Exact-head CI: must be verified on the correction commit.
- Package: deterministic build must be verified on the correction commit.
- Staging deployment: unverified.
- Deployed version: unverified.
- Database/schema migration: unverified.
- Live behavior: unverified.
