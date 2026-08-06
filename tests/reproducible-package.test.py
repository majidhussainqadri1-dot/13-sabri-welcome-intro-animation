#!/usr/bin/env python3
from pathlib import Path
import hashlib, subprocess, tempfile, shutil, zipfile
root = Path(__file__).resolve().parents[1]
subprocess.run(['python3', str(root/'tools/build-release.py')], check=True, stdout=subprocess.DEVNULL)
archive = root/'release/13-sabri-welcome-intro-animation-1.0.0.zip'
first = hashlib.sha256(archive.read_bytes()).hexdigest()
with tempfile.TemporaryDirectory() as d:
    saved = Path(d)/'first.zip'; shutil.copy2(archive, saved)
    subprocess.run(['python3', str(root/'tools/build-release.py')], check=True, stdout=subprocess.DEVNULL)
    second = hashlib.sha256(archive.read_bytes()).hexdigest()
    assert first == second, (first, second)
    with zipfile.ZipFile(archive) as z:
        names = z.namelist(); assert names and all(n.startswith('sabri-welcome-intro-13/') for n in names)
        assert not any('..' in Path(n).parts or n.startswith('/') for n in names)
        assert 'sabri-welcome-intro-13/sabri-welcome-intro.php' in names
print(f'REPRODUCIBLE PACKAGE: PASS {first}')
