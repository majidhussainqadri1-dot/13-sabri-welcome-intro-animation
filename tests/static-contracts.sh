#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
plugin="$root/sabri-welcome-intro"
pass=0
check() { if eval "$2"; then printf 'PASS: %s\n' "$1"; pass=$((pass+1)); else printf 'FAIL: %s\n' "$1"; exit 1; fi; }
contains() { grep -Fq -- "$2" "$1"; }
not_contains() { ! grep -Fq -- "$2" "$1"; }
check 'plugin declares version 1.1.0' "contains '$plugin/sabri-welcome-intro.php' 'Version: 1.1.0' && contains '$plugin/sabri-welcome-intro.php' \"SWI_VERSION', '1.1.0\""
check '30-day minimum exists in server contract' "contains '$plugin/includes/class-swi-config.php' \"'frequency_days'                => 30\" && contains '$plugin/includes/class-swi-config.php' 'max( 30'"
check 'historical forced eight-second timeout is not the default' "contains '$plugin/includes/class-swi-config.php' \"'duration_ms'                   => 0\" && not_contains '$plugin/includes/class-swi-config.php' 'min( 8000'"
check 'governing healing identity claim is the default' "contains '$plugin/includes/class-swi-config.php' 'The Tridimensional Healing System of Soul, Vital Force, and Matter'"
check 'guest timestamp cookie uses SameSite Lax' "contains '$plugin/assets/js/welcome-intro.js' 'SameSite=Lax'"
check 'overlay is hidden in markup and CSS by default' "contains '$plugin/includes/class-swi-renderer.php' 'tabindex=\"-1\" hidden' && contains '$plugin/assets/css/welcome-intro.css' '.swi-intro[hidden]'"
check 'primary identity uses exact Sabri Green with contextual orange' "grep -Fqi '#087a4e' '$plugin/assets/css/welcome-intro.css' && grep -Fqi '#087a4e' '$plugin/assets/images/sabri-sh-logo.svg' && grep -Fqi '#ff8a1f' '$plugin/assets/css/welcome-intro.css' && ! grep -Rqi '#087a3e' '$plugin'"
check 'SVG is path based and has no text element' "contains '$plugin/assets/images/sabri-sh-logo.svg' '<path' && not_contains '$plugin/assets/images/sabri-sh-logo.svg' '<text'"
check 'no autoplay sound or audio element exists' "! grep -RniE '<audio|Audio\\(|autoplay.*sound' '$plugin' --include='*.php' --include='*.js'"
check 'no remote runtime scripts or styles exist' "! grep -RniE 'wp_enqueue_(script|style).*https?://' '$plugin' --include='*.php'"
check 'admin writes require capability and nonce' "contains '$plugin/includes/class-swi-admin.php' 'swi_current_user_can_manage()' && contains '$plugin/includes/class-swi-admin.php' 'check_admin_referer'"
check 'Founder visual approval is separately capability gated' "contains '$plugin/includes/class-swi-admin.php' 'swi_current_user_can_approve()' && contains '$plugin/includes/class-swi-admin.php' 'swi_approve_visual'"
check 'preview and replay are signed and preview is noindex' "contains '$plugin/includes/class-swi-renderer.php' 'wp_verify_nonce' && contains '$plugin/includes/class-swi-renderer.php' 'swi_public_replay' && contains '$plugin/includes/class-swi-renderer.php' 'noindex'"
check 'kill-switch contracts exist' "contains '$plugin/includes/class-swi-eligibility.php' 'SABRI_PLATFORM_SAFE_MODE' && contains '$plugin/includes/class-swi-eligibility.php' 'swi_force_disabled'"
check 'runtime configuration integration is restriction-only' "contains '$plugin/includes/class-swi-config.php' 'restrict_runtime_config' && contains '$plugin/includes/class-swi-config.php' 'array_intersect' && contains '$plugin/includes/class-swi-config.php' \"array_merge( (array) \\$local['suppressed_prefixes'], (array) \\$runtime['suppressed_prefixes'] )\""
check 'eligibility integration cannot lift a local denial' "contains '$plugin/includes/class-swi-eligibility.php' \"empty( \\$decision['eligible'] ) && ! empty( \\$filtered['eligible'] )\""
check 'File 20 registry and slot contracts exist' "contains '$plugin/includes/class-swi-contracts.php' 'sabri_shell_module_registry' && contains '$plugin/includes/class-swi-contracts.php' 'sabri_shell_welcome_intro'"
check 'File 25 token fallbacks are consumed' "contains '$plugin/assets/css/welcome-intro.css' '--sabri-color-primary'"
check 'privacy export and erasure include Future preferences' "contains '$plugin/includes/class-swi-privacy.php' 'wp_privacy_personal_data_exporters' && contains '$plugin/includes/class-swi-privacy.php' 'wp_privacy_personal_data_erasers' && contains '$plugin/includes/class-swi-privacy.php' 'USER_META_PROFILE'"
check 'cross-device preference mutation is authenticated and idempotent' "contains '$plugin/includes/class-swi-rest.php' \"'/preference'\" && contains '$plugin/includes/class-swi-rest.php' \"'permission_callback' => static function () { return is_user_logged_in(); }\" && contains '$plugin/includes/class-swi-rest.php' 'swi_pref_'"
check 'aggregate telemetry has no IP or user-agent fingerprint fields' "not_contains '$plugin/includes/class-swi-analytics.php' 'REMOTE_ADDR' && not_contains '$plugin/includes/class-swi-analytics.php' 'HTTP_USER_AGENT' && contains '$plugin/includes/class-swi-analytics.php' 'ALLOWED_EVENTS'"
check 'uninstall is non-destructive by default' "contains '$plugin/uninstall.php' 'SWI_PURGE_ON_UNINSTALL'"
check 'no private key material is present' "! grep -RniE 'BEGIN (RSA|OPENSSH|EC) PRIVATE KEY' '$root' --exclude-dir=release"
printf 'STATIC CONTRACTS: %d passed, 0 failed\n' "$pass"
