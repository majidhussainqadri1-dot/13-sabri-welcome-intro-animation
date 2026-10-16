# Fourth one-off File 13 forty-scope exact-source audit — 2026-10-16

## Evidence freeze before changes

Uploaded governing documents: `13-File-13-Welcome-Intro-Animation-Complete-Master-Plan-2026-v1.0(1).docx`, F13-FR-001..015, F13-NFR-001..010 and F13-CEN-01/02; `Sabri-Social-Homeopathy-Platform-Three-Central-Plans-Consolidated-Governing-Master-Plan-2026…docx`, Welcome Screen Frequency Control / SUP-003; `25-Complete-Public-UI-Profile-Timeline-Visual-Experience-Final (1).docx`, accessibility/responsive requirements.

Exact **pre-correction** heads: File13 draft PR#6 `8fb56dcc51585a042d81b8fec1c8dbae9f35e787`; File20 draft PR#41 `340d9864b9d77add92777b04826b4eeb6f408fcf`; File25 draft PR#19 `1cbd84ce7b300bf6b4f5601d6a81a2082452cd85`. File13 main `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`; File20 main `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`; File25 main `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e`; File01 main `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`; File24 main `a5b8d49968a7a5a7d6f3f4655bea541bf38a9acb`.

**Interpretation:** Forty separate source-to-requirement review scopes; these are NOT 40 comprehensive, independent installed WordPress/browser production audits. Source evidence is never installed or live evidence.

## Exactly 40 distinct targeted checks

| # | Inspected requirement / relationship | Pre-fix | Post-fix |
|---:|---|---|---|
| 01 | Exact File13 version 1.0.1 and historical compatibility | SOURCE-PASS | SOURCE-PASS |
| 02 | No legacy File13 public welcome hook registration | SOURCE-PASS | SOURCE-PASS |
| 03 | No public File13 analytics endpoint registration | SOURCE-PASS | SOURCE-PASS |
| 04 | Legacy active/analytics options forced disabled | SOURCE-PASS | SOURCE-PASS |
| 05 | Read back persisted config before schema advance | SOURCE-PASS | SOURCE-PASS |
| 06 | Malformed scalar option writes pass suppressive sanitizer | SOURCE-PASS | SOURCE-PASS |
| 07 | Exact-byte CAS for authorized settings writes | SOURCE-PASS | SOURCE-PASS |
| 08 | Audit-gap on concurrent update failure | SOURCE-PASS | SOURCE-PASS |
| 09 | Native and institutional administration capability | SOURCE-PASS | SOURCE-PASS |
| 10 | Only authorized read-only status REST | SOURCE-PASS | SOURCE-PASS |
| 11 | Private canonical preview rewrite route | SOURCE-PASS | SOURCE-PASS |
| 12 | Persisted first-match rewrite precedence proof | SOURCE-PASS | SOURCE-PASS |
| 13 | WordPress matched rule required for preview | SOURCE-PASS | SOURCE-PASS |
| 14 | Five allowable semantic preview states | SOURCE-PASS | SOURCE-PASS |
| 15 | Admin authorization/noindex/no-cache | SOURCE-PASS | SOURCE-PASS |
| 16 | Preview without browser preference writes | SOURCE-PASS | SOURCE-PASS |
| 17 | Allowlisted auto/mobile/tablet/desktop states | SOURCE-PASS | SOURCE-PASS |
| 18 | Malformed viewport selectors fail closed | SOURCE-PASS | SOURCE-PASS |
| 19 | Non-recursive sand-boxed responsive iframe | SOURCE-PASS | SOURCE-PASS |
| 20 | Translated viewport labels via WP textdomain | DEFECT | SOURCE-PASS |
| 21 | Viewport links with 44px minimum targets | DEFECT | SOURCE-PASS |
| 22 | Keyboard focus-visible state | SOURCE-PASS | SOURCE-PASS |
| 23 | File01 explicit registry sync and route collision | SOURCE-PASS | SOURCE-PASS |
| 24 | File24 schema/legacy health compatibility interface | SOURCE-PASS | SOURCE-PASS |
| 25 | Trace current main vs unmerged draft separately | DEFECT | SOURCE-PASS |
| 26 | File20 canonical 30-day frequency | SOURCE-PASS | SOURCE-PASS |
| 27 | Homepage/query/task/deep-link exclusion | SOURCE-PASS | SOURCE-PASS |
| 28 | First-party site-scoped guest cookie and localStorage | SOURCE-PASS | SOURCE-PASS |
| 29 | Account-specific user meta suppression | SOURCE-PASS | SOURCE-PASS |
| 30 | WordPress REST nonce and capability | SOURCE-PASS | SOURCE-PASS |
| 31 | Bounded network failure retry | SOURCE-PASS | SOURCE-PASS |
| 32 | Skip/Escape with non-modal focus | SOURCE-PASS | SOURCE-PASS |
| 33 | File25 exported Welcome renderer | SOURCE-PASS | SOURCE-PASS |
| 34 | File25 invoked only by File20 | SOURCE-PASS | SOURCE-PASS |
| 35 | Approved healing identity supplied to renderer | SOURCE-PASS | SOURCE-PASS |
| 36 | Strict File25 semver + required hex-token validation | SOURCE-PASS | SOURCE-PASS |
| 37 | Polite screenreader announcement once | SOURCE-PASS | SOURCE-PASS |
| 38 | Green short animation with reduced-motion | SOURCE-PASS | SOURCE-PASS |
| 39 | File25 safe mode boundary | SOURCE-PASS | SOURCE-PASS |
| 40 | Regression tests include malformed File25 visual contract | SOURCE-PASS | SOURCE-PASS |

