# File 13 Corrective Manifest

## Corrected plugin tree

| Path | Purpose |
|---|---|
| `sabri-welcome-intro/sabri-welcome-intro.php` | Plugin header, version, bootstrap |
| `sabri-welcome-intro/includes/class-swi-activator.php` | Safe default activation option |
| `sabri-welcome-intro/includes/class-swi-admin.php` | Settings and signed administrator preview URL |
| `sabri-welcome-intro/includes/class-swi-plugin.php` | Module registration |
| `sabri-welcome-intro/includes/class-swi-renderer.php` | Preview authorization, no-cache/noindex, early bootstrap, assets, markup |
| `sabri-welcome-intro/assets/js/welcome-bootstrap.js` | Immediate session claim and fail-open display authorization |
| `sabri-welcome-intro/assets/js/welcome-intro.js` | Accessible dialog lifecycle, timing, cleanup, late-runtime guard |
| `sabri-welcome-intro/assets/css/welcome-intro.css` | Hidden default, animations, CSS fail-safe, responsive/reduced motion |
| `sabri-welcome-intro/assets/images/sabri-sh-logo.svg` | Approved circular path-based SH roundel |
| `sabri-welcome-intro/readme.txt` | WordPress plugin documentation and changelog |
| `sabri-welcome-intro/uninstall.php` | Owned-option cleanup only |

## Verification and release files

| Path | Purpose |
|---|---|
| `tests/bootstrap-runtime.test.js` | Executable session/bootstrap behavior tests |
| `tests/runtime-behavior.test.js` | Executable modal lifecycle and fail-open tests |
| `tests/source-contract.test.js` | Security, brand, accessibility, and release contracts |
| `tests/php-load-smoke.php` | PHP load/activation/hook smoke test |
| `tests/static-security.sh` | Prohibited primitives and security invariant checks |
| `tests/reproducible-package.test.py` | Two-build byte-reproducibility and ZIP structure test |
| `tools/build-release.py` | Deterministic release builder |
| `.github/workflows/corrective-integrity.yml` | Corrective CI |
| `release/13-sabri-welcome-intro-animation-0.2.0.zip` | Corrected installable plugin package |
| `release/SHA256SUMS` | Corrected package checksum |
| `CHECKSUMS-CORRECTED.sha256` | Corrective branch integrity ledger |
| `CORRECTION-REPORT.md` | Defect remediation report |
| `STAGING-ACCEPTANCE.md` | Mandatory Hostinger acceptance matrix |
| `GOVERNANCE-NOTICE.md` | Outstanding repository visibility decision |
