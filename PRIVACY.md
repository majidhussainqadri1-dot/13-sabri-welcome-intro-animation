# Privacy

The public intro is deliberately privacy-minimal.

Browser preference state:
- `sessionStorage`: a config-version seen flag for the current browser session;
- `localStorage`: a single dismissal-until timestamp;
- no random identifier, account identifier, health interest, browsing profile, device fingerprint, advertising identifier, or cross-site identifier is created.

Dismissal/completion suppresses reappearance for at least 30 days.

Optional aggregate analytics is disabled by default. When enabled it accepts only three event names (shown, skipped, completed) and a configuration version. It stores daily aggregate counts only and automatically removes counters older than 90 days.

The plugin does not export File 00/File 24 rights by pretending to own them. Platform-wide export/erasure/legal-hold policy remains with the canonical owners.
