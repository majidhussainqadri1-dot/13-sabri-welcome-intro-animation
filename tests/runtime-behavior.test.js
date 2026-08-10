'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');
const source = fs.readFileSync(path.join(__dirname, '..', 'sabri-welcome-intro', 'assets', 'js', 'welcome-intro.js'), 'utf8');

function classList(initial = []) {
  const values = new Set(initial);
  return { add(...v) { v.forEach((x) => values.add(x)); }, remove(...v) { v.forEach((x) => values.delete(x)); }, contains(v) { return values.has(v); }, values };
}
function storage(initial = {}, throws = false) {
  const values = new Map(Object.entries(initial));
  return { getItem(k) { if (throws) throw new Error('blocked'); return values.has(k) ? values.get(k) : null; }, setItem(k, v) { if (throws) throw new Error('blocked'); values.set(k, String(v)); }, values };
}
function element(tag, document) {
  const attrs = new Map(); const listeners = new Map();
  return {
    tagName: tag, nodeType: 1, parentElement: null, children: [], isConnected: true, classList: classList(), listeners,
    setAttribute(k, v) { attrs.set(k, String(v)); }, getAttribute(k) { return attrs.has(k) ? attrs.get(k) : null; }, hasAttribute(k) { return attrs.has(k); }, removeAttribute(k) { attrs.delete(k); },
    addEventListener(k, fn) { listeners.set(k, fn); }, removeEventListener(k, fn) { if (listeners.get(k) === fn) listeners.delete(k); },
    focus() { document.activeElement = this; this.focused = true; },
    remove() { this.isConnected = false; if (this.parentElement) this.parentElement.children = this.parentElement.children.filter((c) => c !== this); }
  };
}
function createRuntime(options = {}) {
  const docListeners = new Map(); const events = []; const timers = new Map(); const fetches = [];
  let timerId = 1; let cookie = options.cookie || '';
  const document = {
    activeElement: null, hidden: false,
    addEventListener(k, fn) { docListeners.set(k, fn); }, removeEventListener(k, fn) { if (docListeners.get(k) === fn) docListeners.delete(k); },
    dispatchEvent(e) { events.push(e); return true; }
  };
  const root = element('HTML', document); const body = element('BODY', document); root.children = [body]; body.parentElement = root; document.documentElement = root; document.body = body;
  const previous = element('A', document); const intro = element('ASIDE', document); const close = element('BUTTON', document); const cont = element('BUTTON', document); const skip = element('BUTTON', document);
  previous.parentElement = body; intro.parentElement = body; close.parentElement = cont.parentElement = skip.parentElement = intro; intro.children = [close, cont, skip]; body.children = [previous, intro]; document.activeElement = previous;
  intro.setAttribute('hidden', ''); intro.setAttribute('data-preview', options.preview ? '1' : '0'); intro.setAttribute('data-preview-state', options.previewState || 'default'); intro.setAttribute('data-frequency-days', String(options.frequency || 30)); intro.setAttribute('data-duration', '8000'); intro.setAttribute('data-reduced-duration', '900'); intro.setAttribute('data-config-version', '3'); intro.setAttribute('data-cookie-name', 'swi_seen_at_v1'); intro.setAttribute('data-session-key', 'swi_seen_session_v1'); intro.setAttribute('data-local-key', 'swi_seen_at_v1'); intro.setAttribute('data-claim-key', 'swi_claim_v1'); intro.setAttribute('data-rest-url', options.restUrl || ''); intro.setAttribute('data-rest-nonce', options.restNonce || ''); intro.setAttribute('data-event-nonce', options.eventNonce || ''); intro.setAttribute('data-analytics', options.analytics ? '1' : '0'); intro.setAttribute('data-account-authoritative', options.accountAuthoritative ? '1' : '0'); intro.setAttribute('data-user-id', String(options.userId || 0));
  intro.querySelector = (q) => q === '[data-swi-close]' ? close : q === '[data-swi-continue]' ? cont : q === '[data-swi-skip]' ? skip : null;
  document.getElementById = (id) => id === 'swi-intro' ? intro : null;
  Object.defineProperty(document, 'cookie', { get() { return cookie; }, set(v) { cookie = v; } });
  const localStorage = storage(options.local || {}, options.storageThrows); const sessionStorage = storage(options.session || {}, options.storageThrows);
  const windowListeners = new Map();
  const window = {
    location: { protocol: 'https:' }, localStorage, sessionStorage,
    matchMedia: () => ({ matches: Boolean(options.reduced) }),
    getComputedStyle: () => ({ getPropertyValue: () => options.cssMissing ? '' : '1' }),
    requestAnimationFrame(fn) { fn(); },
    setTimeout(fn, delay) { const id = timerId++; timers.set(id, { fn, delay }); return id; }, clearTimeout(id) { timers.delete(id); },
    addEventListener(k, fn) { windowListeners.set(k, fn); }, removeEventListener(k, fn) { if (windowListeners.get(k) === fn) windowListeners.delete(k); },
    fetch(url, init) { fetches.push({ url, init }); return Promise.resolve({ ok: true }); },
    crypto: { getRandomValues(arr) { for (let i = 0; i < arr.length; i++) arr[i] = i + 1; return arr; } }
  };
  function CustomEvent(name, init) { this.type = name; this.detail = init.detail; }
  vm.runInNewContext(source, { Array, Boolean, CustomEvent, Date, JSON, Math, Number, String, Uint32Array, decodeURIComponent, document, encodeURIComponent, parseInt, window }, { filename: 'welcome-intro.js' });
  return { document, root, body, previous, intro, close, cont, skip, docListeners, events, timers, fetches, localStorage, sessionStorage, cookie: () => cookie };
}

