# 13 — Sabri Welcome Intro Animation

Corrective source repository for **File 13** of the **Sabri Social Homeopathy Platform**.

## Current corrective candidate

- Plugin: `Sabri Welcome Intro Animation`
- Corrective version: `0.2.0`
- Historical baseline: `0.1.1`
- Baseline branch: `baseline/file-13-original-import`
- Corrective branch: `audit/file-13-source-review`
- Status: **source defects corrected; staging acceptance still mandatory**

The plugin presents the approved eight-second Sabri Homeopathy welcome sequence once per browser session. It includes the circular SH roundel, the `Sabri Homeopathy` name, the approved Tridimensional Healing claim, the bright-orange line, Skip/Escape controls, a short reduced-motion experience, and fail-open behavior when JavaScript is delayed or unavailable.

## Corrective security and accessibility architecture

Version `0.2.0`:

- hides the overlay by default and exposes it only after the early session bootstrap authorizes display;
- includes a CSS-only bounded exit, so a failed or delayed runtime cannot leave the website blocked;
- claims the session immediately, with session-storage fallback and short-lived cross-tab coordination;
- requires an authenticated administrator, `manage_options`, and a valid WordPress nonce for previews;
- removes unauthorized preview parameters and excludes authorized previews from caches and indexing;
- moves initial focus into the dialog, contains Tab navigation, makes the background inert, restores original attributes, and restores prior focus;
- completes through `animationend` with a bounded timer fallback and cleans up every listener and timer;
- uses a minimum 44 by 44 CSS-pixel Skip target;
- implements the approved circular, path-based SH logo without font-dependent SVG text.

## Deterministic evidence

- Corrected plugin files: `11`
- PHP files: `6`
- JavaScript files: `2`
- CSS files: `1`
- Corrected plugin bytes: `27,183`
- Corrected source-tree SHA-256: `5e48eb5a295c53185ca45d7fabd3f36af7ff347a7ec1f1833828714c45a3bb42`
- Corrected ZIP: `release/13-sabri-welcome-intro-animation-0.2.0.zip`
- Corrected ZIP bytes: `11,932`
- Corrected ZIP SHA-256: `a59aaab80d89a41cfa57c9bdff64a392ae8874e2c3b03161bd62020f845c07b1`

## Validation

The corrective workflow performs:

1. JavaScript syntax checks.
2. Executable bootstrap session tests.
3. Executable dialog lifecycle tests.
4. Deterministic source-contract tests.
5. PHP lint and plugin-load/activation smoke tests.
6. Declared PHP 7.4 compatibility lint/smoke testing.
7. Static security and accessibility invariants.
8. Two-build byte-reproducibility verification.
9. ZIP integrity and release checksum verification.
10. Corrected source checksum verification.

## Release boundary

Green source CI is not production acceptance. Do not merge to `main` or deploy live until `STAGING-ACCEPTANCE.md` is completed on Hostinger staging, including LiteSpeed delayed-script tests, real WordPress preview authorization, keyboard/accessibility checks, responsive browser review, File 20/theme integration, backup restoration, rollback, and Founder visual approval.

The repository visibility remains a separate governance action because repository-setting mutation is not part of the available source-correction interface. See `GOVERNANCE-NOTICE.md`.
