# Source Provenance

- Repository: `majidhussainqadri1-dot/13-sabri-welcome-intro-animation`
- Main branch: historical baseline until the draft candidate is explicitly merged.
- Corrective/Future branch: `audit/file-13-source-review` (draft PR #2).
- Historical lineage: imported `0.1.1` → corrective `0.2.0` → governed/reconciled `1.0.0` → Future Welcome Experience Superset `1.1.0`.
- Governing sources for the 1.1.0 candidate: the current consolidated central governing plan, the reconciled File 13 plan, File 20 shell/suppression contract, File 24 security/privacy assurance boundary, File 25 visual-token boundary, and explicit later Founder-approved File 13 decisions.
- Authoritative repository source for a candidate is the **exact current PR head**, not a historical commit, CI run, ZIP, checksum or status document.
- Canonical plugin source: `sabri-welcome-intro/` plus repository tests/docs/tools required to verify/build it.
- Canonical installable package target: `release/13-sabri-welcome-intro-animation-1.1.0.zip`, top-level folder `sabri-welcome-intro-13/`.
- Deterministic package identity is generated from the exact source by `tools/build-release.py` and independently reproduced by `tests/reproducible-package.test.py`.
- Exact current package digest is recorded by the fresh workflow in `release/SHA256SUMS` and its uploaded artifact. A digest from an earlier head is historical only.
- `MANIFEST-1.1.0.json` is generated during the exact-head build; historical `MANIFEST-1.0.0.json` and `CHECKSUMS-1.0.0.sha256` are not current 1.1.0 proof.
- The permanent workflow additionally verifies PHP/JavaScript/static/Future regressions, translation freshness and the sequential 80-round gate.

No repository/source/package result is claimed to be Hostinger staging-accepted, live-deployed or operational unless those distinct environment gates are separately evidenced and deployment parity is proven.
