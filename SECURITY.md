# Security

File 13 is a fail-closed historical compatibility module: it emits no active public intro and therefore cannot block ordinary page access.

Security controls:
- File 13 does not register `sabri_shell_welcome_intro_invoke`;
- no public analytics endpoint or writable configuration route is registered;
- legacy state sanitizes to disabled even when stale callers attempt activation;
- preview is authenticated, no-store, and noindex;
- no remote scripts, remote fonts, autoplay audio, secrets, tokens, patient data, or private content are embedded;
- File 24 evaluates `spcrc/file13_contract_state`; any legacy public-renderer or analytics registration produces `blocked`;
- File 20 and File 25 remain responsible for active welcome security and accessibility within their owned boundaries.

Security reports must not include production secrets or private patient information.
