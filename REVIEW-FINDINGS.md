# File 13 — Corrective Source Review

**Module:** Sabri Welcome Intro Animation  
**Baseline version:** 0.1.1  
**Baseline source commit:** `212cc9723afdf7ced46889672323f2c05bb29f12`  
**Review date:** 30 July 2026  
**Verdict:** **CHANGES REQUIRED — DO NOT MERGE OR DEPLOY**

## Scope and governing requirements

The review checked the supplied ZIP, the complete extracted source tree, repository evidence, and the approved File 13 requirements in the Sabri Social Homeopathy Platform Comprehensive Master Plan v2.0.

The approved behavior requires an eight-second branded intro, the approved SH logo, “Sabri Homeopathy,” the approved Tridimensional Healing claim, an orange left-to-right line, one display per browser session, Skip Intro and Escape controls, a short static reduced-motion version, and continued loading of the underlying page.

## Checks that passed

- Original ZIP SHA-256 matched the recorded digest: `74eb19f24a22439202f9da50b1e2a69083046543108d09c4d6fd911cbdd71fd5`.
- ZIP integrity test passed.
- PHP syntax passed: 6/6 files.
- JavaScript syntax passed: 2/2 files.
- No unsafe archive traversal path was detected.
- No obvious dangerous PHP execution primitive was found by targeted static search.
- Standard duration is 8000 ms.
- Reduced-motion duration is 1200 ms.
- Approved English brand name and Tridimensional Healing claim are present.
- Skip button, Escape dismissal, `wp_body_open` rendering, and `wp_footer` fallback are present.

## Confirmed blockers

### F13-01 — Critical — Overlay can block the website indefinitely when the footer JavaScript is delayed, blocked, or fails

The overlay is fixed above the whole page. Its exit animation is applied only after JavaScript adds `swi-is-running`, and removal is performed only by `welcome-intro.js`. The `<noscript>` fallback covers JavaScript-disabled browsers, but not a failed, delayed, optimized, or blocked script. This is especially material under LiteSpeed/Hostinger script optimization.

**Required correction:** add an independent fail-safe that guarantees pointer release and overlay removal after a bounded interval even when the main runtime script does not execute; protect the critical bootstrap/runtime from delay/defer optimization where necessary.

### F13-02 — Critical — Modal accessibility lifecycle is incomplete

The markup declares `role="dialog"` and `aria-modal="true"`, but the runtime does not place initial focus, trap Tab/Shift+Tab within the dialog, make the background inert, or restore the previously focused element after dismissal. Keyboard users can move into the covered underlying page.

**Required correction:** implement initial focus, focus containment, background inert/aria-hidden management with safe restoration, and prior-focus restoration on all exit paths.

### F13-03 — High — Approved logo specification is not implemented

The approved logo system requires a circular/roundel mark, a thin separator between S and H, and a strong circular boundary. The supplied SVG explicitly renders a rounded square, has no separator, uses embedded text, and uses `#F26B1C` rather than the approved primary Sabri Orange `#FF8A1F`.

**Required correction:** replace the asset with an approved circular vector logo using stable paths rather than font-dependent `<text>`, include the separator and strong boundary, and use the frozen brand palette.

### F13-04 — High — Public preview bypass is not authorized

Any visitor can append `?swi_preview=1`. Both bootstrap and runtime treat that query as preview mode and bypass session suppression. The server performs no capability or nonce validation.

**Required correction:** generate a signed administrator-only preview token/nonce, validate it server-side, and ignore or remove unauthorized preview parameters. Preview responses should also be excluded from public indexing and caches.

### F13-05 — High — Session-only behavior is not robust across concurrent tabs and restricted-cookie modes

The session cookie is written only after completion or manual skip. Two tabs opened before either intro ends can both display it. When cookies are restricted, the intro can repeat on every navigation.

**Required correction:** record the session state as soon as the first accepted intro starts, add a safe session-storage/in-memory fallback, and test ordinary, private, blocked-cookie, multi-tab, and cache-restored navigation.

### F13-06 — High — LiteSpeed/cache compatibility is unproven and current architecture is flash-prone

Seen-state suppression depends on an external head script. If optimization delays that script, a returning visitor can see the overlay before the `swi-intro-seen` class is applied. The baseline CI does not test this behavior.

**Required correction:** implement an optimization-resistant early suppression mechanism and test it with the actual Hostinger staging cache and LiteSpeed settings.

### F13-07 — Medium — Escape listener is never removed

The document-level keydown listener is anonymous and remains attached after the overlay is removed. The `closed` guard prevents repeated functional action, but lifecycle cleanup is incomplete.

**Required correction:** use a named handler and remove all listeners/timers during cleanup.

### F13-08 — Medium — Exit timing can cut the final fade

CSS starts a 700 ms fade at 7.3 seconds, while JavaScript removes the node at 8.0 seconds. Scheduling differences can remove the overlay before the final animation frame or prolong it when timers are throttled.

**Required correction:** coordinate completion through `animationend` with a bounded timeout fallback, and test foreground/background-tab behavior.

### F13-09 — Medium — Skip control does not meet the approved 44 px touch-target minimum

The CSS sets `min-height: 40px`.

**Required correction:** provide at least 44 × 44 CSS pixels, including safe-area and mobile acceptance.

### F13-10 — Medium — Founder/author identity is stale

Plugin metadata uses `Dr. Allama Majid Hussain Sabri`, while the governing master plan freezes `Dr. Allamah Majid Hussain Sabri Muhaddith Mursheed` for public institutional surfaces.

**Required correction:** normalize plugin metadata, documentation, and public brand surfaces to the approved spelling.

### F13-11 — High — CI proves syntax and checksums only, not behavior

The workflow verifies source checksums, PHP syntax, and JavaScript syntax. It does not test modal lifecycle, cookie/session suppression, preview authorization, reduced motion, JavaScript failure, cache behavior, accessibility, responsive layout, or WordPress activation.

**Required correction:** add deterministic contract tests and browser/runtime tests, plus a WordPress staging acceptance matrix.

### F13-12 — Governance — Repository is currently public

The repository visibility is Public. This conflicts with the project’s previously established proprietary-repository control unless the Founder explicitly approves public release.

**Required correction:** make the repository Private before merge, or record a formal Founder decision authorizing public-source publication.

## Release and staging blockers

The following remain mandatory after source correction:

1. Fresh WordPress staging installation and activation.
2. Active-theme and File 20 shell compatibility.
3. LiteSpeed cache, delayed-JavaScript, and page-cache tests.
4. Chrome, Edge, Firefox, Safari/WebKit-equivalent acceptance.
5. Viewport checks at 320, 360, 390, 480, 768, 1024, 1366, 1600, and 1920 px.
6. Keyboard-only, screen-reader-oriented semantics, reduced-motion, zoom, and high-contrast checks.
7. Session, private-mode, multi-tab, blocked-cookie, back/forward cache, refresh, and preview tests.
8. JavaScript failure and slow-resource fail-safe tests.
9. Founder visual acceptance of logo, timing, text, and line animation.
10. Reproducible corrected ZIP, manifest, checksum, rollback notes, and post-install smoke test.

## Merge gate

- Keep baseline PR #1 Draft, Open, and Unmerged.
- Preserve `baseline/file-13-original-import` unchanged as the historical source import.
- Perform corrections only on `audit/file-13-source-review` or a dedicated corrective release branch.
- Do not merge to `main`, install on live, or claim production completion until every confirmed defect is corrected, retested, and accepted on Hostinger staging.
