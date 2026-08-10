# Security, Privacy and Threat Model

## Controls

- Administrator writes: native capability, CSRF nonce, strict sanitization, bounded fields and optimistic version check.
- Preview: administrator capability, signed nonce, no-cache, noindex/noarchive/nofollow and unauthorized redirect cleanup.
- REST dismissal: authenticated cookie/REST nonce, allowlisted dismissal event, required idempotency key and bounded rate limit.
- Public analytics: disabled by default, separate nonce, allowlisted aggregate event, required idempotency key, 90-day/day-cap bounds.
- Routing: same-origin path normalization, traversal rejection, explicit allowlist and suppressed prefixes.
- Rendering: hidden attribute and CSS readiness marker; no script can leave a visible blocking overlay when assets fail.
- Secrets: no provider, token, private key or remote runtime dependency.
- Diagnostics: configuration facts and asset booleans only; no request/user/network data.

## Threats addressed

- overlay denial of service from delayed/failed JavaScript or CSS;
- repeated nuisance display through reload/navigation/multi-tab behavior;
- unauthorized preview or configuration mutation;
- stale/lost settings update;
- route interruption during login, recovery, clinical, emergency or transactional tasks;
- analytics fingerprinting and unbounded retention;
- duplicate shell/design-system ownership;
- malicious route values, traversal and unsafe redirects;
- unsafe destructive uninstall.

## Residual environment risks

Theme DOM, optimization/minification, LiteSpeed caching, browser/assistive-technology differences and cross-file provider versions require staging evidence. The module fails open or disabled when an integration returns malformed/unsafe state.
