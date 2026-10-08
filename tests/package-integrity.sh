#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT
mkdir -p "$TMP/project/tools" "$TMP/project/sabri-welcome-intro-13"
cp "$ROOT/tools/build-package.sh" "$TMP/project/tools/build-package.sh"
printf 'not-for-public-package\n' > "$TMP/private-secret"
ln -s "$TMP/private-secret" "$TMP/project/sabri-welcome-intro-13/secret.txt"
if SOURCE_DATE_EPOCH=1791172800 bash "$TMP/project/tools/build-package.sh" "$TMP/unsafe" >"$TMP/unsafe.log" 2>&1; then
  echo "FAIL: package builder dereferenced a source symlink" >&2
  exit 1
fi
test ! -f "$TMP/unsafe/sabri-welcome-intro-13-1.0.1.zip" || { echo "FAIL: unsafe package published" >&2; exit 1; }
rm "$TMP/project/sabri-welcome-intro-13/secret.txt"
mkdir -p "$TMP/foreign"
printf 'outside-directory\n' > "$TMP/foreign/keep.txt"
ln -s "$TMP/foreign" "$TMP/project/sabri-welcome-intro-13/foreign-dir"
if SOURCE_DATE_EPOCH=1791172800 bash "$TMP/project/tools/build-package.sh" "$TMP/unsafe-dir" >"$TMP/unsafe-dir.log" 2>&1; then
  echo "FAIL: package builder accepted a symlinked directory" >&2
  exit 1
fi
rm "$TMP/project/sabri-welcome-intro-13/foreign-dir"
printf 'safe-package-content\n' > "$TMP/project/sabri-welcome-intro-13/example.txt"
SOURCE_DATE_EPOCH=1791172800 bash "$TMP/project/tools/build-package.sh" "$TMP/output-a" >/dev/null
SOURCE_DATE_EPOCH=1791172800 bash "$TMP/project/tools/build-package.sh" "$TMP/output-b" >/dev/null
NAME="sabri-welcome-intro-13-1.0.1.zip"
cmp "$TMP/output-a/$NAME" "$TMP/output-b/$NAME"
cmp "$TMP/output-a/$NAME.sha256" "$TMP/output-b/$NAME.sha256"
(cd "$TMP/output-a" && sha256sum -c "$NAME.sha256")
(cd "$TMP/output-b" && sha256sum -c "$NAME.sha256")
printf 'unchanged\n' > "$TMP/private-secret"
rm "$TMP/output-a/$NAME.sha256"
ln -s "$TMP/private-secret" "$TMP/output-a/$NAME.sha256"
SOURCE_DATE_EPOCH=1791172800 bash "$TMP/project/tools/build-package.sh" "$TMP/output-a" >/dev/null
test "$(cat "$TMP/private-secret")" = "unchanged"
test ! -L "$TMP/output-a/$NAME.sha256"
(cd "$TMP/output-a" && sha256sum -c "$NAME.sha256")
echo "File 13 package symlink, portable checksum and atomic sidecar: PASS"
