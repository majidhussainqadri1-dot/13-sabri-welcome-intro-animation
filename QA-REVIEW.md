# Preliminary QA Review — File 13

## Automated checks executed before upload

- `unzip -t`: archive structure readable.
- `php -l`: 6/6 PHP files passed.
- `node --check`: 2/2 JavaScript files passed.
- Path inspection: no absolute paths or parent-directory traversal entries were found.

## Confirmed baseline behaviors from source

- Eight-second standard duration (`8000` ms).
- Reduced-motion duration of `1200` ms.
- Public enable/disable setting.
- Preview query parameter: `swi_preview=1`.
- Session-cookie suppression after first display.
- Skip button and Escape-key dismissal.
- Rendering through `wp_body_open` with `wp_footer` fallback.
- WordPress escaping and capability checks in the settings surface.

## Review items requiring correction or explicit acceptance

1. The modal declares `aria-modal="true"`, but the baseline JavaScript does not implement a focus trap, initial focus placement, or focus restoration after dismissal.
2. The Escape listener remains attached after the intro is removed. It is harmless in ordinary use but should be cleaned up during lifecycle hardening.
3. The session cookie is intentionally a browser-session cookie, but its behavior must be tested with LiteSpeed/Hostinger caching and privacy modes.
4. The overlay exit CSS begins at 7.3 seconds while JavaScript removes the element at 8 seconds; visual timing must be accepted manually.
5. The SVG uses embedded text and system fonts; cross-platform rendering and brand fidelity require screenshot acceptance.

Under the project QA rule, any confirmed defect must be corrected and re-tested before this file is promoted beyond baseline status.
