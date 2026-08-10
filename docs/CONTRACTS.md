# Versioned Integration Contracts — File 13 / 1.1.0

## Public PHP contract

Canonical function:

```php
swi_get_welcome_intro_contract(): array
```

Contract name: `sabri.file13.welcome-intro`, version `1.1.0`.

The returned contract identifies File 13 ownership, render/replay helpers, restriction-only integration hooks, browser event names, privacy class, 30-day minimum recurrence, fail-open behavior and the exact 18 Future Welcome enhancement IDs.

## File 20 — shell / route context

- Filter `sabri_shell_module_registry`: adds read-only File 13 metadata.
- Action `sabri_shell_welcome_intro`: invokes the idempotent File 13 render callback.
- Callback `swi_render_welcome_intro`: duplicate guarded.
- Filter `swi_runtime_config`: **restriction-only**. A companion may lengthen recurrence, narrow routes/schedule, add suppressions or disable behavior. It cannot shorten File 13 recurrence, broaden eligible routes, remove protected suppressions, widen schedule, replace File 13 copy, opt analytics in or re-enable a local kill decision.
- Filter `swi_eligibility_decision`: last-mile suppression only. It cannot lift a local denial; malformed output fails closed.

## File 00 — identity / account authority

- Filter `swi_manage_capability`: maps ordinary administration surfaces to an approved File 00 capability where available.
- Filter `swi_founder_identity_asserted`: **required explicit canonical Founder identity assertion** before File 13 can execute a Founder visual approval. Default is `false`; File 13 never infers Founder status from `manage_options`, role/name/email/user ID.
- Filter `swi_founder_approval_capability`: optional additional capability mapping after Founder identity has been asserted.
- Filter `swi_user_last_seen_timestamp`: canonical external account recurrence timestamp where supplied.
- Filter `swi_user_never_show`: canonical external Never Show Again state where supplied.
- Filter `swi_external_preference_store_active`: prevents duplicate local account writes when a canonical external preference store is active.
- Authenticated REST `/dismiss` and `/preference` remain nonce/idempotency/rate-limit protected and re-use the current logged-in identity rather than accepting an arbitrary subject user ID.

## File 24 — security/privacy assurance

- Constant `SABRI_PLATFORM_SAFE_MODE`.
- Constant `SWI_DISABLE` for immediate scoped emergency disable.
- Filter `swi_force_disabled`.
- Filter `sabri_platform_safe_mode` with module identifier `file-13-welcome-intro`.
- Safe-mode/kill denials are hard boundaries; signed replay never bypasses them.

## File 25 — visual-system ownership

CSS consumes approved `--sabri-color-*` tokens including primary, primary-strong, accent, ink, surface and focus. Exact local primary fallback is Sabri Green `#087A4E`; orange remains contextual accent. Local values are resilient fallbacks only, not a second design-system authority.

## Replay and preview contracts

- `swi_get_welcome_intro_replay_url()` creates a same-origin signed replay URL. External destinations are rejected before the nonce is attached.
- Preview route `/welcome-intro-preview/` requires authenticated management capability + nonce and is noindex/no-cache.
- Replay bypasses recurrence/Never Show Again only after hard request/route/schedule/safe-mode gates pass.

## PWA/offline adapter

- Filter `sabri_pwa_precache_assets` exposes File 13's local CSS/JS/SVG asset URLs to the platform PWA owner.
- File 13 **does not register, replace or own a service worker**.

## Browser events

- `swi:shown`
- `swi:skipped`
- `swi:completed`
- `swi:closed`
- `swi:error`
- `swi:variant`
- `swi:replay`

Event details are bounded to configuration/experience/preview/variant/failure context required by the File 13 experience. They do not contain user identity, IP address, user-agent or a fingerprint identifier.
