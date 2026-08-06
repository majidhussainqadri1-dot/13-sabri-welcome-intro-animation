#!/usr/bin/env python3
from pathlib import Path
from zipfile import ZipFile, ZipInfo, ZIP_STORED
import hashlib, json, sys
root = Path(__file__).resolve().parents[1]
source = root / 'sabri-welcome-intro'
outdir = root / 'release'; outdir.mkdir(exist_ok=True)
out = outdir / '13-sabri-welcome-intro-animation-1.0.0.zip'
top = 'sabri-welcome-intro-13'
files = sorted(p for p in source.rglob('*') if p.is_file() and '__pycache__' not in p.parts)
with ZipFile(out, 'w', compression=ZIP_STORED) as z:
    for p in files:
        rel = p.relative_to(source).as_posix()
        info = ZipInfo(f'{top}/{rel}', (1980, 1, 1, 0, 0, 0))
        info.compress_type = ZIP_STORED
        info.external_attr = 0o100644 << 16
        z.writestr(info, p.read_bytes(), compress_type=ZIP_STORED)
digest = hashlib.sha256(out.read_bytes()).hexdigest()
(outdir / 'SHA256SUMS').write_text(f'{digest}  {out.name}\n', encoding='utf-8')
manifest = {
    'plugin': 'Sabri Welcome Intro Animation', 'version': '1.0.0', 'top_level_folder': top,
    'archive': out.name, 'sha256': digest, 'bytes': out.stat().st_size, 'entries': len(files),
    'files': [{'path': p.relative_to(source).as_posix(), 'bytes': p.stat().st_size, 'sha256': hashlib.sha256(p.read_bytes()).hexdigest()} for p in files],
}
(root / 'MANIFEST-1.0.0.json').write_text(json.dumps(manifest, indent=2, ensure_ascii=False) + '\n', encoding='utf-8')
print(json.dumps({k: manifest[k] for k in ('archive','sha256','bytes','entries','top_level_folder')}, indent=2))
