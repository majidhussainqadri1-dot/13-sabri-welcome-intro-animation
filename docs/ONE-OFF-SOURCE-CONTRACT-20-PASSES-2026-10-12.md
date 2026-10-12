# File 13 — one-off coding-coverage assessment, 2026-10-12
## Evidence status and exact source snapshots
- Governing materials inspected: supplied `13-File-13-Welcome-Intro-Animation-Complete-Master-Plan-2026-v1.0` including 7-Aug governing annex; supplied consolidated Three Central Plans (the newer Welcome Screen Frequency Control directive); File 25 visual plan; related File 20 and File 25 current-main source contracts.
- Original review candidate: File13 draft PR #6 `5273bf672dbe9b8c527ca06fc5ad40b415ca3bc0`, main `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`.
- File 20 main: `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`. File 25 main: `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e`. File 01 main: `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`. These repository versions are not deployed version claims.
- Fifteen current-PR File13 files were fetched again on post-fix SHA `2c8e8e81627a064cd0627c907f446cfdb1758f4b`; a twenty-assertion source/static battery was independently executed twenty times on the same frozen source, **20/20 assertions in each run (400/400 assertion evaluations)**.
- **Interpretation limitation:** These are 20 machine static-check passes, **not 20 separate exhaustive human code reviews**. They do NOT prove that all plan features exist, that the required cross-file owner integration is complete, or that staging/live is operational. No full 20/20 green engineering-acceptance claim is made.
## Tested source assertions (20 per repeat)
- 1. Public File 13 invocation hook remains unregistered — post-fix static assertion PASS
- 2. Historical enabled/analytics options forced off — post-fix static assertion PASS
- 3. No File 13 browser recurrence storage — post-fix static assertion PASS
- 4. Client JavaScript only runs in authenticated legacy preview — post-fix static assertion PASS
- 5. Canonical preview rewrite collision is detected — post-fix static assertion PASS
- 6. Persisted rewrite precedence is calculated — post-fix static assertion PASS
- 7. Protected preview entry requires persisted first-match route (corrected) — post-fix static assertion PASS
- 8. Negative regressions cover missing/shadowed/foreign routing (added) — post-fix static assertion PASS
- 9. Preview requires institutional/admin manage authorization — post-fix static assertion PASS
- 10. Protected preview emits no-cache and noindex headers — post-fix static assertion PASS
- 11. Admin mutations require POST plus nonce/capability — post-fix static assertion PASS
- 12. REST status is permission gated — post-fix static assertion PASS
- 13. Config write uses DB compare-and-swap — post-fix static assertion PASS
- 14. Migration requires post-write readback before schema promotion — post-fix static assertion PASS
- 15. File 01 manifest/route adapter is explicit — post-fix static assertion PASS
- 16. File 24 assurance filter exists — post-fix static assertion PASS
- 17. Forced/system reduced-motion preview overrides CSS animation — post-fix static assertion PASS
- 18. Public analytics handler is not registered — post-fix static assertion PASS
- 19. Uninstall is non-destructive absent explicit purge constant — post-fix static assertion PASS
- 20. Deterministic SHA-256 package is tested by CI — post-fix static assertion PASS
## Defect ledger and concrete correction
- **F13-20261012-ROUTE-01 (found pre-fix):** `Renderer::is_preview_request()` checked request-local rewrite mapping and query state but did not require persisted, first-match-reachable canonical WordPress rewrite state. `Renderer::preview_route_status()` had the necessary read-only proof for health/assurance yet was not called at protected preview entry. A stale or foreign rewrite situation could be incorrectly accepted in adversarial/mocked request state. **Repository-owned corrective commits on the existing draft PR #6:** `ffa2aab46474d45465a6de445bf8e7c361424cfc` adds fail-closed preview entry gate; `7e3c90c571ecffff1eb986bd44bfbc6ecfee2c91` added negative test but introduced a PHP syntax regression during write; `2c8e8e81627a064cd0627c907f446cfdb1758f4b` rebuilt the test from verified baseline and corrected that error. The source regression tests verify unpersisted, shadowed, foreign persisted and restored canonical routes.
- Test parse failure on intermediate head was **not accepted as passing**; only exact post-fix head CI can validate corrective success.
- **F13-XFILE-20-01 (still open, companion-owned):** File20 `class-four-plan-harmonization.php` around lines 199–232 still says File13 owns session/frequency and dispatches the old File13 hook, while File13 intentionally no longer subscribes to it. Its `tests/run-four-plan-harmonization.php` also asserts the old owner. Newer central governance requires File20 to own invocation/30-day recurrence and File25 presentation; see [File20 issue #40](https://github.com/majidhussainqadri1-dot/20-sabri-unified-application-shell/issues/40). Do **not** reintroduce a parallel File13 public runtime to conceal this defect.
- **F13-GOV-01:** Original File13 v1.0 plan requires dated Founder change-control for permanent ownership and package identity transfer; approval/migration/rollback proof not verified. Remains release gate.
- **F13-OPS-01:** deployed artifact checksum, running WordPress plugin, DB/schema, migration and live browser evidence not obtained. Repository patch cannot be called live-resolved.
## Pending completion gates
File20 30-day recurrence with account-first preference and safe guest fallback; File25 active accessible welcome presentation and integration; approved native ownership change-control and canonical package identity; exact-source package integrity; staging backup/restore, migration, RTL/screen-reader/keyboard/reduced-motion real browser checks, Founder signoff; deployed/source parity and live rerun. All must be separately evidenced.
## Final evidence fields
- Repository main HEAD: `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`; corrective PR #6 HEAD (before this documentation commit): `2c8e8e81627a064cd0627c907f446cfdb1758f4b`.
- Deployed Version: **UNKNOWN**.
- DB Version: **UNKNOWN**.
- Migration State: **UNKNOWN**.
- Live Verification Status: **UNVERIFIED**.
- Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
- This is a **one-off** check/correction task; the previously stopped hourly automation was not reenabled.
