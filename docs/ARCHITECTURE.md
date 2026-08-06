# Architecture and Ownership

File 13 owns only the welcome-intro semantic experience, its fallback configuration, device/session state, optional aggregate events and local operational diagnostics.

It does not own global navigation/layout (File 20), identity/roles (File 00), assurance governance (File 24), or design-system truth (File 25). It consumes versioned filters/actions and maintains a safe standalone fallback until those providers are active.

## Components

- `SWI_Config`: versioned safe defaults, sanitization, optimistic writes and bounded audit.
- `SWI_Eligibility`: server-side route/request/schedule/account/safe-mode decision.
- `SWI_Renderer`: hidden-by-default semantic dialog and signed preview route.
- `welcome-intro.js`: client session/timestamp, accessibility lifecycle and fail-open orchestration.
- `SWI_REST`: authenticated dismissal, optional public aggregate event and admin health endpoint.
- `SWI_Analytics`: 90-day bounded aggregate counters only.
- `SWI_Privacy`: WordPress export, erasure and account reset.
- `SWI_Contracts`: File 20 registry and slot.
- `SWI_System_Check`: privacy-safe configuration/asset/contract status.
- `SWI_Activator`: idempotent legacy migration and rewrite registration.

## State model

- Intro: `eligible → ready → playing → skipped|completed|closed → frequency_suppressed`.
- Configuration: `active version N → validated command → active version N+1`; stale expected version fails closed.
- Failure: `asset/storage/runtime error → overlay removed → underlying page remains usable`.

No duplicate canonical object, queue, table, navigation wrapper or design-system store is created.
