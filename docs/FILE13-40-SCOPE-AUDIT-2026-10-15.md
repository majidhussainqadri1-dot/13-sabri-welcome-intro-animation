# File 13 — 40 distinct plan/source review scopes (15 October 2026)

## Evidence and scope

This is a **focused source-to-requirement review**, not forty complete independent site/browser E2E passes. Reviewed the uploaded amended File 13 governing plan (F13-FR-001…015; F13-NFR-001…010), the consolidated three central plans (including the 6 August 2026 Welcome Screen Frequency Control amendment), and the File 20/25 current draft integration boundaries. File 13 is historical compatibility only; File 20 owns actual invocation/30-day recurrence and File 25 owns accessible responsive presentation.

**Read-only pre-fix exact refs:**
- File 13 PR #6: `49976537832c5744cd6944e2a110453bb3cd9e96`.
- File 20 PR #41: `e4b398e103e3b59df8690f8ce9e187286a3ba1dc`.
- File 25 PR #19: `1cbd84ce7b300bf6b4f5601d6a81a2082452cd85`.
- File 13 main: `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316`.

The review read 16 concrete exact-ref code files. A first broad source check of REST registration was classified as GAP, then **corrected to PASS after reading `class-rest.php`**: it exposes an authenticated GET-only status route, not a public configuration-writing route. No cosmetic false positive is counted as a defect.

## Forty distinct checks

| # | Requirement/code review scope | Source basis | Pre-fix | Corrective disposition |
|---:|---|---|---|---|
| 01 | Plugin runtime 1.0.1 manifest | File13 plugin entry point | PASS | No new source gap in this targeted check |
| 02 | No historical public invocation hook | File13 Renderer/Plugin | PASS | No new source gap in this targeted check |
| 03 | No historical public analytics registration | File13 Plugin | PASS | No new source gap in this targeted check |
| 04 | Legacy enabled flag forcibly disabled | File13 Settings | PASS | No new source gap in this targeted check |
| 05 | Legacy active status forcibly disabled | File13 Settings | PASS | No new source gap in this targeted check |
| 06 | Legacy analytics forcibly disabled | File13 Settings | PASS | No new source gap in this targeted check |
| 07 | Invalid scalar option update suppression | File13 Settings::guard_option_write | GAP | FIX: suppress even on non-array option update |
| 08 | Untrusted legacy revision cannot roll backward/forward | File13 Settings::guard_option_write | GAP | FIX: retain persisted revision on legacy direct option writes |
| 09 | Optimistic concurrency against exact option row | File13 Settings::update | PASS | No new source gap in this targeted check |
| 10 | Native plus institutional privileged update boundary | File13 Settings/Authorization | PASS | No new source gap in this targeted check |
| 11 | Stored configuration read-back before schema advance | File13 Settings migration | PASS | No new source gap in this targeted check |
| 12 | Schema version read-back on migration | File13 Settings schema | PASS | No new source gap in this targeted check |
| 13 | Sticky audit-gap evidence on contention | File13 Settings audit | PASS | No new source gap in this targeted check |
| 14 | Analytics write-boundary disabled kill switch | File13 Analytics::record | PASS | No new source gap in this targeted check |
| 15 | Analytics event-type allowlist | File13 Analytics | PASS | No new source gap in this targeted check |
| 16 | Aggregate-only telemetry, no visitor identity | File13 Analytics | PASS | No new source gap in this targeted check |
| 17 | Retention scheduler registration | File13 Plugin | PASS | No new source gap in this targeted check |
| 18 | Historical analytics keyset expiry walk | File13 Analytics::cleanup | PASS | No new source gap in this targeted check |
| 19 | On-deactivation scheduled hook cleanup | File13 Plugin | PASS | No new source gap in this targeted check |
| 20 | Guarded non-destructive uninstall | File13 uninstall | PASS | No new source gap in this targeted check |
| 21 | Exact canonical WordPress preview rewrite | File13 Renderer | PASS | No new source gap in this targeted check |
| 22 | WordPress matched-rule proof for preview | File13 Renderer | PASS | No new source gap in this targeted check |
| 23 | Preview authentication and capability check | File13 Renderer | PASS | No new source gap in this targeted check |
| 24 | Preview noindex and no-cache | File13 Renderer | PASS | No new source gap in this targeted check |
| 25 | Preview-only JS, no browser persistent keys | File13 preview JS | PASS | No new source gap in this targeted check |
| 26 | Preview Escape/reduced motion support | File13 preview JS | PASS | No new source gap in this targeted check |
| 27 | Only authorized GET/status REST, no public config writes | File13 Rest::routes | PASS | No new source gap in this targeted check |
| 28 | Operator-triggered File01 registry sync | File13 Foundation | PASS | No new source gap in this targeted check |
| 29 | File01 route conflict fail-closed preflight | File13 Foundation | PASS | No new source gap in this targeted check |
| 30 | File24 legacy-renderer assurance gate | File13 Health | PASS | No new source gap in this targeted check |
| 31 | File24 migration/readiness assurance gate | File13 Health | PASS | No new source gap in this targeted check |
| 32 | File20 owns public invocation/frequency | File20 WelcomeFrequency | PASS | No new source gap in this targeted check |
| 33 | Homepage-only safe eligibility; task-query suppression | File20 WelcomeFrequency | PASS | No new source gap in this targeted check |
| 34 | 30-day dismissal bound | File20 WelcomeFrequency | PASS | No new source gap in this targeted check |
| 35 | Logged-in account user-meta preference | File20 WelcomeFrequency | PASS | No new source gap in this targeted check |
| 36 | Guest site-local cookie/local-storage preference | File20 Welcome JS | PASS | No new source gap in this targeted check |
| 37 | Expired own-key cleanup on next eligible visit | File20 Welcome JS | PASS | No new source gap in this targeted check |
| 38 | Bounded HTTP >=500/network retry; no 4xx retry | File20 Welcome JS | PASS | No new source gap in this targeted check |
| 39 | WordPress REST auth/nonce for account preference | File20 WelcomeFrequency REST | PASS | No new source gap in this targeted check |
| 40 | File25 green primary visual contract | File25 Design_System | PASS | No new source gap in this targeted check |

