# File 13 — Sabri Welcome Intro Animation

Production-oriented WordPress source for the Sabri Social Homeopathy Platform welcome experience.

## Release candidate

- Plugin version: `1.0.0`
- Schema version: `1.0.0`
- Canonical installable ZIP is deterministically built by CI as `release/13-sabri-welcome-intro-animation-1.0.0.zip` and published as the `file-13-welcome-intro-1.0.0` workflow artifact
- ZIP root: `sabri-welcome-intro-13/`
- WordPress text domain: `sabri-welcome-intro`
- PHP: `7.4+`
- Target project baseline: WordPress `7.0.1`, PHP `8.3`

## Governing behavior

- Appears only on the first eligible visit.
- Never appears again in the same browser session.
- Continue, Skip, Close, Escape, or normal completion suppress the experience for at least 30 days.
- Logged-in account timestamp is preferred; first-party timestamp cookie and local storage provide a guest/failure fallback.
- Login, registration, account, support, appointment-task, clinical, emergency, checkout, cart, admin, REST, feed and embed requests are suppressed by default.
- Overlay markup is hidden by default. Missing JavaScript, CSS, storage or network support cannot block the underlying page.
- Maximum normal duration is eight seconds. Reduced motion is static/short and has no hidden long timer.
- Primary visual identity is green; orange is a contextual motion accent. File 25 tokens override local fallbacks.
- No audio, remote runtime dependency, fingerprinting or personal analytics.

## Administration

`Settings → Sabri Welcome Intro` provides:

- enable/disable kill switch;
- 30–365 day frequency fallback;
- maximum and reduced-motion durations;
- approved brand copy and language;
- eligible routes and suppressed prefixes;
- optional schedule;
- optional aggregate-only analytics;
- signed administrator preview states;
- system check and bounded audit evidence.

## Cross-file contracts

- File 00: `swi_manage_capability` and native capability checks.
- File 20: `sabri_shell_module_registry`, `sabri_shell_welcome_intro`, `swi_runtime_config`, route/suppression filters.
- File 24: `SABRI_PLATFORM_SAFE_MODE`, `swi_force_disabled`, `sabri_platform_safe_mode`.
- File 25: `--sabri-color-*` visual tokens and RTL/accessibility presentation.

See `docs/CONTRACTS.md` and `docs/REQUIREMENTS-TRACEABILITY.md`.

## Local verification

```bash
php tests/php-contract-tests.php
node --test tests/runtime-behavior.test.js
bash tests/static-contracts.sh
python3 tests/reproducible-package.test.py
python3 tools/build-release.py
sha256sum --check CHECKSUMS-1.0.0.sha256
```

## Status law

Source, automated QA and deterministic packaging are independently verifiable in this repository. Hostinger staging, active-theme/File 20/File 25 integration, real browser/assistive-technology acceptance, backup restoration, Founder visual acceptance, production deployment and operational monitoring remain separate evidence gates and must not be inferred from source completion.
