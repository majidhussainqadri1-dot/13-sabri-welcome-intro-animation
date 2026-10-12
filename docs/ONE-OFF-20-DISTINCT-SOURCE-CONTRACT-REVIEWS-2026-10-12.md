# One-off File 13 — 20 DISTINCT governing-plan, source and companion-contract review scopes (12 October 2026)

## Freeze BEFORE corrective implementation
- File 13 original plan: supplied `13-File-13-Welcome-Intro-Animation-Complete-Master-Plan-2026-v1.0(1).docx`, chapters 1–14 and 7-Aug governance annex; baseline has F13-FR-001– etc. File 13 v1.0 original owner is session intro; latest master directive supersedes orange/forced-8s and assigns active invocation/frequency File20, public visual File25. Permanent transfer and package rename require dated change-control acceptance.
- Central consolidated governing plan: supplied Three Central Plans 2026, `Welcome Screen Frequency Control`, paragraphs: first eligible visit, 30-day hold after Continue/Close/Skip, account preference for login, guest first-party cookie/localStorage, same-session refresh/navigation suppression, fail-open to content, green, File20 frequency, File25 visuals; `SUP-003` marks old forced-8s historical.
- File13 main `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`; review candidate PR#6 `d8eacc91433367eaee7c0c52f254167250098fa2`. File20 main `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`, File25 main `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e`; File01 main `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`. Live plugin/DB unknown.
- **Method:** 20 different independently inspected source/requirement areas, not twenty identical static tests or 20 full end-to-end execution cycles. Findings below are explicit PASS/GAP/EXTERNAL_GATE, not fabricated GREEN round labels. All are read-only findings frozen before new correction work.

| Review | Distinct substantive area | Evidence reviewed | Frozen result |
|---|---|---|---|
| R01 | Plan precedence/identity | File13 §1.3 & 7-Aug annex; central §Welcome and SUP-001/SUP-003 | PASS: green/new 30-day overrides 8-sec/orange |
| R02 | Canonical owner | File13 `docs/TRACEABILITY.md`; File20 harmonization:199-232; File25 `Three_Plan_Corrections` | **GAP X20-01:** File20 still states File13 frequency owner |
| R03 | Active invocation | File20 `prepare_welcome_invocation/invoke_welcome_intro`; File13 `Plugin::register` and `Renderer::invoke` | **GAP X20-02:** dispatch targets unregistered legacy hook; active welcome not served |
| R04 | Eligibility and suppression | File20 `welcome_eligible()` 235-250; File13 `Eligibility::hard_suppressed_path` (inactive) | **GAP X20-03:** account/login/appointment/clinical/emergency explicit path suppression absent from owner |
| R05 | Account recurrence | Central 30-day rule; File20 `FourPlanHarmonization`; File13 forced-disabled `Settings` | **GAP X20-04:** no File20 persisted account dismissal timestamp or due-check |
| R06 | Guest recurrence | Central cookie/localStorage; File20 assets `four-plan-harmonization.js` | **GAP X20-05:** no guest 30-day fallback or user-triggered preference receipt |
| R07 | Session/refresh guard | Central same session/refresh/nav; File20 JS and PHP | **GAP X20-06:** no canonical session dedup or precedence |
| R08 | Dismissal action contract | Central Continue/Close/Skip; File20 source/tests | **GAP X20-07:** no owner-scoped dismissal endpoint/nonce/idempotency |
| R09 | Presentation primitive | File25 `Three_Plan_Corrections::render_welcome_panel` 162-193 | CONDITIONAL: renderer exists and checks `invoked_by_file_20`, but currently uninvoked |
| R10 | Skip and Escape | File25 renderer 187-192; File13 preview JS 33-37 | **GAP X25-01:** active File25 panel has Continue and Close but no Skip button or Escape integration |
| R11 | Motion/keyboard/no-flash | File13 `Renderer::style_block`, `welcome-intro.js`; File25 welcome CSS | PASS for authenticated historical preview, **GAP** active welcome real-browser acceptance |
| R12 | Protected preview route | File13 `Renderer::preview_route_status/is_preview_request`, `tests/rewrite-lifecycle.php` | PASS at PR#6: persisted first-match route guard, foreign rule and query spoof regressions |
| R13 | Native capability and REST | File13 `Authorization`, `Rest`, `Settings::update` | PASS at source-level: manage_options/institutional tightening, read-only REST, optimistic CAS |
| R14 | Schema and migration fail-close | File13 `Settings::persist_suppressed_config`, `tests/settings-migration.php`, `tests/schema-write-failure.php` | PASS at source/test-level; live installed schema unknown |
| R15 | File01 registry | File13 `Foundation::manifest/status/sync`, `tests/foundation-registry-contract.php` | PASS standalone registry compatibility; installed registry sync unknown |
| R16 | File24 assurance | File13 `Health::file24_contract_state`, `tests/health-contract.php` | PASS fail-close adapter at source-level; File24 deployed integration unknown |
| R17 | File25 visual token/RTL | File13 `Renderer::visual_tokens`; File25 `Design_System::filter_shell_visual_contract` | PASS token DTO, **GAP** active welcome responsive/RTL/focus/zoom screenshot acceptance |
| R18 | Analytics/retention/privacy | File13 `Analytics::register_retention`, disabled public events; `tests/analytics-privacy.php` | PASS privacy kill switch at source-level; live retention audit unknown |
| R19 | Packaging/upgrade/rollback | File13 `build-package.sh`, `tests/package-integrity.sh`, `MIGRATION/ROLLBACK` | PASS deterministic ZIP CI; **EXTERNAL_GATE** Founder package-identity change-control and rollback drill |
| R20 | Exact SHA CI, deployed parity, Founder acceptance | File13 PR#6 CI at review snapshot successful; central and File13 DoD 1-13 | **EXTERNAL_GATE:** no staging/live/DB verification, no full owner integration, no permanent transfer signoff |

## Corrective ownership and release discipline
- Fix X20-01–07 in **File20**, X25-01 in **File25**, keep File13 historical runtime suppressed. Never use File13 as a second source of truth. Only mark affected gaps corrected after exact changed-head regression CI; never conflate branch candidate with live.
- Fresh File13-source code fixes were already separately incorporated on PR#6 at `2c8e8e81627a064cd0627c907f446cfdb1758f4b` and documented at `d8eacc91433367eaee7c0c52f254167250098fa2`; do not patch-stack same issue.
- This audit ledger records **20 distinct, focused source/contract review scopes**, not 20 independently complete full-scope repeat audits. Later changes to File20/25 require fresh owner-specific review and CI evidence.
- Repository HEAD / Deployed Version / DB Version / Migration State / Live Verification Status must always remain separately recorded.
