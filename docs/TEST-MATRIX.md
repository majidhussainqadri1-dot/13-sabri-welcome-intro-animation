# Test Matrix — File 13 / 1.1.0

| Layer | Cases | Environment | Evidence / gate |
|---|---|---|---|
| PHP syntax | plugin + PHP test files | CI PHP runtime | `php -l` all files |
| Core PHP contracts | defaults/sanitization/config lock/snapshots/migration/eligibility/analytics/REST/system contract | CI stubbed WordPress semantics | `tests/php-contract-tests.php` |
| Historical corrected regressions | governance metadata, duration/site-time, monotonic denial, REST abuse/idempotency, rewrite/system-check | CI | `tests/forty-round-regressions.php` |
| Monotonic governance | companion runtime config may only restrict; concurrent/stale write-lock behavior | CI | `tests/governance-monotonic-regressions.php` |
| Future Superset PHP | exact 18 Future source capabilities + governing invariants | CI | `tests/future-superset-regressions.php` |
| Core browser runtime model | first visit/session/30-day/expired/fail-open/focus/reduced motion/preview/REST | Node VM harness | `tests/runtime-behavior.test.js` |
| Historical account runtime | account vs newer/older guest handoff, fail-open activation, authenticated aggregate nonce | Node VM harness | `tests/forty-round-runtime.test.js` |
| Static JavaScript semantics | account handoff, nonce, runtime catch, RTL/copy, closing transition, config governance | Node static | `tests/forty-round-static.test.js` |
| Future browser runtime | no forced close, Never Show Again, replay/version replay, Save-Data/slow/reduced motion, accessibility profile, circuit breaker, sync/analytics/preview | Node VM harness | `tests/future-superset-runtime.test.js` |
| Security/privacy/architecture | version, recurrence, hidden/fail-open, green/orange, no sound/remote runtime, authorization/nonces, Founder assertion path, safe mode, shell/token/privacy/REST/telemetry/uninstall | shell semantic contracts | `tests/static-contracts.sh` |
| Sequential adversarial review | 80 independent deterministic source/governance/evidence checks in fixed order | Python CI | `tests/eighty-round-review.py` |
| Localization | current translatable source exactly matches committed POT | Python CI | `tools/generate-pot.py --check` |
| Package reproducibility | two deterministic builds, canonical ZIP root/version/manifest | Python + unzip/SHA | `tests/reproducible-package.test.py`, `release/SHA256SUMS` |
| WordPress migration | fresh install, 1.0→1.1 upgrade, repeat migration, no foreign mutation | real staging | mandatory before Staging-Accepted |
| File 00 integration | ordinary admin vs canonical Founder; logged-in preferences and capabilities | real staging | mandatory |
| File 20/24/25 integration | one shell slot, hard suppression/safe mode, visual tokens | real staging | mandatory |
| Cache | guest page-cache, session/30-day state, no cross-user personalized cache leak | Hostinger/LiteSpeed staging | mandatory |
| Browser/device | Chrome/Firefox/Safari/Edge; mobile/desktop; weak/save-data/offline profiles | real browsers/devices | mandatory |
| Accessibility | keyboard, screen readers, zoom 200/400%, RTL/LTR, reduced motion, contrast/profiles | real AT/browser | mandatory |
| Performance | startup circuit, asset size, Core Web Vitals p75/p95/no material regression | target staging | mandatory |
| Privacy | export/erasure/reset, analytics-off/on bounded aggregate, no fingerprint fields | real WordPress staging | mandatory |
| Rollback | config snapshot restore + independent full backup/package rollback | staging | mandatory |
| Founder visual acceptance | source+copy+duration baseline hash current and explicit Founder approval | staging | mandatory before approved visual handoff |
| Production | exact approved package deploy, parity, smoke/retest, monitoring | live | only after all prior gates |

A passing automated row does not substitute for a mandatory real-environment row. Historical run counts/checksums are not frozen into this matrix; exact current-head evidence is authoritative.
