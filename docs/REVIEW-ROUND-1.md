# Review and Correction Round 1 — Functional/Architectural

Review scope: current master plan, File 13 plan, superseding 30-day directive, File 00/20/24/25 boundaries, source, tests and package.

Defects found and corrected:

1. Session-only baseline contradicted the current 30-day policy — replaced with timestamp policy and account-first fallback.
2. Orange-primary baseline contradicted current green identity — changed to green tokens with contextual orange cue.
3. Configuration reads rewrote `updated_at` — preserved recorded metadata.
4. Wildcard route help text did not match sanitizer behavior — explicit governed `*` support added.
5. File 00 capability integration was implicit — versioned capability filter and native helper added.
6. Preview existed only as a query string — canonical signed `/welcome-intro-preview/` rewrite added.
7. Account/public event code could treat `shown/error` as a dismissal — dismissal and analytics paths separated and server allowlist hardened.
8. Same-session claim occurred only after dismissal — session claimed before showing to prevent navigation duplication.
9. Fixed CSS exit and fallback could exceed configured/eight-second maximum — exact JavaScript timer made authoritative; hidden default preserves failure safety.
10. Modal isolation covered only immediate siblings — recursive ancestor isolation/restoration implemented.

Round result: all identified source defects corrected; full regression suite rerun required.
