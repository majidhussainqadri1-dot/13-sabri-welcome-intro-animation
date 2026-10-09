# Rollback

Version 1.0.1 is itself the safe suppression boundary.

1. Confirm File 13 health reports `legacy_public_disabled=true`, schema migration is current, File 01 registry is synced, there is no configuration audit gap, and File 24 contract state `compatible`.
2. Confirm no File 13 public renderer, analytics AJAX action or configuration write route is registered.
3. Clear only File-13-owned caches/assets if applicable; do not purge companion data.
4. Revert the plugin package only after validating WordPress/PHP compatibility.
5. Preserve configuration/audit options by default.
6. Do not re-enable the historical File 13 runtime without an approved change. Current File 24/25 source contracts assign active welcome behavior to File 20 and File 25, subject to Founder change-control verification.

Uninstall is non-destructive unless an operator deliberately defines `SWI_PURGE_ON_UNINSTALL=true`. A source-level `compatible` result is not approval to deploy or revert on live: verify exact installed code, DB schema, migration state, backup/restore proof, rollback artifact, and Founder authorization before production action.
