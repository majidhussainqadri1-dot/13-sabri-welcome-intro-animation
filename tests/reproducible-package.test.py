#!/usr/bin/env python3
from __future__ import annotations

import hashlib
import pathlib
import tempfile
import zipfile
import importlib.util
import sys

sys.dont_write_bytecode = True

ROOT = pathlib.Path(__file__).resolve().parents[1]
MODULE_PATH = ROOT / "tools" / "build-release.py"
spec = importlib.util.spec_from_file_location("build_release", MODULE_PATH)
module = importlib.util.module_from_spec(spec)
assert spec.loader is not None
spec.loader.exec_module(module)

digest = None

with tempfile.TemporaryDirectory() as directory:
    first = pathlib.Path(directory) / "first.zip"
    second = pathlib.Path(directory) / "second.zip"
    first_digest = module.build(first)
    second_digest = module.build(second)

    assert first.read_bytes() == second.read_bytes(), "release ZIP is not byte reproducible"
    assert first_digest == second_digest

    with zipfile.ZipFile(first) as archive:
        assert archive.testzip() is None
        names = archive.namelist()
        assert names
        assert all(name.startswith("sabri-welcome-intro/") for name in names)
        assert "sabri-welcome-intro/sabri-welcome-intro.php" in names
    digest = hashlib.sha256(first.read_bytes()).hexdigest()

assert digest is not None
print(digest)
