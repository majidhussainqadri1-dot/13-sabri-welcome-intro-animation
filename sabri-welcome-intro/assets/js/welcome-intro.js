(function () {
  'use strict';

  var intro = document.getElementById('swi-intro');
  if (!intro) return;

  var preview = intro.getAttribute('data-preview') === '1';
  var previewState = intro.getAttribute('data-preview-state') || 'default';
  var frequencyDays = Math.max(30, parseInt(intro.getAttribute('data-frequency-days'), 10) || 30);
  var duration = Math.min(8000, Math.max(1200, parseInt(intro.getAttribute('data-duration'), 10) || 8000));
  var reducedDuration = Math.min(1500, Math.max(250, parseInt(intro.getAttribute('data-reduced-duration'), 10) || 900));
  var configVersion = Math.max(1, parseInt(intro.getAttribute('data-config-version'), 10) || 1);
  var cookieName = intro.getAttribute('data-cookie-name') || 'swi_seen_at_v1';
  var sessionKey = intro.getAttribute('data-session-key') || 'swi_seen_session_v1';
  var localKey = intro.getAttribute('data-local-key') || 'swi_seen_at_v1';
  var claimKey = intro.getAttribute('data-claim-key') || 'swi_claim_v1';
  var restUrl = (intro.getAttribute('data-rest-url') || '').replace(/\/$/, '');
  var restNonce = intro.getAttribute('data-rest-nonce') || '';
  var eventNonce = intro.getAttribute('data-event-nonce') || '';
  var analytics = intro.getAttribute('data-analytics') === '1';
  var accountAuthoritative = intro.getAttribute('data-account-authoritative') === '1';
  var now = Date.now();
  var closed = false;
  var timer = 0;
  var skipTimer = 0;
  var previousFocus = document.activeElement;
  var backgroundStates = [];
  var closeButton = intro.querySelector('[data-swi-close]');
  var continueButton = intro.querySelector('[data-swi-continue]');
  var skipButton = intro.querySelector('[data-swi-skip]');
  var focusables = [closeButton, continueButton, skipButton].filter(Boolean);
  var reduceMotion = Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);

  function safeGet(storage, key) {
    try { return storage ? storage.getItem(key) : null; } catch (error) { return null; }
  }
  function safeSet(storage, key, value) {
    try { if (storage) storage.setItem(key, value); } catch (error) { /* fail open */ }
  }
  function readCookie(name) {
    try {
      var prefix = name + '=';
      var parts = document.cookie ? document.cookie.split(';') : [];
      for (var i = 0; i < parts.length; i += 1) {
        var part = parts[i].trim();
        if (part.indexOf(prefix) === 0) return decodeURIComponent(part.slice(prefix.length));
      }
    } catch (error) { /* fail open */ }
    return '';
  }
  function writeCookie(timestamp) {
    var maxAge = frequencyDays * 86400;
    var secure = window.location.protocol === 'https:' ? '; Secure' : '';
    try {
      document.cookie = cookieName + '=' + encodeURIComponent(String(timestamp)) + '; Path=/; Max-Age=' + maxAge + '; SameSite=Lax' + secure;
    } catch (error) { /* local storage is fallback */ }
  }
  function validTimestamp(value) {
    var parsed = parseInt(value, 10);
    return Number.isFinite(parsed) && parsed > 0 ? parsed : 0;
  }
  function recentlySeen() {
    if (preview) return false;
    if (safeGet(window.sessionStorage, sessionKey) === '1') return true;
    if (accountAuthoritative) return false;
    var cutoff = now - frequencyDays * 86400000;
    var local = validTimestamp(safeGet(window.localStorage, localKey));
    var cookie = validTimestamp(readCookie(cookieName));
    return Math.max(local, cookie) >= cutoff;
  }
  function anotherTabClaimed() {
    if (preview) return false;
    var claim = validTimestamp(safeGet(window.localStorage, claimKey));
    return claim > 0 && now - claim >= 0 && now - claim < 15000;
  }
  function claim() {
    if (preview) return;
    safeSet(window.sessionStorage, sessionKey, '1');
    safeSet(window.localStorage, claimKey, String(Date.now()));
  }
  function persistDismissal() {
    if (preview) return;
    var timestamp = Date.now();
    safeSet(window.sessionStorage, sessionKey, '1');
    safeSet(window.localStorage, localKey, String(timestamp));
    writeCookie(timestamp);
  }
  function cssReady() {
    if (typeof window.getComputedStyle !== 'function') return false;
    var style = window.getComputedStyle(intro);
    return String(style.getPropertyValue('--swi-css-ready')).trim() === '1';
  }
  function eventId() {
    if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
      var bytes = new Uint32Array(4); window.crypto.getRandomValues(bytes);
      return Array.prototype.map.call(bytes, function (n) { return n.toString(36); }).join('-');
    }
    return String(Date.now()) + '-' + String(Math.random()).slice(2);
  }
  function emit(name, detail) {
    try { document.dispatchEvent(new CustomEvent('swi:' + name, { detail: detail || {} })); } catch (error) { /* old browser */ }
  }
  function post(path, eventName) {
    if (!restUrl || preview) return;
    var body = JSON.stringify({ event: eventName, config_version: configVersion, idempotency_key: eventId() });
    var headers = { 'Content-Type': 'application/json' };
    if (restNonce) headers['X-WP-Nonce'] = restNonce;
    if (eventNonce && path === '/event') headers['X-SWI-Nonce'] = eventNonce;
    try {
      if (window.fetch) {
        window.fetch(restUrl + path, { method: 'POST', credentials: 'same-origin', keepalive: true, headers: headers, body: body }).catch(function () {});
      }
    } catch (error) { /* persistence already happened locally */ }
  }
  function record(eventName, dismissal) {
    if (dismissal && restNonce) post('/dismiss', eventName);
    else if (analytics) post('/event', eventName);
  }
  function setBackgroundInert() {
    var current = intro;
    while (current && current.parentElement && current.parentElement !== document.documentElement) {
      Array.prototype.forEach.call(current.parentElement.children, function (sibling) {
        if (sibling === current || sibling.nodeType !== 1 || sibling.tagName === 'SCRIPT' || sibling.tagName === 'STYLE') return;
        backgroundStates.push({ element: sibling, hadInert: sibling.hasAttribute('inert'), ariaHidden: sibling.getAttribute('aria-hidden') });
        sibling.setAttribute('inert', '');
        sibling.setAttribute('aria-hidden', 'true');
      });
      current = current.parentElement;
    }
  }
  function restoreBackground() {
    backgroundStates.forEach(function (state) {
      if (!state.element || !state.element.isConnected) return;
      if (!state.hadInert) state.element.removeAttribute('inert');
      if (state.ariaHidden === null) state.element.removeAttribute('aria-hidden');
      else state.element.setAttribute('aria-hidden', state.ariaHidden);
    });
    backgroundStates = [];
  }
  function restoreFocus() {
    if (!previousFocus || previousFocus === document.body || previousFocus === document.documentElement) return;
    if (previousFocus.isConnected && typeof previousFocus.focus === 'function') {
      try { previousFocus.focus({ preventScroll: true }); } catch (error) { previousFocus.focus(); }
    }
  }
  function removeListeners() {
    document.removeEventListener('keydown', onKeydown, true);
    document.removeEventListener('visibilitychange', onVisibilityChange);
    intro.removeEventListener('animationend', onAnimationEnd);
    if (closeButton) closeButton.removeEventListener('click', closeIntroButton);
    if (continueButton) continueButton.removeEventListener('click', continueIntro);
    if (skipButton) skipButton.removeEventListener('click', skipIntro);
  }
  function finalize() {
    if (intro.isConnected) intro.remove();
    document.body.classList.remove('swi-intro-active');
    restoreBackground();
    restoreFocus();
  }
  function close(eventName, animate) {
    if (closed) return;
    closed = true;
    window.clearTimeout(timer); window.clearTimeout(skipTimer);
    removeListeners();
    persistDismissal();
    emit(eventName, { configVersion: configVersion, preview: preview });
    record(eventName, true);
    if (animate && !reduceMotion) {
      intro.classList.add('swi-is-closing');
      skipTimer = window.setTimeout(finalize, 220);
    } else finalize();
  }
  function closeIntroButton() { close('closed', true); }
  function continueIntro() { close('completed', true); }
  function skipIntro() { close('skipped', true); }
  function onAnimationEnd(event) {
    if (event.target === intro && event.animationName === 'swi-overlay-exit') close('completed', false);
  }
  function onVisibilityChange() {
    if (!document.hidden || closed) return;
    closed = true;
    window.clearTimeout(timer); window.clearTimeout(skipTimer);
    removeListeners();
    emit('closed', { reason: 'page_hidden', configVersion: configVersion, preview: preview });
    finalize();
  }
  function onKeydown(event) {
    if (event.key === 'Escape') { event.preventDefault(); close('skipped', true); return; }
    if (event.key !== 'Tab' || !focusables.length) return;
    var first = focusables[0]; var last = focusables[focusables.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  }
  function failOpen(reason) {
    window.clearTimeout(timer); window.clearTimeout(skipTimer);
    removeListeners();
    document.body.classList.remove('swi-intro-active');
    restoreBackground();
    emit('error', { reason: reason, configVersion: configVersion, preview: preview });
    if (analytics) record('error', false);
    if (intro.isConnected) intro.remove();
    restoreFocus();
  }

  try {
    if (previewState === 'disabled' || previewState === 'error') { failOpen('preview_' + previewState); return; }
    if (!preview && (recentlySeen() || anotherTabClaimed())) { intro.remove(); return; }
    if (!cssReady()) { failOpen('css_unavailable'); return; }

    if (previewState === 'reduced') reduceMotion = true;
    if (reduceMotion) { intro.classList.add('swi-reduced-motion'); duration = reducedDuration; }
    if (previewState === 'skipped') { intro.removeAttribute('hidden'); window.setTimeout(skipIntro, 120); return; }

    claim();
    intro.removeAttribute('hidden');
    document.body.classList.add('swi-intro-active');
    setBackgroundInert();
    if (closeButton) closeButton.addEventListener('click', closeIntroButton);
    if (continueButton) continueButton.addEventListener('click', continueIntro);
    if (skipButton) skipButton.addEventListener('click', skipIntro);
    document.addEventListener('keydown', onKeydown, true);
    document.addEventListener('visibilitychange', onVisibilityChange);
    intro.addEventListener('animationend', onAnimationEnd);
    emit('shown', { configVersion: configVersion, preview: preview });
    if (analytics) record('shown', false);
    window.requestAnimationFrame(function () {
      var target = continueButton || skipButton || closeButton || intro;
      try { target.focus({ preventScroll: true }); } catch (error) { target.focus(); }
    });
    timer = window.setTimeout(function () { close('completed', false); }, duration);
  } catch (error) {
    failOpen('runtime_exception');
  }
}());
