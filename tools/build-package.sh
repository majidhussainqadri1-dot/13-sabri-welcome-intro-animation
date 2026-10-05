#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN="sabri-welcome-intro-13"
DEST="${1:-$ROOT/dist}"
SOURCE_DATE_EPOCH="${SOURCE_DATE_EPOCH:-1791172800}"
ZIP_NAME="sabri-welcome-intro-13-1.0.0.zip"

command -v zip >/dev/null 2>&1 || { echo "zip is required" >&2; exit 2; }
command -v sha256sum >/dev/null 2>&1 || { echo "sha256sum is required" >&2; exit 2; }

rm -rf "$DEST"
mkdir -p "$DEST/work"
cp -R "$ROOT/$PLUGIN" "$DEST/work/$PLUGIN"

find "$DEST/work/$PLUGIN" -exec touch -h -d "@$SOURCE_DATE_EPOCH" {} +
(
  cd "$DEST/work"
  find "$PLUGIN" -type f -print | LC_ALL=C sort | zip -X -q "$DEST/$ZIP_NAME" -@
)

sha256sum "$DEST/$ZIP_NAME" > "$DEST/$ZIP_NAME.sha256"
rm -rf "$DEST/work"

echo "$DEST/$ZIP_NAME"
cat "$DEST/$ZIP_NAME.sha256"
