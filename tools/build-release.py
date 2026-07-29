#!/usr/bin/env python3
"""Build a byte-reproducible File 13 WordPress plugin ZIP."""

from __future__ import annotations

import hashlib
import pathlib
import sys
import zipfile

ROOT = pathlib.Path(__file__).resolve().parents[1]
PLUGIN_DIR = ROOT / "sabri-welcome-intro"
RELEASE_DIR = ROOT / "release"
OUTPUT = RELEASE_DIR / "13-sabri-welcome-intro-animation-0.2.0.zip"
FIXED_TIME = (2026, 7, 30, 0, 0, 0)


def build(output: pathlib.Path) -> str:
    output.parent.mkdir(parents=True, exist_ok=True)
    files = sorted(path for path in PLUGIN_DIR.rglob("*") if path.is_file())

    with zipfile.ZipFile(output, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for source in files:
            relative = source.relative_to(ROOT).as_posix()
            info = zipfile.ZipInfo(relative, FIXED_TIME)
            info.compress_type = zipfile.ZIP_DEFLATED
            info.create_system = 3
            info.external_attr = (0o100644 & 0xFFFF) << 16
            archive.writestr(info, source.read_bytes(), compress_type=zipfile.ZIP_DEFLATED, compresslevel=9)

    digest = hashlib.sha256(output.read_bytes()).hexdigest()
    return digest


def main() -> int:
    digest = build(OUTPUT)
    checksum_path = RELEASE_DIR / "SHA256SUMS"
    checksum_path.write_text(f"{digest}  {OUTPUT.name}\n", encoding="utf-8")
    print(f"{digest}  {OUTPUT}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
