#!/usr/bin/env python3
from pathlib import Path
import hashlib
root = Path(__file__).resolve().parents[1]
exclude = {'CHECKSUMS-1.0.0.sha256'}
files=[]
for p in root.rglob('*'):
    if not p.is_file() or p.name in exclude or '.git' in p.parts or '__pycache__' in p.parts: continue
    if p.parts[-2:] and 'release' in p.parts and p.name.endswith('.zip'): continue
    files.append(p)
lines=[]
for p in sorted(files): lines.append(f"{hashlib.sha256(p.read_bytes()).hexdigest()}  {p.relative_to(root).as_posix()}")
(root/'CHECKSUMS-1.0.0.sha256').write_text('\n'.join(lines)+'\n', encoding='utf-8')
print(f'CHECKSUM LEDGER: {len(lines)} files')
