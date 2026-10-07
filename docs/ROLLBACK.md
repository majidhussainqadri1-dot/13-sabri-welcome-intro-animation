# Rollback

Version 1.0.1 is itself the safe suppression boundary.

1. Confirm File 13 health reports `legacy_public_disabled=true` and File 24 contract state `compatible`.
2. Confirm no File 13 public renderer, analytics AJAX action or configuration write route is registered.
3. Clear only File-13-owned caches/assets if applicable; do not purge companion data.
4. Revert the plugin package only after validating WordPress/PHP compatibility.
5. Preserve configuration/audit options by default.
6. Do not re-enable the historical File 13 runtime. Active welcome behavior belongs in File 20 and File 25.

Uninstall is non-destructive unless an operator deliberately defines `SWI_PURGE_ON_UNINSTALL=true`.
