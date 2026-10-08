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
name = "sabri-welcome-intro-13-1.0.1.zip"
dest.mkdir(parents=True, exist_ok=True)
timestamp = time.gmtime(max(epoch, 315532800))[:6]
with tempfile.NamedTemporaryFile(prefix=".swi-", suffix=".zip", dir=dest, delete=False) as tmp:
    temporary = Path(tmp.name)
try:
    with zipfile.ZipFile(temporary, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for source in sorted(plugin.rglob("*")):
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
(dest / (name + ".sha256")).write_text(digest + "  " + str(dest / name) + "\n")
print(dest / name)
print(digest)
PY
