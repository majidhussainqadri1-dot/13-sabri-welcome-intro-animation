# File 13 — Hostinger Staging Acceptance

No item may be marked complete without dated evidence.

## Installation and rollback

- [ ] Verified full staging backup exists before installation.
- [ ] Corrected ZIP SHA-256 equals `a59aaab80d89a41cfa57c9bdff64a392ae8874e2c3b03161bd62020f845c07b1`.
- [ ] Fresh install activates without fatal error.
- [ ] Upgrade from Version 0.1.1 to 0.2.0 succeeds.
- [ ] Disable/re-enable preserves intended setting behavior.
- [ ] Uninstall removes only `swi_enabled` and no unrelated data.
- [ ] Backup restore succeeds.
- [ ] Rollback to Version 0.1.1 succeeds in a controlled test.

## Functional timing

- [ ] Normal intro lasts eight seconds within accepted browser timing tolerance.
- [ ] Bright-orange line draws left to right.
- [ ] Underlying page continues loading.
- [ ] Skip Intro closes immediately and safely.
- [ ] Escape closes immediately and safely.
- [ ] CSS-only failure test releases pointer interaction after the bounded interval.
- [ ] Delayed runtime does not re-lock an already released page.
- [ ] Reduced-motion mode is static and closes after approximately 1.2 seconds.

## Session and navigation

- [ ] First public page displays the intro.
- [ ] Refresh does not repeat it in the same browser session.
- [ ] Navigation to another page does not repeat it.
- [ ] New normal tab during the intro does not duplicate it.
- [ ] Private-window behavior is correct.
- [ ] Cookie-restricted mode uses a safe fallback without blocking.
- [ ] Back/forward cache behavior is correct.
- [ ] New browser session displays the intro once again.

## Preview security

- [ ] Signed administrator Preview Intro link works even when public intro is disabled.
- [ ] Logged-out `?swi_preview=1` is redirected to the clean URL.
- [ ] Logged-in nonadministrator preview attempt is rejected.
- [ ] Invalid/expired nonce is rejected.
- [ ] Authorized preview returns no-cache headers.
- [ ] Authorized preview is `noindex, noarchive`.
- [ ] Preview does not mutate the ordinary session state.

## Accessibility

- [ ] Initial focus moves to Skip Intro.
- [ ] Tab and Shift+Tab cannot enter the covered page.
- [ ] Background is inert while the dialog is active.
- [ ] Original `aria-hidden`/`inert` states are restored exactly.
- [ ] Prior focus is restored after dismissal.
- [ ] Keyboard-only use passes.
- [ ] Screen-reader-oriented semantic review passes.
- [ ] Focus indicator is visible.
- [ ] 200% and 400% zoom do not clip essential controls.
- [ ] High-contrast/forced-color review passes or documented adjustments are completed.

## Cache and performance

- [ ] LiteSpeed page cache enabled: returning visitor has no overlay flash.
- [ ] LiteSpeed delayed JavaScript enabled: overlay never becomes permanent.
- [ ] LiteSpeed defer/minify/combine configurations tested.
- [ ] Cache purge after activation/update works.
- [ ] Logged-in administrator preview is never served from public cache.
- [ ] No console error occurs.
- [ ] No material layout shift remains after intro removal.

## Responsive and integration matrix

Test at: `320`, `360`, `390`, `480`, `768`, `1024`, `1366`, `1600`, and `1920` CSS pixels.

- [ ] Chrome/Chromium.
- [ ] Microsoft Edge.
- [ ] Firefox.
- [ ] Safari/WebKit-equivalent.
- [ ] Active WordPress theme.
- [ ] File 20 Unified Application Shell.
- [ ] No horizontal overflow.
- [ ] Safe-area placement works on mobile.
- [ ] Skip target is at least 44 × 44 CSS pixels.

## Founder visual acceptance

- [ ] Circular SH logo approved.
- [ ] Bright Sabri Orange approved.
- [ ] `Sabri Homeopathy` typography approved.
- [ ] Tridimensional Healing claim approved.
- [ ] Animation sequence and timing approved.
- [ ] Final screenshots/video evidence attached to the corrective PR.

## Release gate

- [ ] Repository governance/visibility decision recorded.
- [ ] All defects found during staging corrected and retested.
- [ ] GitHub corrective CI green on final commit.
- [ ] PR remains Draft until every item above is accepted.
- [ ] Explicit Founder authorization obtained before merge/deployment.
