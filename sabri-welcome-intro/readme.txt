=== Sabri Welcome Intro Animation ===
Contributors: sabrihomeopathy
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.2.0
License: GPLv2 or later

An accessible, fail-safe eight-second welcome animation for Sabri Homeopathy.

== Description ==

This modular plugin displays the approved Sabri Homeopathy welcome sequence:

* The circular SH roundel from the approved Sabri logo system.
* The American English brand name, Sabri Homeopathy.
* The American English claim: The Tridimensional Healing System of Soul, Vital Force, and Matter.
* A bright-orange line that draws from left to right.
* A complete eight-second timeline followed by the already-loaded public page.

The intro appears once per browser session, records its session claim as soon as it starts, includes Skip Intro and Escape controls, supports older themes without wp_body_open(), contains keyboard focus within the dialog, restores background state and focus after dismissal, and automatically presents a short static version when a visitor has enabled reduced-motion preferences.

The overlay is hidden by default and includes a CSS exit fail-safe, so a delayed, optimized, blocked, or failed runtime script cannot leave the website permanently covered.

Administrator previews use a signed WordPress nonce, require the manage_options capability, bypass public caches, and are marked noindex/noarchive.

== Installation ==

1. In WordPress, open Plugins > Add New > Upload Plugin.
2. Upload the ZIP file and select Install Now.
3. Activate Sabri Welcome Intro Animation.
4. Open Settings > Sabri Welcome Intro to enable, disable, or preview the intro.
5. Test the public website in a private browser window on desktop and mobile.
6. Complete the repository staging-acceptance checklist before production deployment.

== Frequently Asked Questions ==

= Does the intro run on every page? =

No. It appears on the visitor's first accepted public page in a browser session and does not repeat during that session.

= How can I preview it again? =

An administrator can use the signed Preview Intro button under Settings > Sabri Welcome Intro. An unsigned public query parameter is rejected and removed.

= Does it delay the underlying page from loading? =

No. The website loads behind the lightweight visual overlay. The overlay is fail-open and cannot remain as a permanent blocker when JavaScript fails.

== Changelog ==

= 0.2.0 =
* Added hidden-by-default, optimization-resistant early bootstrap behavior and an independent CSS exit fail-safe.
* Added immediate session claiming, sessionStorage fallback, and short-lived cross-tab coordination.
* Added administrator-only nonce-protected previews, no-cache headers, noindex/noarchive, and unauthorized-preview cleanup.
* Added initial focus, Tab containment, background inert/aria-hidden restoration, prior-focus restoration, and complete event cleanup.
* Coordinated normal completion through animationend with a bounded cleanup fallback.
* Increased the Skip Intro touch target to at least 44 by 44 CSS pixels.
* Replaced the rounded-square, font-dependent logo with the approved circular vector SH roundel.
* Normalized the Founder/author identity to Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed.
* Added deterministic behavior, security, accessibility, packaging, and contract tests.

= 0.1.1 =
* Replaced the Urdu brand name with Sabri Homeopathy.
* Standardized the complete welcome interface in American English.

= 0.1.0 =
* Initial eight-second welcome animation.
* Added responsive layout, session control, skip control, fallback rendering, and reduced-motion support.
