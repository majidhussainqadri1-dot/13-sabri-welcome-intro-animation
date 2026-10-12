# File 13 — New one-off cross-file 20-topic evidence audit and correction ledger, 2026-10-12
## Source-of-authority and frozen evidence
- Supplied File13 final specification `13-File-13-Welcome-Intro-Animation-Complete-Master-Plan-2026-v1.0(1).docx`: sections 1–14, 7-Aug governing annex, F13-FR-001..015, NFR-001..010. This is **specification**, not code/deployment proof.
- Supplied consolidated Three Central Plans v3.0: later All-Chats Welcome Screen Frequency Control: first eligible visit, Continue/Close/Skip then 30 days, account timestamp precedence, guest first-party fallback, same-session suppression, fail-open content, green/accessibility; canonical File20 invocation/frequency and File25 visual/RTL/accessibility. Supersedes historic orange/forced-eight-second details; historic File13 compatibility remains suppressive.
- Exact independently inspected repo snapshots: File13 draft PR#6 `169e7e01d177e4ad42f93bf628c1600df0fa3670` (before this documentation commit); File20 corrective draft PR#41 `75196bdfd46f532fd0d61387356ad8e25e180c14`; File25 corrective draft PR#19 `c2263c964af33f8ffa55a8ca4cb07edec021ec95`.
- Twelve full source/test files retrieved by exact commit, 20 **DIFFERENT** focused exact-source assertions individually evaluated (20/20 source assertions PASS); plus semantic plan-vs-code assessment below. **These are 20 distinct targeted source verifications, NOT 20 exhaustive independently repeated repository audits**, and not proof of installed WordPress, browser/visual acceptance or live production.
## 20 independently targeted checks and frozen scope results
| Round | Source/provenance inspected | Result / distinction |
|---|---|---|
| 01 | File13 `Plugin::register`, F13-FR-009 | PASS: old public renderer not registered; File13 compatibility-only |
| 02 | File13 `Plugin`/Analytics, F13-FR-014 | PASS: no public Analytics producer reactivated |
| 03 | File13 `Renderer::is_preview_request`, F13-FR-011 | PASS source: protected preview requires persisted own rewrite |
| 04 | File13 `Settings`, F13-FR-012 | PASS source: suppressive config+schema migration gates |
| 05 | File13 `Foundation`, File01 registry boundary | PASS adapter present; installed File01 registry state unknown |
| 06 | File13 `Health`, File24 integration | PASS assurance filter present; installed File24 behavior untested |
| 07 | File20 `FourPlanHarmonization`, F13-FR-009 | PASS PR#41: historic `sabri_shell_welcome_intro_invoke` no longer invoked |
| 08 | File20 `FourPlanHarmonization` and `WelcomeFrequency::register` | PASS PR#41: canonical File20 frequency/placement registration |
| 09 | File20 `WelcomeFrequency::due` and PHP regression | PASS 30d inclusive threshold; duplicated dismissal extension fixed |
| 10 | File20 homepage eligibility/route regression | PASS targeted source+negative-path unit test; real Hostinger routes unverified |
| 11 | File20 account `user_meta` and PHP cross-account regression | **CORRECTED**: user account no longer inherits guest/browser-global dismissal |
| 12 | File20 site-scoped `siteScope` JS session/local storage | **CORRECTED**: distinct WordPress sites no longer share browser key |
| 13 | File20 `cookie_path()`, guest JS, subdirectory test | **CORRECTED**: first-party guest cookie scopes to WordPress subdirectory |
| 14 | File20 guest/member dismissal JS and Node regression | **CORRECTED**: logged-in user no longer writes shared guest cookie/localStorage |
| 15 | File20 JS welcome heading focus, dismissal restoration, Escape | **CORRECTED**: programmatic heading focus (tabindex -1), prior-focus restoration |
| 16 | File20 File25 visual owner contract and PHP negative regression | **CORRECTED**: no unstyled welcome if File25 visual filter/tokens missing or degraded |
| 17 | File20 `dismiss` REST and idempotency regression | **CORRECTED**: capability+nonce and repeated POST without expiry extension or extra meta write |
| 18 | File25 `Three_Plan_Corrections::render_welcome_panel` and regression | PASS PR#19: Continue, Close, Skip + nonempty accessible Skip label; **genuine brand logo artwork remains unverified** |
| 19 | File20 to File25 delegated function and `invoked_by_file_20` | PASS exact-source contract handoff; real installed joint plugin integration unverified |
| 20 | File20 Node storage-denial/Escape suite and File13 disabled legacy producer | PASS isolated source tests; JS-disabled/content reading acceptance still staging-only |
## Correction ledger — not live-resolved
1. `X20-20261012-01`: guest cookie used to suppress a new unrelated logged-in account. Root: server fell back to browser cookie for account with absent user-meta. Fixed in owner File20 PR#41 and negative PHP test.
2. `X20-20261012-02`: member dismissal wrote shared guest cookie/localStorage and could change guest experience on same browser. Root: unconditional guest persistence. Fixed by guest-only write and Node negative test.
3. `X20-20261012-03`: browser keys/cookie path were not site-partitioned. Root: shared origin-wide constant and `Path=/`. Fixed with site-specific keys and WordPress home path.
4. `X20-20261012-04`: welcome heading did not receive predictable focus/restore handling. Added keyboard-safe heading programmatic focus + restoration; tested by Node.
5. `X20-20261012-05`: duplicate authenticated REST dismissal could slide 30-day hold and rewrite user-meta indefinitely. Added idempotent reply preserving original deadline + negative PHP test.
6. `X20-20261012-06`: File25 safe mode/absent token contract might emit unstyled File20 welcome host. Added fail-closed File25 visual owner/token contract gate + PHP negative test.
All six code corrections are in **File20 draft PR#41** only; no second File13 runtime introduced.
## Existing owner source candidates / exact CI evidence
- File13 corrective draft [PR #6](https://github.com/majidhussainqadri1-dot/13-sabri-welcome-intro-animation/pull/6), `169e7e01d177e4ad42f93bf628c1600df0fa3670`: [CI #38227198265](https://github.com/majidhussainqadri1-dot/13-sabri-welcome-intro-animation/actions/runs/38227198265) SUCCESS and [#38227193343](https://github.com/majidhussainqadri1-dot/13-sabri-welcome-intro-animation/actions/runs/38227193343) SUCCESS. Recheck new head after this record commit.
- File20 canonical [PR #41](https://github.com/majidhussainqadri1-dot/20-sabri-unified-application-shell/pull/41), `75196bdfd46f532fd0d61387356ad8e25e180c14`: full PHP7.4+8.3 and Node suite [CI #38229551429](https://github.com/majidhussainqadri1-dot/20-sabri-unified-application-shell/actions/runs/38229551429) SUCCESS; baseline archive [#38229552104](https://github.com/majidhussainqadri1-dot/20-sabri-unified-application-shell/actions/runs/38229552104) SUCCESS.
- File25 visual [PR #19](https://github.com/majidhussainqadri1-dot/25-sabri-public-ui-profile-timeline-visual-experience/pull/19), `c2263c964af33f8ffa55a8ca4cb07edec021ec95`: [CI #38226981017](https://github.com/majidhussainqadri1-dot/25-sabri-public-ui-profile-timeline-visual-experience/actions/runs/38226981017) SUCCESS; [#38226975196](https://github.com/majidhussainqadri1-dot/25-sabri-public-ui-profile-timeline-visual-experience/actions/runs/38226975196) SUCCESS.
## Honest OPEN requirements / release gates
- F13-FR-003 actual approved Sabri/SH logo + branded sequence/motion cue is **not shown by the current File25 welcome panel implementation**; the actual authorized logo/creative acceptance was not available in the reviewed source. Historical orange/forced-eight-second variant is superseded, but approved visual identity must still be checked. Do not invent an official logo.
- File20 and File25 corrected draft branches are **unmerged**; source contract PASS != jointly installed compatibility. Verify actual dependency versions/activation order, safe-mode, role-specific routes, real WordPress 7.0.1/PHP8.3 baseline, staging source parity and browser acceptance.
- Missing real RTL/200–400% zoom, reduced-motion, screen reader, heading focus, low-bandwidth, JS-disabled tests on deployed staging. No authenticated cross-device and guest post-30-day installed test. No quantitative p75/p95 or real package/restore drill.
- Dated Founder approval/change-control is required for permanent File13 public ownership transfer/package identity. Formal migration, fallback, rollback, File01 registry sync, File24 operational assurance and performance must be staged and certified.
- Installed active plugin/runtime versions, exact deployed artifact/checksum, DB/schema/wp_options/user_meta, migration markers/logs/rollback and live smoke test have **not** been inspected. Never call these changes LIVE RESOLVED.
## Final deployment-truth record
Repository HEAD (main): File13 `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`; File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`; File25 `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e`.
Deployed Version: UNKNOWN. DB Version: UNKNOWN. Migration State: UNKNOWN. Live Verification Status: UNVERIFIED.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
Former File13 hourly audit automation remains disabled. This is a one-off, user-authorized source review/correction.

## Post-review File25 brand-identity corrective addendum (exact fresh source)
- After the twenty focus-area snapshot, the File25 brand/visual gap was corrected in canonical File25 draft [PR #19](https://github.com/majidhussainqadri1-dot/25-sabri-public-ui-profile-timeline-visual-experience/pull/19), latest corrective SHA `d675a3991492cb5161c68ed39071cd9f9925c960`. Its existing `Three_Plan_Corrections::render_welcome_panel()` now uses the installed WordPress site's **already approved Custom Logo attachment** through `get_theme_mod('custom_logo')`, `wp_get_attachment_image()` and `wp_kses_post()`. When no logo is configured, it uses safely escaped **Sabri Homeopathy** text identity instead of inventing visual artwork. Added explicit positive/fallback regression tests. Design owner remains File25; frequency remains File20.
- Exact post-change PHP 8.0, PHP 8.3, JavaScript and deterministic staging-candidate jobs all SUCCESS on [CI #38230317878](https://github.com/majidhussainqadri1-dot/25-sabri-public-ui-profile-timeline-visual-experience/actions/runs/38230317878) and [#38230310723](https://github.com/majidhussainqadri1-dot/25-sabri-public-ui-profile-timeline-visual-experience/actions/runs/38230310723). These are SOURCE/BUILD results, not visual staging acceptance.
- Earlier open note about 'no logo-rendering path' is now CLOSED at **source-code capability** level; which actual Custom Logo asset is configured/approved on installed staging/live remains **UNKNOWN**. The old 8-second forced entrance and orange cue are superseded by the newer central 30-day/green/non-blocking policy. Current source does not certify an approved bespoke animation or real brand visual signoff.
- Corrected File20 latest source SHA remains `75196bdfd46f532fd0d61387356ad8e25e180c14`, [CI #38229551429](https://github.com/majidhussainqadri1-dot/20-sabri-unified-application-shell/actions/runs/38229551429) SUCCESS. File13 public runtime still inactive. None of these draft PRs has been merged or deployed.
- Deployment evidence fields unchanged: Deployed Version UNKNOWN; DB Version UNKNOWN; Migration State UNKNOWN; Live Verification UNVERIFIED. Full, separately repeated twenty end-to-end reviews cannot be certified without installed WordPress/Browser/DB/staging evidence.
