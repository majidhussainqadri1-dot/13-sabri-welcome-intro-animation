# Dependency Matrix — File 13 / 1.1.0

File 13 is deliberately fail-open when optional presentation adapters disappear, but that does **not** make the platform-owner integrations optional for staging/production acceptance. Runtime survivability and release acceptance are separate questions.

| Dependency | Runtime requirement | Release / governance requirement | Failure behavior |
|---|---|---|---|
| WordPress | 6.6+; project baseline 7.0.1 | Required | Plugin cannot operate outside WordPress |
| PHP | 7.4+; project baseline 8.3 | Required; target version must be staged | Activation blocked by WordPress metadata / syntax floor |
| File 00 | Public guest intro can fail safely without it | **Required for real platform identity/capability integration, account-authoritative preference behavior and canonical Founder identity assertion** | Public reading remains; privileged Founder approval fails closed; no alternate Founder inference from `manage_options`, name/email/role/user ID |
| File 20 | Local body/footer fallback can keep the page usable | **Required for accepted shell slot, route/layout/suppression integration** | Narrow Home-only fallback; duplicate render guard; companion denial may only restrict |
| File 24 | Local `SWI_DISABLE`/admin kill still exists | **Required for accepted security/privacy/safe-mode assurance integration** | Local safeguards remain; File 24 denial may only restrict |
| File 25 | Local accessible fallback tokens exist | **Required for accepted global visual-token integration and Founder visual acceptance** | Exact Sabri Green `#087A4E` + contextual orange fallback only; no duplicate design system |
| Browser storage | Optional | Guest recurrence/storage behavior must be tested in supported browsers | Storage failure is fail-open; page is never blocked |
| REST/network | Optional for first paint | Required for accepted authenticated preference sync; optional analytics when enabled | Local/device dismissal remains; UI never waits for network |
| Browser Network Information / Performance APIs | Optional | Adaptive behavior must degrade safely where unsupported | Full or static safe variant selected without blocking |
| PWA/service worker owner | File 13 does not require or register one | If platform PWA exists, owner may consume File 13's local precache asset filter | No service-worker ownership or registration is invented by File 13 |

No Composer, npm, CDN, remote font, media provider or third-party runtime package is required or bundled by File 13.

**Acceptance law:** successful fallback behavior proves resilience only. Hostinger-equivalent staging must separately prove the real deployed File 00 / 20 / 24 / 25 contracts before File 13 may be called Staging-Accepted.
