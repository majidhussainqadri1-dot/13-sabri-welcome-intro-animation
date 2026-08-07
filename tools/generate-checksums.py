#!/usr/bin/env python3
from __future__ import print_function
from pathlib import Path
import hashlib
import sys

root = Path(__file__).resolve().parents[1]
ledger = root / 'CHECKSUMS-1.0.0.sha256'
excluded = {
    'CHECKSUMS-1.0.0.sha256',
    '.github/workflows/file13-forty-round-apply.yml',
    '.github/file13-forty-round-delete-list.txt',
}

def eligible(path):
    rel = path.relative_to(root).as_posix()
    if rel in excluded or rel.startswith('.github/file13-forty-round.part'):
        return False
    if '.git' in path.parts or '__pycache__' in path.parts:
        return False
    if rel == 'release/13-sabri-welcome-intro-animation-1.0.0.zip':
        return False
    if rel.startswith('release/') and rel != 'release/SHA256SUMS':
        return False
    allowed_roots = ('sabri-welcome-intro/', 'tests/', 'tools/', 'docs/')
    allowed_files = {
        '.github/workflows/corrective-integrity.yml',
        '.gitignore', 'CHANGELOG.md', 'CORRECTION-REPORT.md', 'DEPENDENCIES.md',
        'FORTY-ROUND-QA-EVIDENCE.md', 'GOVERNANCE-NOTICE.md', 'MANIFEST-1.0.0.json',
        'MANIFEST.md', 'QA-REVIEW.md', 'README.md', 'REVIEW-FINDINGS.md',
        'SBOM.spdx.json', 'SOURCE-PROVENANCE.md', 'STAGING-ACCEPTANCE.md',
        'STATUS.md', 'release/SHA256SUMS',
    }
    return rel in allowed_files or rel.startswith(allowed_roots)

def render():
    rows = []
    for path in sorted(p for p in root.rglob('*') if p.is_file() and eligible(p)):
        rel = path.relative_to(root).as_posix()
        rows.append('%s  %s' % (hashlib.sha256(path.read_bytes()).hexdigest(), rel))
    return '\n'.join(rows) + '\n'

expected = render()
if '--check' in sys.argv:
    actual = ledger.read_text(encoding='utf-8') if ledger.exists() else ''
    if actual != expected:
        print('checksum ledger mismatch', file=sys.stderr)
        sys.exit(1)
    print('SOURCE/EVIDENCE CHECKSUMS: verified')
else:
    ledger.write_text(expected, encoding='utf-8')
    print('SOURCE/EVIDENCE CHECKSUMS: wrote %d entries' % len(expected.splitlines()))
