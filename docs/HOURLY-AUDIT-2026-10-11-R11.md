## File 13 audit — 2026-10-11, read-only ledger frozen before correction

Evidence basis: main `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`; PR #6 corrective `bfc10e11be87f1167422bf25525718c7a05fe051`; prior PR CI run `38091824451` successful (PHP 8.1/8.3). Companion main HEADs: 00 `2fa7c022ee9cd1b65432e900579512f304532442`, 01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, 20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`, 24 `a5b8d49968a7a5a7d6f3f4655bea541bf38a9acb`, 25 `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e`.

### Frozen defect ledger

| ID | Finding | Proof | Owner | Disposition |
|---|---|---|---|---|
| F13-20261011-R1-01 | `state=reduced` forced preview only sets progress duration to zero; 220ms entrance/160ms exit CSS still animates when OS media preference is not reduced | `class-renderer.php::style_block()` vs `welcome-intro.js` forceReducedMotion branch; existing JS test covers only OS media query | File 13 | One atomic source/test/docs correction in existing PR #6; exact-head CI and live browser test separately required |
| EXT-F20-OWN-01 | File 20 still calls File 13 canonical owner of frequency | File 20 `class-four-plan-harmonization.php` lines 179-201; File 24 00-26 matrix assigns frequency to File 20 | File 20 | External-owner issue, no File 13 code takeover |
| GOV-F13-01 | Original v1.0 ownership change requires dated Founder approval | File 13 plan section 1.3; File 13 traceability | Governance | Approval unverified; no permanent transfer assertion |
| GOV-F13-02 | Historical vs current package directory identity requires release change control | `docs/MIGRATION.md` | Release governance | Approval unverified |
| OPS-F13-01 | Deployed source, DB version, migration state, live tests unknown | No authoritative live environment proof | Operations | Do not claim live fix |

### Review scope and gates

Read-only inventory covered all current File 13 repository paths, WordPress PHP lifecycle, authorization, registry, health, analytics, rewrite, migration, rollback, privacy, security, tests, packaging and workflow, plus the governing File 13 and central plans and current companion contracts. The only new proven File 13-owned issue is F13-20261011-R1-01. The correction is limited to authenticated historical preview accessibility; File 20 remains invocation/frequency owner, File 25 remains active visual owner. Exact-head CI, staged browser verification and deployment parity must be reported separately; this ledger is not a claim of 20 complete rounds.