test('first eligible visit reveals only after CSS readiness and claims the session', () => {
  const r = createRuntime();
  assert.equal(r.intro.hasAttribute('hidden'), false); assert.equal(r.body.classList.contains('swi-intro-active'), true); assert.equal(r.previous.getAttribute('inert'), ''); assert.equal(r.document.activeElement, r.cont); assert.equal(r.sessionStorage.values.get('swi_seen_session_v1'), '1'); assert.equal(r.events[0].type, 'swi:shown');
});
test('same session never renders again', () => { const r = createRuntime({ session: { swi_seen_session_v1: '1' } }); assert.equal(r.intro.isConnected, false); assert.equal(r.body.classList.contains('swi-intro-active'), false); });
test('timestamp suppresses for at least 30 days', () => { const r = createRuntime({ local: { swi_seen_at_v1: String(Date.now() - 29 * 86400000) } }); assert.equal(r.intro.isConnected, false); });
test('expired timestamp permits a new eligible visit', () => { const r = createRuntime({ local: { swi_seen_at_v1: String(Date.now() - 31 * 86400000) } }); assert.equal(r.intro.isConnected, true); assert.equal(r.intro.hasAttribute('hidden'), false); });
test('CSS or storage failure cannot leave a blocking overlay', () => { const r = createRuntime({ cssMissing: true, storageThrows: true }); assert.equal(r.intro.isConnected, false); assert.equal(r.body.classList.contains('swi-intro-active'), false); assert.equal(r.events[0].type, 'swi:error'); });
test('Continue persists timestamp, restores background and focus', () => { const r = createRuntime({ accountAuthoritative: false, userId: 0 }); r.cont.listeners.get('click')(); const closeTimer = [...r.timers.values()].find((x) => x.delay === 220); assert.ok(closeTimer); closeTimer.fn(); assert.equal(r.intro.isConnected, false); assert.match(r.cookie(), /^swi_seen_at_v1=/); assert.ok(Number(r.localStorage.values.get('swi_seen_at_v1')) > 0); assert.equal(r.previous.hasAttribute('inert'), false); assert.equal(r.document.activeElement, r.previous); assert.equal(r.events.at(-1).type, 'swi:completed'); });
test('Escape performs the skip journey', () => { const r = createRuntime(); let prevented = false; r.docListeners.get('keydown')({ key: 'Escape', preventDefault() { prevented = true; } }); assert.equal(prevented, true); const closeTimer = [...r.timers.values()].find((x) => x.delay === 220); closeTimer.fn(); assert.equal(r.events.at(-1).type, 'swi:skipped'); });
test('focus trap wraps in both directions', () => { const r = createRuntime(); const key = r.docListeners.get('keydown'); r.skip.focus(); key({ key: 'Tab', shiftKey: false, preventDefault() {} }); assert.equal(r.document.activeElement, r.close); r.close.focus(); key({ key: 'Tab', shiftKey: true, preventDefault() {} }); assert.equal(r.document.activeElement, r.skip); });
test('reduced motion uses short static path', () => { const r = createRuntime({ reduced: true }); assert.equal(r.intro.classList.contains('swi-variant-static'), true); const fallback = [...r.timers.values()].find((x) => x.delay === 900); assert.ok(fallback); });
test('preview never changes public persistence', () => { const r = createRuntime({ preview: true }); r.cont.listeners.get('click')(); const t = [...r.timers.values()].find((x) => x.delay === 220); t.fn(); assert.equal(r.cookie(), ''); assert.equal(r.localStorage.values.has('swi_seen_at_v1'), false); });
test('authenticated dismissal uses nonce and idempotent request body', () => { const r = createRuntime({ restUrl: '/wp-json/sabri-welcome-intro/v1', restNonce: 'nonce', accountAuthoritative: true, userId: 5 }); r.skip.listeners.get('click')(); const request = r.fetches.find((entry) => entry.url.endsWith('/dismiss')); assert.ok(request); assert.equal(request.init.headers['X-WP-Nonce'], 'nonce'); const body = JSON.parse(request.init.body); assert.equal(body.event, 'skipped'); assert.equal(body.config_version, 3); assert.ok(body.idempotency_key); });
test('public analytics is opt-in and uses separate event nonce', () => { const r = createRuntime({ restUrl: '/wp-json/sabri-welcome-intro/v1', analytics: true, eventNonce: 'event' }); const request = r.fetches.find((entry) => entry.url.endsWith('/event')); assert.ok(request); assert.equal(request.init.headers['X-SWI-Nonce'], 'event'); });
test('disabled/error preview is a visible fail-open test and does not mutate storage', () => { const r = createRuntime({ preview: true, previewState: 'error' }); assert.equal(r.intro.isConnected, false); assert.equal(r.events[0].type, 'swi:error'); assert.equal(r.localStorage.values.size, 0); });
