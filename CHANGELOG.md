# Changelog

## 1.0.1 — cross-contract ownership correction

- Reconciled File 13 with the current File 24 and File 25 contracts: File 13 is historical compatibility only; File 20 owns invocation/frequency and File 25 owns presentation.
- Removed registration of the public File 13 renderer, eligibility resolver, analytics endpoints and configuration-write surfaces.
- Forced all legacy activation and analytics state to disabled during reads, writes, activation and schema upgrade.
- Added the File 24 `spcrc/file13_contract_state` assurance adapter, which blocks on any legacy public-runtime reactivation.
- Converted the File 01 manifest to compatibility/migration scope and prevented existing active registry state from being silently preserved.
- Retained an authenticated, non-persistent historical preview for migration inspection only.
- Added exact-head cross-contract traceability and negative regression gates against reintroducing public rendering, browser persistence or analytics.
- Staging, deployment, DB migration and live verification remain unverified.

## 1.0.0 — source completion candidate

- Implemented File 20 exact welcome invocation contract.
- Added canonical File 25 Sabri Green visual-token consumption with safe fallback.
- Added first-eligible route handling, >=30-day dismissal recurrence, Skip/Escape, reduced motion and screen-reader-safe non-modal presentation.
- Added fail-open behavior, admin preview, governed configuration, optimistic concurrency, audit history, optional aggregate analytics, health status and Safe Mode/kill switches.
- Added migration, rollback, privacy, security, traceability, twenty-pass audit and CI/static contract tests.
- Explicitly did not claim staging/live/operational completion.

- Added explicit File 01 canonical registry/preview-route synchronization.
- Changed fresh-install public activation to fail-closed.
- Hardened configuration and aggregate metrics with compare-and-swap concurrency control.
- Validated the exact File 25 owner/version contract and canonical token keys.
- Added deterministic ZIP/SHA-256 build verification and executable JavaScript behavior tests.

- Prevented companion authorization adapters from widening the native WordPress privilege gate.
- Kept configuration-audit gap evidence sticky and health-visible until explicit reconciliation.
