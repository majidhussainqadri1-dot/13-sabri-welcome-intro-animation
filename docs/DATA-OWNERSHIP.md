# Data Ownership and Retention

| Record | Owner | Classification | Retention |
|---|---|---|---|
| `swi_config` | File 13 fallback; File 20 may override at runtime | Internal configuration | Current version; change evidence in bounded audit |
| `swi_audit_log` | File 13 | Internal audit, minimized | Last 100 entries |
| `swi_aggregate_metrics` | File 13, optional | Anonymous aggregate | Last 90 UTC days; 5,000 events/day cap |
| `swi_last_dismissed_at` | File 13 fallback / File 00 external preferred | Account preference | Until reset/erasure/account lifecycle |
| `swi_last_config_version` | File 13 fallback | Account preference metadata | Same as preference |
| session storage key | Browser | Device/session | Browser session |
| first-party timestamp cookie/local storage | Browser | Device preference | 30–365 day policy window; timestamp only |

No IP address, user agent, referrer, device fingerprint, message, clinical data, payment data or profile content is stored.

WordPress privacy exporters/erasers cover account preference metadata. Browser storage is user-agent controlled and contains only a timestamp/session marker.
