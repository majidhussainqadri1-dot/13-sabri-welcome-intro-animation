# Rollback

The safest rollback is feature suppression, not data mutation.

1. Set the File 13 status to disabled, define `SWI_DISABLE_INTRO=true`, or activate File 20 Safe Mode/emergency disable.
2. Confirm public content is immediately available with no intro.
3. Clear only File-13-owned caches/assets if applicable; do not purge companion data.
4. Revert the plugin package only after validating WordPress/PHP compatibility.
5. Preserve configuration/audit options by default.
6. Re-enable only after the health check and the affected regression suite pass.

Uninstall is non-destructive unless an operator deliberately defines `SWI_PURGE_ON_UNINSTALL=true`.
