#!/usr/bin/env python3
from pathlib import Path
import hashlib, subprocess, tempfile, shutil, zipfile, json
root = Path(__file__).resolve().parents[1]
subprocess.run(['python3', str(root/'tools/build-release.py')], check=True, stdout=subprocess.DEVNULL)
archive = root/'release/13-sabri-welcome-intro-animation-1.1.0.zip'
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
        plugin = z.read('sabri-welcome-intro-13/sabri-welcome-intro.php').decode('utf-8')
        assert 'Version: 1.1.0' in plugin and "define( 'SWI_VERSION', '1.1.0' )" in plugin
manifest = json.loads((root/'MANIFEST-1.1.0.json').read_text(encoding='utf-8'))
assert manifest['version'] == '1.1.0' and manifest['sha256'] == first
print(f'REPRODUCIBLE PACKAGE: PASS {first}')
