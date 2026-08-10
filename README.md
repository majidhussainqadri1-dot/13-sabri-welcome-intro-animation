# File 13 — Sabri Welcome Intro Animation

Repository source for the accessible, non-blocking Welcome Intro experience of the Sabri Social Homeopathy Platform. This repository is **not** itself evidence of staging acceptance, live deployment or operational completion.

## Current repository candidate

- Software version: `1.1.0`
- Schema version: `1.1.0`
- Experience contract/version: `1.1.0`
- Canonical installable ZIP: `release/13-sabri-welcome-intro-animation-1.1.0.zip`
- ZIP root: `sabri-welcome-intro-13/`
- WordPress text domain: `sabri-welcome-intro`
- PHP prefix: `SWI_`
- PHP compatibility floor declared by the plugin: `7.4+`
- Target project baseline: WordPress `7.0.1`, PHP `8.3`

The ZIP, SHA-256 and `MANIFEST-1.1.0.json` are generated deterministically by the exact-head CI. Do not reuse a checksum from an older commit as evidence for a newer candidate.

## Governing behavior

- First eligible visit only, with **same-session and multi-tab suppression** plus a **minimum 30-day recurrence**.
- Login, registration, account, support/recovery, appointment-task, clinical, emergency, checkout, cart, admin, REST, feed and embed contexts are suppressed by default.
- Underlying page load is never dependent on the intro. CSS/JS/storage/runtime failure is fail-open.
- The historical forced eight-second auto-close is **not** active. Default automatic close is `0`; a non-zero timing requires a separately Founder-approved visual specification and is bounded at 30 seconds for safety.
- Default governing claim: `The Tridimensional Healing System of Soul, Vital Force, and Matter`.
- Exact local fallback primary: Sabri Green `#087A4E`; orange is contextual accent only. File 25 remains the global visual-token owner.
- No autoplay audio, no remote runtime script/style dependency, no fingerprinting, and analytics is opt-in aggregate-only.

## Future Welcome Experience Superset — 18 approved enhancements

The `1.1.0` candidate implements all 18 File 13 Future enhancements without creating a second shell, identity owner or service-worker owner:

1. Adaptive full/light/static intro selection.
2. `Never Show Again` device/account preference.
3. Signed `Replay Welcome` entry.
4. Version-aware replay after a materially new approved experience.
5. Cross-device authenticated preference synchronization.
6. Privacy-minimal guest → account preference reconciliation.
7. Instant interaction exit on clear user intent.
8. Data-Saver static mode.
9. Performance circuit breaker with bounded startup budget.
10. Accessibility presentation profiles.
11. Locale-aware governed copy resolver.
12. Advanced signed Preview Lab states/variants/profiles.
13. Founder visual-baseline approval workflow.
14. Deterministic source visual-baseline hashing.
15. Privacy-safe aggregate variant/replay telemetry.
16. Approval-aware health/status evidence.
17. Bounded configuration rollback snapshots.
18. PWA/offline precache adapter, while service-worker registration remains outside File 13 ownership.

The executable declaration is exposed by `swi_get_welcome_intro_contract()` and is independently checked by `tests/future-superset-regressions.php`.

## Administration and user controls

`Settings → Sabri Welcome Intro` provides governed enable/disable, 30–365 day recurrence, optional automatic duration, reduced-motion duration, approved copy, route allow/suppression lists, schedule, performance budget, Future feature switches, aggregate analytics, Preview Lab, Founder visual approval, rollback snapshots, health evidence and bounded audit history.

Account preferences additionally support resetting recurrence/`Never Show Again` and selecting a File-13-only accessibility profile. Privacy export and erasure include these Future preferences.

## Cross-file ownership contracts

- **File 00:** identity/capability truth; File 13 consumes native authorization and never creates an alternate identity authority.
- **File 20:** application shell, route/layout and suppression context; File 13 exposes registry/slot contracts but does not own navigation/layout.
- **File 24:** safe-mode/security assurance; File 13 cannot lift a File 24 denial.
- **File 25:** global visual tokens/design system; File 13 consumes approved tokens/fallbacks and owns only its intro presentation.

Companion runtime filters are restriction-only: they may make recurrence, route eligibility, schedule, privacy or safe-mode behavior stricter, but cannot broaden local permissions or replace File 13 governed copy.

## Exact-head verification

```bash
find sabri-welcome-intro tests -type f -name '*.php' -print0 | sort -z | xargs -0 -n1 php -l
php tests/php-contract-tests.php
php tests/forty-round-regressions.php
php tests/governance-monotonic-regressions.php
php tests/future-superset-regressions.php
find sabri-welcome-intro tests -type f -name '*.js' -print0 | sort -z | xargs -0 -n1 node --check
node --test tests/runtime-behavior.test.js
node --test tests/forty-round-runtime.test.js
node --test tests/forty-round-static.test.js
node --test tests/future-superset-runtime.test.js
bash tests/static-contracts.sh
python3 tests/eighty-round-review.py
python3 tools/generate-pot.py --check
python3 tests/reproducible-package.test.py
unzip -t release/13-sabri-welcome-intro-animation-1.1.0.zip
sha256sum --check release/SHA256SUMS
```

## Completion-status law

The repository can independently prove **Coded**, **Packaged** and **Automated-QA Green** only when the exact current candidate passes its fresh workflow. The following remain independent mandatory external gates and are never inferred from GitHub source or a green ZIP build:

- Hostinger-equivalent staging fresh install/upgrade and migration checks;
- real deployed File 00 / File 20 / File 24 / File 25 contract integration;
- active theme shell-slot behavior and LiteSpeed/cache behavior;
- Chrome/Firefox/Safari/Edge plus mobile/desktop acceptance;
- keyboard, screen-reader, zoom, RTL and reduced-motion acceptance;
- backup restore and rollback rehearsal;
- Founder copy/logo/visual/duration acceptance;
- controlled production deployment, live smoke/re-test and monitoring.

See `docs/CONTRACTS.md`, `docs/REQUIREMENTS-TRACEABILITY.md`, `STATUS.md`, and the exact current GitHub Actions run before making any release claim.
