#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEST="${1:-$ROOT/dist}"
SOURCE_DATE_EPOCH="${SOURCE_DATE_EPOCH:-1791172800}"
command -v python3 >/dev/null 2>&1 || { echo "python3 is required" >&2; exit 2; }

python3 - "$ROOT" "$DEST" "$SOURCE_DATE_EPOCH" <<'PY'
import hashlib
import os
from pathlib import Path
import sys
import tempfile
import time
import zipfile

root, dest, epoch = Path(sys.argv[1]), Path(sys.argv[2]), int(sys.argv[3])
plugin = root / "sabri-welcome-intro-13"
if plugin.is_symlink() or not plugin.is_dir():
    raise ValueError("Canonical plugin source must be a real directory, not a symlink")
name = "sabri-welcome-intro-13-1.0.1.zip"
dest.mkdir(parents=True, exist_ok=True)
timestamp = time.gmtime(max(epoch, 315532800))[:6]
with tempfile.NamedTemporaryFile(prefix=".swi-", suffix=".zip", dir=dest, delete=False) as tmp:
    temporary = Path(tmp.name)
try:
    with zipfile.ZipFile(temporary, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for source in sorted(plugin.rglob("*")):
            # ZIP writes dereference symlinks: reject them before reading any bytes.
            # This also rejects symlinked directories instead of silently skipping them.
            if source.is_symlink():
                raise ValueError(f"Symlink forbidden in installable source: {source.relative_to(root)}")
            if not source.is_file():
                continue
            member = zipfile.ZipInfo(str(source.relative_to(root)).replace(os.sep, "/"), timestamp)
            member.compress_type = zipfile.ZIP_DEFLATED
            member.external_attr = 0o100644 << 16
            archive.writestr(member, source.read_bytes(), compress_type=zipfile.ZIP_DEFLATED, compresslevel=9)
    os.replace(temporary, dest / name)
finally:
    temporary.unlink(missing_ok=True)
digest = hashlib.sha256((dest / name).read_bytes()).hexdigest()
with tempfile.NamedTemporaryFile(mode="w", encoding="utf-8", prefix=".swi-", suffix=".sha256", dir=dest, delete=False) as checksum_tmp:
    checksum_tmp.write(digest + "  " + name + "\n")
    checksum_temp = Path(checksum_tmp.name)
try:
    # Atomic replacement also prevents a pre-existing checksum symlink from
    # redirecting the write outside the requested output directory.
    os.replace(checksum_temp, dest / (name + ".sha256"))
finally:
    checksum_temp.unlink(missing_ok=True)
print(dest / name)
print(digest)
PY
