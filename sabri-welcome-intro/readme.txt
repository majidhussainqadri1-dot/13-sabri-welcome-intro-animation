=== Sabri Welcome Intro Animation ===
Contributors: majidhussainqadri1-dot
Tags: welcome, accessibility, animation, rtl, privacy
Requires at least: 6.6
Tested up to: 7.0.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Accessible, non-blocking welcome intro for the Sabri Social Homeopathy Platform.

== Description ==

File 13 provides the canonical welcome intro experience. Version 1.0.0 implements:

* first eligible visit with a minimum 30-day suppression period;
* logged-in account timestamp plus guest first-party cookie/localStorage fallback;
* same-session and multi-tab suppression;
* home-only safe default and explicit suppression for login, clinical, booking, emergency and task routes;
* Continue, Skip, Close and Escape controls;
* reduced-motion, keyboard, focus, screen-reader and RTL support;
* hidden-by-default fail-open behavior when JavaScript, CSS or storage fails;
* green primary visual identity with contextual orange motion accent;
* administrator preview states, diagnostics, versioned configuration and minimized audit trail;
* optional aggregate-only analytics without IP address, user agent or fingerprint;
* File 20 registry/render contract and File 24 safe-mode integration;
* privacy exporter/eraser support and non-destructive uninstall.

== Installation ==

1. Upload the plugin folder or release ZIP.
2. Activate the plugin on staging first.
3. Open Settings > Sabri Welcome Intro.
4. Run the staging acceptance checklist before production deployment.

== Changelog ==

= 1.0.0 =
* Completed File 13 source implementation against the governing master plan and updated platform directives.
* Replaced once-per-session-only behavior with a 30-day minimum frequency policy.
* Added account preference, route governance, configuration versioning, audit, analytics, privacy, REST and shell contracts.
* Adopted green primary identity and retained orange as a contextual motion accent.
* Added complete automated source/package QA and release documentation.
