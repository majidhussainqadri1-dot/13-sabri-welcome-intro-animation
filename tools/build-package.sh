#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd -P)"
PLUGIN="sabri-welcome-intro-13"
DEST="${1:-$ROOT/dist}"
SOURCE_DATE_EPOCH="${SOURCE_DATE_EPOCH:-1791172800}"
ZIP_NAME="sabri-welcome-intro-13-1.0.1.zip"

command -v zip >/dev/null 2>&1 || { echo "zip is required" >&2; exit 2; }
command -v sha256sum >/dev/null 2>&1 || { echo "sha256sum is required" >&2; exit 2; }
[[ ! -L "$DEST" ]] || { echo "Destination symlinks are not permitted" >&2; exit 2; }
mkdir -p -- "$DEST"
DEST="$(cd "$DEST" && pwd -P)"
if [[ "$DEST" == "/" || "$DEST" == "$ROOT" || "$ROOT" == "$DEST/"* || "$DEST" == "$ROOT/$PLUGIN" || "$DEST" == "$ROOT/$PLUGIN/"* ]]; then
  echo "Unsafe package destination" >&2
  exit 2
fi

# Only the temporary directory created by mktemp is eligible for cleanup.
WORK="$(mktemp -d "$DEST/.swi-build.XXXXXXXX")"
cleanup() {
  if [[ -n "${WORK:-}" && -d "$WORK" && "$WORK" == "$DEST/.swi-build."* ]]; then
    rm -r -- "$WORK"
  fi
}
trap cleanup EXIT

cp -R "$ROOT/$PLUGIN" "$WORK/$PLUGIN"
find "$WORK/$PLUGIN" -exec touch -h -d "@$SOURCE_DATE_EPOCH" {} +
(
  cd "$WORK"
  find "$PLUGIN" -type f -print | LC_ALL=C sort | zip -X -q "$ZIP_NAME" -@
  sha256sum "$ZIP_NAME" > "$ZIP_NAME.sha256"
)
mv -f -- "$WORK/$ZIP_NAME" "$DEST/$ZIP_NAME"
mv -f -- "$WORK/$ZIP_NAME.sha256" "$DEST/$ZIP_NAME.sha256"
echo "$DEST/$ZIP_NAME"
cat "$DEST/$ZIP_NAME.sha256"