**Pre-fix verified source-scoped result: 38 PASS / 2 code gaps.** Neither the result nor historic successful CI attests to zero unexamined defects, installation success, real 30-day recurrence, installed File01/File24/25 contract parity, or production.

## Defect closure, limited to the canonical owner File 13

**F13-OPT-20261015-01 — scalar input bypass**. Root cause: `Settings::guard_option_write` returned the old array unchanged for an invalid `update_option` payload. Historical `enabled=true`, `status=active`, `analytics_enabled=true` therefore survived persistence on that path even though File13's effective runtime is separately disabled. Correction: always run the suppressive sanitizer, including for scalar/malformed input; preserve safe historical copy.

**F13-OPT-20261015-02 — forged revision**. Root cause: the same `pre_update_option` filter trusted caller-supplied `config_version` and could write a lower/higher revision without the authorized CAS and audit path. Correction: derive version solely from the persisted base through `Settings::sanitize`; leave the preexisting authorized `Settings::update()` CAS workflow unchanged.

**Source correction commit:** `33d95b74239d6edcbeacf2a0f741c8c221b370b9`.

**New executed-on-CI PHP regression:** `tests/legacy-option-guard.php` covers five malformed scalar payload classes, six forged revisions, preserved copy and disabled flags, and corrupt stored revision. Test commit `aef5803e2c52082b0d390c28a62896a911337c26`. Workflow integration commit `0a9fbd204ab4c7a1173d9215da28ce41eae3a2b8`; PHP 8.1 and 8.3 CI matrix. Historical File13 public runtime **must remain disabled**. No File20/25 code changes are asserted by this fix.

## Separate operational gates — still not proven

The currently reviewed File13, File20 and File25 improvements are in **draft, unmerged PRs**. Merge/version compatibility requires dated Founder-approved scope/owner change-control. Before any live release: verify exact installed plugin and database versions, relevant WP options and migrations, deployed file checksums, File01 registry, File24 assurance, File20/25 installed rendering/30-day behavior, browser screen-reader/RTL/zoom/safe mode, backups/restore/rollback, then smoke test and monitored live parity. No source edit is a deployed fix.

- Repository HEAD: File13 main `67418c0f9d0fb4cf77fc2c5d5d79e4caf2e3c316` (PR branch separate).
- Deployed Version: unknown.
- DB Version: unknown.
- Migration State: unknown.
- Live Verification Status: unverified.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
