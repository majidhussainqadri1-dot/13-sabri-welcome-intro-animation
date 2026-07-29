#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN="$ROOT/sabri-welcome-intro"

if grep -RInE 'eval\s*\(|base64_decode\s*\(|shell_exec\s*\(|passthru\s*\(|system\s*\(' "$PLUGIN" --include='*.php'; then
  echo "Dangerous PHP execution primitive detected." >&2
  exit 1
fi

if grep -RInE 'URLSearchParams|window\.location\.search' "$PLUGIN/assets/js"; then
  echo "Client-side preview query parsing is forbidden." >&2
  exit 1
fi

grep -q "wp_verify_nonce( \$nonce, 'swi_preview' )" "$PLUGIN/includes/class-swi-renderer.php"
grep -q "current_user_can( 'manage_options' )" "$PLUGIN/includes/class-swi-renderer.php"
grep -q "data-no-optimize" "$PLUGIN/includes/class-swi-renderer.php"
grep -q "min-height: 44px" "$PLUGIN/assets/css/welcome-intro.css"

echo "Static security and accessibility invariants passed."
