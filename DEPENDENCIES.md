# Dependency Matrix

| Dependency | Requirement | Failure behavior |
|---|---|---|
| WordPress | 6.6+; project baseline 7.0.1 | Plugin cannot activate outside WordPress |
| PHP | 7.4+; project baseline 8.3 | Activation blocked by WordPress metadata |
| File 00 | Optional versioned capability/account adapter | Native `manage_options` and local account preference fallback |
| File 20 | Optional registry/slot/runtime config adapter | Narrow Home-only body/footer fallback; duplicate guard |
| File 24 | Optional safe-mode signal | Local admin/`SWI_DISABLE` kill switches remain |
| File 25 | Optional CSS custom properties | Accessible green/orange local fallback tokens |
| Browser storage | Optional | Hidden fail-open; account/server or available storage used |
| REST/network | Optional for preference sync/analytics | Local dismissal persists; UI never blocks |

No Composer, npm, CDN, remote font, media provider or third-party runtime package is required.
