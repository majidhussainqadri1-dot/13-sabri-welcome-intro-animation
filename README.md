# File 13 — Sabri Welcome Intro Animation

Accessible WordPress welcome-intro implementation for the Sabri Social Homeopathy Platform.

## Governing behavior

- Appears only on the first eligible visit.
- After Continue, Skip or Close, it is suppressed for at least 30 days.
- Logged-in account state is authoritative; guests use first-party cookie/localStorage fallback.
- The same session and internal navigation do not replay the intro.
- JavaScript, CSS, storage or runtime failure cannot block the underlying page.
- Authentication, registration, account, support, appointment-task, clinical, emergency, checkout/cart, admin, REST, feed and embed contexts are suppressed.
- Normal duration is capped at eight seconds; reduced-motion users receive a static/short path.
- Continue, Skip, Close and Escape are supported with keyboard focus containment and restoration.
- Primary identity is green; orange is only a contextual motion accent.

## Forty-round review result

- Reviews completed: **40/40**
- Reviews finding defects, followed by correction and retest: **18**
- Reviews finding no new defect: **22**
- Known unresolved repository source/package blockers after Round 40: **0**

Detailed evidence is in `docs/FORTY-ROUND-REVIEW-2026-08-07.md` and `FORTY-ROUND-QA-EVIDENCE.md`.

## Automated verification

- PHP contract assertions: 61/61
- Forty-round PHP regression assertions: 9/9
- Declared PHP 7.4 compatibility: the same 70/70 assertions pass
- Node runtime/static tests: 28/28
- Static security/privacy/architecture contracts: 16/16
- Reproducible package, archive safety and source/evidence ledger: pass

## Release boundary

This repository establishes source implementation, automated QA and deterministic packaging. Hostinger-equivalent staging, real File 00/20/24/25 deployment integration, browser and assistive-technology acceptance, LiteSpeed behavior, restore/rollback rehearsal, Founder visual acceptance, production deployment and operational monitoring remain separate mandatory gates. The pull request intentionally remains draft until those gates are evidenced.
