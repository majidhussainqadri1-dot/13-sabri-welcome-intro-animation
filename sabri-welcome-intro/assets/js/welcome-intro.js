(function () {
  'use strict';

  var intro = document.getElementById('swi-intro');
  if (!intro) return;
  var startedAt = window.performance && typeof window.performance.now === 'function' ? window.performance.now() : 0;
  var preview = intro.getAttribute('data-preview') === '1';
  var previewState = intro.getAttribute('data-preview-state') || 'default';
  var previewVariant = intro.getAttribute('data-preview-variant') || 'full';
  var replay = intro.getAttribute('data-replay') === '1';
  var frequencyDays = Math.max(30, parseInt(intro.getAttribute('data-frequency-days'), 10) || 30);
  var requestedDuration = parseInt(intro.getAttribute('data-duration'), 10);
  var duration = Number.isFinite(requestedDuration) && requestedDuration > 0 ? Math.min(30000, Math.max(1200, requestedDuration)) : 0;
  var reducedDuration = Math.min(1500, Math.max(250, parseInt(intro.getAttribute('data-reduced-duration'), 10) || 900));
  var configVersion = Math.max(1, parseInt(intro.getAttribute('data-config-version'), 10) || 1);
  var experienceVersion = intro.getAttribute('data-experience-version') || '1.1.0';
  var cookieName = intro.getAttribute('data-cookie-name') || 'swi_seen_at_v1';
  var sessionKey = intro.getAttribute('data-session-key') || 'swi_seen_session_v1';
  var localKey = intro.getAttribute('data-local-key') || 'swi_seen_at_v1';
  var claimKey = intro.getAttribute('data-claim-key') || 'swi_claim_v1';
  var neverKey = intro.getAttribute('data-never-key') || 'swi_never_show_v1';
  var profileKey = intro.getAttribute('data-profile-key') || 'swi_a11y_profile_v1';
  var restUrl = (intro.getAttribute('data-rest-url') || '').replace(/\/$/, '');
  var restNonce = intro.getAttribute('data-rest-nonce') || '';
  var eventNonce = intro.getAttribute('data-event-nonce') || '';
  var analytics = intro.getAttribute('data-analytics') === '1';
  var accountAuthoritative = intro.getAttribute('data-account-authoritative') === '1';
  var userId = Math.max(0, parseInt(intro.getAttribute('data-user-id'), 10) || 0);
  var accountSeenAt = Math.max(0, parseInt(intro.getAttribute('data-account-seen-at'), 10) || 0) * 1000;
  var accountNever = intro.getAttribute('data-account-never-show') === '1';
  var accountExperience = intro.getAttribute('data-account-experience') || '';
  var adaptive = intro.getAttribute('data-adaptive') !== '0';
  var neverShowEnabled = intro.getAttribute('data-never-show-enabled') !== '0';
  var versionReplay = intro.getAttribute('data-version-replay') !== '0';
  var guestReconcile = intro.getAttribute('data-guest-reconcile') !== '0';
  var instantExit = intro.getAttribute('data-instant-exit') !== '0';
  var dataSaverStatic = intro.getAttribute('data-data-saver-static') !== '0';
  var performanceCircuit = intro.getAttribute('data-performance-circuit') !== '0';
  var performanceBudget = Math.min(1000, Math.max(40, parseInt(intro.getAttribute('data-performance-budget'), 10) || 120));
  var configuredProfile = sanitizeProfile(intro.getAttribute('data-accessibility-profile') || 'auto');
  var now = Date.now();
  var closed = false;
  var timer = 0;
  var skipTimer = 0;
  var previousFocus = document.activeElement;
  var backgroundStates = [];
  var closeButton = intro.querySelector('[data-swi-close]');
  var continueButton = intro.querySelector('[data-swi-continue]');
  var skipButton = intro.querySelector('[data-swi-skip]');
  var neverButton = intro.querySelector('[data-swi-never]');
  var focusables = [closeButton, continueButton, skipButton, neverButton].filter(Boolean);
  var reduceMotion = Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  var activeVariant = 'full';

  function sanitizeProfile(value) {
    value = String(value || 'auto').toLowerCase().replace(/_/g, '-');
    return ['auto', 'high-contrast', 'large-text', 'simple', 'screen-reader'].indexOf(value) >= 0 ? value : 'auto';
  }
  function safeGet(storage, key) { try { return storage ? storage.getItem(key) : null; } catch (error) { return null; } }
  function safeSet(storage, key, value) { try { if (storage) storage.setItem(key, value); return true; } catch (error) { return false; } }
  function safeRemove(storage, key) { try { if (storage && typeof storage.removeItem === 'function') storage.removeItem(key); } catch (error) { /* fail open */ } }
  function readCookie(name) {
    try { var prefix = name + '='; var parts = document.cookie ? document.cookie.split(';') : []; for (var i = 0; i < parts.length; i += 1) { var part = parts[i].trim(); if (part.indexOf(prefix) === 0) return decodeURIComponent(part.slice(prefix.length)); } } catch (error) { /* fail open */ }
    return '';
  }
  function writeCookie(timestamp) {
    var maxAge = frequencyDays * 86400; var secure = window.location && window.location.protocol === 'https:' ? '; Secure' : '';
    try { document.cookie = cookieName + '=' + encodeURIComponent(String(timestamp)) + '; Path=/; Max-Age=' + maxAge + '; SameSite=Lax' + secure; } catch (error) { /* local storage is fallback */ }
  }
  function validTimestamp(value) { var parsed = parseInt(value, 10); return Number.isFinite(parsed) && parsed > 0 ? parsed : 0; }
  function parseSeen(value) {
    if (!value) return { ts: 0, version: '' };
    try { var parsed = JSON.parse(value); if (parsed && typeof parsed === 'object') return { ts: validTimestamp(parsed.ts), version: String(parsed.version || '') }; } catch (error) { /* legacy numeric */ }
    return { ts: validTimestamp(value), version: '' };
  }
  function seenPayload(timestamp) { return JSON.stringify({ ts: timestamp, version: experienceVersion }); }
  function localSeen() { return parseSeen(safeGet(window.localStorage, localKey)); }
  function localNever() { return neverShowEnabled && safeGet(window.localStorage, neverKey) === '1'; }
  function versionAllowsReplay(record) { return versionReplay && record.version && record.version !== experienceVersion; }
  function recentLocal(record) { return record.ts > 0 && !versionAllowsReplay(record) && record.ts >= now - frequencyDays * 86400000; }
  function recentlySeen() {
    if (preview || replay) return false;
    if (safeGet(window.sessionStorage, sessionKey) === '1') return true;
    var local = localSeen();
    if (localNever()) return true;
    if (accountAuthoritative) {
      if (accountNever) return true;
      if (guestReconcile && recentLocal(local) && local.ts > accountSeenAt) return true;
      return false;
    }
    if (recentLocal(local)) return true;
    var cookie = validTimestamp(readCookie(cookieName));
    return !versionAllowsReplay(local) && cookie >= now - frequencyDays * 86400000;
  }
  function anotherTabClaimed() { if (preview || replay) return false; var claim = validTimestamp(safeGet(window.localStorage, claimKey)); return claim > 0 && now - claim >= 0 && now - claim < 15000; }
  function claim() { if (preview) return; safeSet(window.sessionStorage, sessionKey, '1'); safeSet(window.localStorage, claimKey, String(Date.now())); }
  function persistDismissal() {
    if (preview) return;
    var timestamp = Date.now(); safeSet(window.sessionStorage, sessionKey, '1'); safeSet(window.localStorage, localKey, seenPayload(timestamp)); writeCookie(timestamp);
  }
  function cssReady() { if (typeof window.getComputedStyle !== 'function') return false; var style = window.getComputedStyle(intro); return String(style.getPropertyValue('--swi-css-ready')).trim() === '1'; }
  function eventId() {
    if (window.crypto && typeof window.crypto.getRandomValues === 'function') { var bytes = new Uint32Array(4); window.crypto.getRandomValues(bytes); return Array.prototype.map.call(bytes, function (n) { return n.toString(36); }).join('-'); }
    return String(Date.now()) + '-' + String(Math.random()).slice(2);
  }
  function emit(name, detail) { try { document.dispatchEvent(new CustomEvent('swi:' + name, { detail: detail || {} })); } catch (error) { /* old browser */ } }
  function post(path, payload) {
    if (!restUrl || preview || !window.fetch) return Promise.resolve(null);
    var body = payload || {}; body.config_version = configVersion; body.idempotency_key = body.idempotency_key || eventId();
    var headers = { 'Content-Type': 'application/json' }; if (restNonce) headers['X-WP-Nonce'] = restNonce; if (eventNonce && path === '/event') headers['X-SWI-Nonce'] = eventNonce;
    try { return window.fetch(restUrl + path, { method: 'POST', credentials: 'same-origin', keepalive: true, headers: headers, body: JSON.stringify(body) }).catch(function () { return null; }); } catch (error) { return Promise.resolve(null); }
  }
  function record(eventName, dismissal) {
    if (dismissal && restNonce) post('/dismiss', { event: eventName, experience_version: experienceVersion });
    else if (analytics) post('/event', { event: eventName });
  }
  function currentProfile() {
    var local = sanitizeProfile(safeGet(window.localStorage, profileKey));
    if (accountAuthoritative && configuredProfile !== 'auto') return configuredProfile;
    return local !== 'auto' ? local : configuredProfile;
  }
  function syncAccountPreference(options) {
    if (!accountAuthoritative || !restNonce || !guestReconcile) return;
    var payload = { event: 'preference', last_seen: Math.floor((options.lastSeen || 0) / 1000), never_show: options.neverShow ? 1 : 0, accessibility_profile: sanitizeProfile(options.profile || currentProfile()), experience_version: experienceVersion };
    post('/preference', payload);
  }
  function reconcileGuestToAccount() {
    if (!accountAuthoritative || !guestReconcile || preview) return;
    var local = localSeen(); var never = localNever(); var profile = currentProfile();
    var newer = local.ts > accountSeenAt; var profileNeedsSync = profile !== 'auto' && profile !== configuredProfile;
    if ((never && !accountNever) || newer || profileNeedsSync || (local.version && local.version !== accountExperience)) syncAccountPreference({ lastSeen: local.ts, neverShow: never, profile: profile });
  }
  function setBackgroundInert() {
    var current = intro;
    while (current && current.parentElement && current.parentElement !== document.documentElement) {
      Array.prototype.forEach.call(current.parentElement.children, function (sibling) { if (sibling === current || sibling.nodeType !== 1 || sibling.tagName === 'SCRIPT' || sibling.tagName === 'STYLE') return; backgroundStates.push({ element: sibling, hadInert: sibling.hasAttribute('inert'), ariaHidden: sibling.getAttribute('aria-hidden') }); sibling.setAttribute('inert', ''); sibling.setAttribute('aria-hidden', 'true'); });
      current = current.parentElement;
    }
  }
  function restoreBackground() { backgroundStates.forEach(function (state) { if (!state.element || !state.element.isConnected) return; if (!state.hadInert) state.element.removeAttribute('inert'); if (state.ariaHidden === null) state.element.removeAttribute('aria-hidden'); else state.element.setAttribute('aria-hidden', state.ariaHidden); }); backgroundStates = []; }
  function restoreFocus() { if (!previousFocus || previousFocus === document.body || previousFocus === document.documentElement) return; if (previousFocus.isConnected && typeof previousFocus.focus === 'function') { try { previousFocus.focus({ preventScroll: true }); } catch (error) { previousFocus.focus(); } } }
  function removeListeners() {
    document.removeEventListener('keydown', onKeydown, true); document.removeEventListener('visibilitychange', onVisibilityChange); intro.removeEventListener('animationend', onAnimationEnd); intro.removeEventListener('pointerdown', onPointerIntent);
    if (typeof intro.removeEventListener === 'function') intro.removeEventListener('wheel', onWheelIntent);
    if (closeButton) closeButton.removeEventListener('click', closeIntroButton); if (continueButton) continueButton.removeEventListener('click', continueIntro); if (skipButton) skipButton.removeEventListener('click', skipIntro); if (neverButton) neverButton.removeEventListener('click', neverIntro);
  }
  function finalize() { if (intro.isConnected) intro.remove(); document.body.classList.remove('swi-intro-active'); restoreBackground(); restoreFocus(); }
  function close(eventName, animate, persist) {
    if (closed) return; closed = true; window.clearTimeout(timer); window.clearTimeout(skipTimer); removeListeners(); if (persist !== false) persistDismissal(); emit(eventName, { configVersion: configVersion, experienceVersion: experienceVersion, preview: preview, variant: activeVariant });
    if (['skipped', 'completed', 'closed'].indexOf(eventName) >= 0) record(eventName, true); else if (analytics) record(eventName, false);
    if (animate && !reduceMotion && activeVariant !== 'static') { intro.classList.add('swi-is-closing'); skipTimer = window.setTimeout(finalize, 220); } else finalize();
  }
  function closeIntroButton() { close('closed', true, true); }
  function continueIntro() { close('completed', true, true); }
  function skipIntro() { close('skipped', true, true); }
  function neverIntro() {
    if (!neverShowEnabled || preview) { close('skipped', true, !preview); return; }
    safeSet(window.localStorage, neverKey, '1'); persistDismissal(); syncAccountPreference({ lastSeen: Date.now(), neverShow: true, profile: currentProfile() }); close('never_show', true, false);
  }
  function onAnimationEnd(event) { if (event.target === intro && event.animationName === 'swi-overlay-exit') close('completed', false, true); }
  function onVisibilityChange() { if (!document.hidden || closed) return; closed = true; window.clearTimeout(timer); window.clearTimeout(skipTimer); removeListeners(); emit('closed', { reason: 'page_hidden', configVersion: configVersion, preview: preview }); finalize(); }
  function onKeydown(event) {
    if (event.key === 'Escape') { event.preventDefault(); close('skipped', true, true); return; }
    if (event.key !== 'Tab' || !focusables.length) return; var first = focusables[0]; var last = focusables[focusables.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); } else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  }
  function onPointerIntent(event) { if (!instantExit || closed) return; if (event.target === intro || event.target === intro.querySelector('.swi-ambient')) close('completed', false, true); }
  function onWheelIntent() { if (instantExit && !closed) close('completed', false, true); }
  function failOpen(reason) { window.clearTimeout(timer); window.clearTimeout(skipTimer); removeListeners(); document.body.classList.remove('swi-intro-active'); restoreBackground(); emit('error', { reason: reason, configVersion: configVersion, preview: preview }); if (analytics) record('error', false); if (intro.isConnected) intro.remove(); restoreFocus(); }

  function chooseVariant(profile) {
    if (preview) {
      if (previewState === 'data-saver' || previewState === 'offline') return 'static';
      if (['full', 'light', 'static'].indexOf(previewVariant) >= 0) return previewVariant;
    }
    if (reduceMotion || profile === 'simple' || profile === 'screen-reader') return 'static';
    var nav = typeof navigator !== 'undefined' ? navigator : {};
    var connection = nav.connection || nav.mozConnection || nav.webkitConnection || null;
    if (dataSaverStatic && connection && connection.saveData) return 'static';
    if (!adaptive) return 'full';
    if (connection && (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g')) return 'light';
    if ((Number(nav.deviceMemory) > 0 && Number(nav.deviceMemory) <= 2) || (Number(nav.hardwareConcurrency) > 0 && Number(nav.hardwareConcurrency) <= 2)) return 'light';
    if (nav.onLine === false) return 'static';
    return 'full';
  }
  function applyExperience(profile, variant) {
    ['high-contrast', 'large-text', 'simple', 'screen-reader'].forEach(function (name) { intro.classList.remove('swi-a11y-' + name); });
    if (profile !== 'auto') intro.classList.add('swi-a11y-' + profile);
    ['full', 'light', 'static'].forEach(function (name) { intro.classList.remove('swi-variant-' + name); }); activeVariant = variant; intro.classList.add('swi-variant-' + variant);
    if (variant === 'static') reduceMotion = true;
    emit('variant', { variant: variant, profile: profile, experienceVersion: experienceVersion }); if (analytics) record('variant_' + variant, false);
  }
  function maybeTripPerformanceCircuit(profile) {
    if (!performanceCircuit || activeVariant === 'static' || !startedAt || !window.performance || typeof window.performance.now !== 'function') return;
    if (window.performance.now() - startedAt > performanceBudget) applyExperience(profile, 'static');
  }

  try {
    if (previewState === 'disabled' || previewState === 'error') { failOpen('preview_' + previewState); return; }
    reconcileGuestToAccount();
    if (!preview && !replay && (recentlySeen() || anotherTabClaimed())) { intro.remove(); return; }
    if (!cssReady()) { failOpen('css_unavailable'); return; }
    if (previewState === 'reduced') reduceMotion = true;
    var profile = currentProfile(); var variant = chooseVariant(profile); applyExperience(profile, variant);
    if (reduceMotion && duration > 0) duration = Math.min(duration, reducedDuration);
    if (previewState === 'skipped') { intro.removeAttribute('hidden'); window.setTimeout(skipIntro, 120); return; }
    claim(); intro.removeAttribute('hidden'); document.body.classList.add('swi-intro-active'); setBackgroundInert();
    if (closeButton) closeButton.addEventListener('click', closeIntroButton); if (continueButton) continueButton.addEventListener('click', continueIntro); if (skipButton) skipButton.addEventListener('click', skipIntro); if (neverButton) neverButton.addEventListener('click', neverIntro);
    document.addEventListener('keydown', onKeydown, true); document.addEventListener('visibilitychange', onVisibilityChange); intro.addEventListener('animationend', onAnimationEnd); intro.addEventListener('pointerdown', onPointerIntent); intro.addEventListener('wheel', onWheelIntent, { passive: true });
    if (replay) { emit('replay', { experienceVersion: experienceVersion }); if (analytics) record('replay', false); }
    emit('shown', { configVersion: configVersion, experienceVersion: experienceVersion, preview: preview, variant: activeVariant }); if (analytics) record('shown', false);
    window.requestAnimationFrame(function () { maybeTripPerformanceCircuit(profile); var target = continueButton || skipButton || closeButton || intro; try { target.focus({ preventScroll: true }); } catch (error) { target.focus(); } });
    if (duration > 0) timer = window.setTimeout(function () { close('completed', false, true); }, duration);
  } catch (error) { failOpen('runtime_exception'); }
}());