**Initial source-scoped result: 37/40 pass, 3 discovered defects. After correction: 40/40 selected source checks pass, none unresolved within the fixed scope.**

### Root causes and corrections

1. **R20 — F13-NFR-010 localization:** Newly implemented private admin viewport controls rendered `ucfirst($choice)` instead of WordPress textdomain strings. Fix introduces `Renderer::preview_viewport_labels()`, four `__(... SWI_TEXT_DOMAIN)` labels and rendered escaped translation. This makes the labels translation-ready; it does **not** pretend actual Urdu/Arabic language packs have been installed or visually accepted.
2. **R21 — F13-NFR-005 accessibility:** Viewport chooser links were plain anchors without explicit touch-target size or focus-visible styling. Fix uses scoped 44px minimum inline/block targets, visible focus outlines, and selected `aria-current`; no duplicated global design system.
3. **R25 — File20 main-versus-draft documentation drift:** The File13 traceability document discussed only File20's historic main owner without distinguishing the unmerged canonical File20 PR#41. Fixed both outdated sections and added accurate F13-FR-011 viewport mapping. No claim of a merged or deployed File20.

Source commits: `a9aa7ff58ecb5034ed4e4b7be708564fbdbd16ed`, `72fc4b361cbb748ea74fbb0fd7cd81647e167158`. Behavioral PHP regression extension: `4f75d56bdf0aaf1668b1081c8fe556aea5485eab`. Trace update: `c5e821e06d09fcd0cb71931437ab8d4d4a910975`.

### Independent post-fix verification

Exactly 25 source checks re-evaluated across 11 File13 files at draft head `c5e821e06d09fcd0cb71931437ab8d4d4a910975`: 25/25 PASS, zero unavailable sources. Exactly 15 current File20/File25 source checks at `340d9864b9d77add92777b04826b4eeb6f408fcf` and `1cbd84ce7b300bf6b4f5601d6a81a2082452cd85`: 15/15 PASS. **Total 40/40 source checks.**

Fresh File13 GitHub Actions from corrected source head: PR run `38922232686` SUCCESS and push run `38922220823` SUCCESS. Both PHP 8.1 and 8.3 jobs succeeded at syntax, governed contract suite, viewport translation+target regression, JS behavior, reproducible ZIP package and checksum/symlink portability.

### Blocked external gates / deployment truth

File13 PR#6, File20 PR#41, and File25 PR#19 remain independent draft/unmerged changes. Exact deployed plugin package + checksums, DB schema/options/migration, File01 route inventory and status, File24 installed assurance, approved Founder owner/package change-control, Staging/actual WordPress browser RTL/Urdu/Arabic translation, 200%-400% zoom/reflow, screen-reader, mobile touch targets, actual 30-day preference, backup/restore, approved deployment and live retest are **unverified**.

| Truth dimension | Status |
|---|---|
| Repository HEAD | File13 main `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`; File13 draft head separately listed |
| Deployed Version | UNKNOWN |
| DB Version | UNKNOWN |
| Migration State | UNVERIFIED |
| Live Verification Status | UNVERIFIED |

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
