# Security

File 13 uses a fail-open-to-content design: failure of the intro must never block ordinary page access.

Security controls:
- public rendering is accepted only from File 20's exact `sabri_shell_welcome_intro_invoke` contract;
- configuration writes require authenticated authorization, POST/REST protections, sanitization, and optimistic revision checks;
- preview is authenticated, no-store, and noindex;
- no remote scripts, remote fonts, autoplay audio, secrets, tokens, patient data, or private content are embedded;
- optional analytics stores aggregate counters only and does not store IP, user ID, user agent, page history, or fingerprint;
- File 20 Safe Mode/emergency disable is honored directly when its class is available;
- File 24 may additionally suppress the intro through the `swi_intro_safe_mode_active` assurance adapter.

Security reports must not include production secrets or private patient information.
