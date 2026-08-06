# Hostinger Staging Acceptance — Mandatory Before Merge/Deployment

## Artifact and recovery

- [ ] Verify branch head, workflow run, ZIP SHA-256, source checksum ledger, manifest and ZIP CRC.
- [ ] Verify database/files backup and complete an isolated restore.
- [ ] Fresh install activates without warning/fatal and creates only documented options/rewrite.
- [ ] Upgrade from the deployed 0.1.1/0.2.0 state preserves enable state and does not duplicate configuration/audit.
- [ ] Rollback to the prior package succeeds without data loss.

## Runtime and integrations

- [ ] File 20 registry/official slot renders exactly once; fallback body/footer hooks do not duplicate.
- [ ] File 20 runtime frequency/eligibility and account preference provider override local fallback correctly.
- [ ] File 24 safe mode and `SWI_DISABLE` remove the intro immediately without companion mutation.
- [ ] File 25 green tokens, logo, spacing, focus and motion render correctly.
- [ ] Home first eligible visit shows; refresh/internal navigation/same session never repeats.
- [ ] Continue, Skip, Close, Escape and auto-completion suppress for at least 30 days.
- [ ] Day-30 expiry allows at most one display on the next eligible session.
- [ ] Login, registration, recovery, account, support, booking, appointment, clinical, emergency, checkout/cart and deep task links are never interrupted.
- [ ] JavaScript disabled, blocked CSS/JS, blocked cookie/storage and failed REST requests reveal the page and never leave an overlay.
- [ ] Signed preview states are admin-only, no-cache/noindex and do not mutate public state.
- [ ] LiteSpeed guest cache, logged-in no-cache behavior and cache purge do not leak/repeat state.

## Accessibility and visual

- [ ] Keyboard-only sequence, focus containment, Escape, cleanup and focus restoration pass.
- [ ] NVDA/JAWS on Windows and VoiceOver on Apple announce once with correct name/role/state.
- [ ] 320 px, 200% and 400% zoom have no clipping or horizontal page scrollbar.
- [ ] Urdu, Arabic and English copy reflow; RTL/LTR direction and icon/action order remain intelligible.
- [ ] Reduced motion is static/short with no delayed hidden animation.
- [ ] Contrast and non-text contrast pass; forced-colors remains usable.
- [ ] Chrome, Firefox, Edge and Safari; Android and iOS representative devices pass.
- [ ] Founder accepts logo, green primary, contextual orange cue, copy and timing.

## Performance, privacy and operations

- [ ] Core Web Vitals remain “good” on representative public pages.
- [ ] No remote request, audio, fingerprint or personal analytics appears in network/storage review.
- [ ] WordPress privacy export/erasure and profile reset work for a real user.
- [ ] System Check remains healthy and diagnostics contain no private/request data.
- [ ] Error logs contain no File 13 warning/fatal/deprecation.
- [ ] Monitoring owner, rollback window and post-deploy smoke plan are approved.
