# Versioned Integration Contracts

## Public PHP contract

`sw_get_welcome_intro_contract()` is intentionally not provided; the canonical function is:

```php
swi_get_welcome_intro_contract(): array
```

Contract name: `sabri.file13.welcome-intro`, version `1.0.0`.

## File 20

- Filter `sabri_shell_module_registry`: adds read-only File 13 metadata.
- Action `sabri_shell_welcome_intro`: invokes the idempotent render callback.
- Filter `swi_runtime_config`: File 20 may supply canonical invocation/frequency/route context; File 13 re-sanitizes the result.
- Filter `swi_eligibility_decision`: last-mile eligibility/suppression decision; malformed output fails closed.
- Callback `swi_render_welcome_intro`: duplicate guarded.

## File 00

- Filter `swi_manage_capability`: maps the fallback `manage_options` capability to an approved File 00 claim/capability.
- Every settings write and preview/status read still performs a native capability check.
- Filter `swi_user_last_seen_timestamp`: preferred external account timestamp.
- Filter `swi_external_preference_store_active`: prevents duplicate local account writes when a canonical external store is active.

## File 24

- Constant `SABRI_PLATFORM_SAFE_MODE`.
- Constant `SWI_DISABLE` for immediate scoped emergency disable.
- Filter `swi_force_disabled`.
- Filter `sabri_platform_safe_mode` with module identifier `file-13-welcome-intro`.

## File 25

CSS consumes `--sabri-color-primary`, `--sabri-color-primary-strong`, `--sabri-color-accent`, `--sabri-color-ink`, `--sabri-color-surface` and `--sabri-color-focus`. Local values are accessibility-safe fallbacks, not a second design-system authority.

## Browser events

- `swi:shown`
- `swi:skipped`
- `swi:completed`
- `swi:closed`
- `swi:error`

Details contain configuration version and preview/failure state only; no identity or fingerprint.
