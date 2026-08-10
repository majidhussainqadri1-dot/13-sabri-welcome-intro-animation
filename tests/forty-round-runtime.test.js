'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'sabri-welcome-intro', 'assets', 'js', 'welcome-intro.js'), 'utf8');

function makeClassList(throwOnAdd = false) {
  const values = new Set();
  return {
    add(...items) { if (throwOnAdd) throw new Error('synthetic activation failure'); items.forEach((item) => values.add(item)); },
    remove(...items) { items.forEach((item) => values.delete(item)); },
    contains(item) { return values.has(item); }
  };
}
function makeStorage(initial = {}) {
  const values = new Map(Object.entries(initial));
  return { getItem(key) { return values.has(key) ? values.get(key) : null; }, setItem(key, value) { values.set(key, String(value)); }, values };
}
function makeElement(tagName, document, throwOnAdd = false) {
  const attrs = new Map(); const listeners = new Map();
  return {
    tagName, nodeType: 1, parentElement: null, children: [], isConnected: true,
    classList: makeClassList(throwOnAdd), listeners,
    setAttribute(key, value) { attrs.set(key, String(value)); },
    getAttribute(key) { return attrs.has(key) ? attrs.get(key) : null; },
    hasAttribute(key) { return attrs.has(key); },
    removeAttribute(key) { attrs.delete(key); },
    addEventListener(key, fn) { listeners.set(key, fn); },
    removeEventListener(key, fn) { if (listeners.get(key) === fn) listeners.delete(key); },
    focus() { document.activeElement = this; },
    remove() { this.isConnected = false; if (this.parentElement) this.parentElement.children = this.parentElement.children.filter((child) => child !== this); }
  };
}
function runtime(options = {}) {
  const documentListeners = new Map(); const events = []; const fetches = []; const timers = new Map(); const windowListeners = new Map(); let nextTimer = 1; let cookie = '';
  const document = {
    activeElement: null,
    addEventListener(key, fn) { documentListeners.set(key, fn); },
    removeEventListener(key, fn) { if (documentListeners.get(key) === fn) documentListeners.delete(key); },
    dispatchEvent(event) { events.push(event); return true; }
  };
  const html = makeElement('HTML', document); const body = makeElement('BODY', document, Boolean(options.throwOnBodyAdd));
  html.children = [body]; body.parentElement = html; document.documentElement = html; document.body = body;
  const previous = makeElement('A', document); const intro = makeElement('ASIDE', document); const close = makeElement('BUTTON', document); const cont = makeElement('BUTTON', document); const skip = makeElement('BUTTON', document);
  previous.parentElement = body; intro.parentElement = body; close.parentElement = cont.parentElement = skip.parentElement = intro; intro.children = [close, cont, skip]; body.children = [previous, intro]; document.activeElement = previous;
  const attributes = {
    hidden: '', 'data-preview': '0', 'data-preview-state': 'default', 'data-frequency-days': '30', 'data-duration': '8000', 'data-reduced-duration': '900',
    'data-config-version': '3', 'data-cookie-name': 'swi_seen_at_v1', 'data-session-key': 'swi_seen_session_v1', 'data-local-key': 'swi_seen_at_v1',
    'data-claim-key': 'swi_claim_v1', 'data-rest-url': options.restUrl || '', 'data-rest-nonce': options.restNonce || '', 'data-event-nonce': options.eventNonce || '',
    'data-analytics': options.analytics ? '1' : '0', 'data-account-authoritative': options.accountAuthoritative ? '1' : '0', 'data-user-id': String(options.userId || 0)
  };
  Object.entries(attributes).forEach(([key, value]) => intro.setAttribute(key, value));
  intro.querySelector = (query) => query === '[data-swi-close]' ? close : query === '[data-swi-continue]' ? cont : query === '[data-swi-skip]' ? skip : null;
  document.getElementById = (id) => id === 'swi-intro' ? intro : null;
  Object.defineProperty(document, 'cookie', { get() { return cookie; }, set(value) { cookie = value; } });
  const localStorage = makeStorage(options.local); const sessionStorage = makeStorage(options.session);
  const window = {
    location: { protocol: 'https:' }, localStorage, sessionStorage,
    matchMedia() { return { matches: false }; }, getComputedStyle() { return { getPropertyValue() { return '1'; } }; }, requestAnimationFrame(fn) { fn(); },
    setTimeout(fn, delay) { const id = nextTimer++; timers.set(id, { fn, delay }); return id; }, clearTimeout(id) { timers.delete(id); },
    addEventListener(key, fn) { windowListeners.set(key, fn); }, removeEventListener(key, fn) { if (windowListeners.get(key) === fn) windowListeners.delete(key); },
    fetch(url, init) { fetches.push({ url, init }); return Promise.resolve({ ok: true }); },
    crypto: { getRandomValues(array) { for (let i = 0; i < array.length; i += 1) array[i] = i + 1; return array; } }
  };
  function CustomEvent(name, init) { this.type = name; this.detail = init.detail; }
  vm.runInNewContext(source, { Array, Boolean, CustomEvent, Date, JSON, Math, Number, String, Uint32Array, decodeURIComponent, document, encodeURIComponent, parseInt, window }, { filename: 'welcome-intro.js' });
  return { intro, body, previous, localStorage, sessionStorage, fetches, events };
}

test('account-authoritative eligible response ignores stale guest timestamp', () => {
  const r = runtime({ accountAuthoritative: true, userId: 5, local: { swi_seen_at_v1: String(Date.now()) } });
  assert.equal(r.intro.isConnected, true);
  assert.equal(r.intro.hasAttribute('hidden'), false);
  assert.equal(r.sessionStorage.values.get('swi_seen_session_v1'), '1');
});

test('same-session claim still suppresses account-authoritative response', () => {
  const r = runtime({ accountAuthoritative: true, userId: 5, session: { swi_seen_session_v1: '1' } });
  assert.equal(r.intro.isConnected, false);
});

test('unexpected activation exception restores an unblocked page', () => {
  const r = runtime({ throwOnBodyAdd: true });
  assert.equal(r.intro.isConnected, false);
  assert.equal(r.body.classList.contains('swi-intro-active'), false);
  assert.equal(r.previous.hasAttribute('inert'), false);
  assert.equal(r.events.at(-1).type, 'swi:error');
});

test('logged-in aggregate event sends both REST and event nonces', () => {
  const r = runtime({ restUrl: '/wp-json/sabri-welcome-intro/v1', restNonce: 'rest', eventNonce: 'event', analytics: true, accountAuthoritative: true, userId: 5 });
  const eventRequests = r.fetches.filter((request) => request.url.endsWith('/event'));
  assert.ok(eventRequests.length >= 1);
  assert.equal(eventRequests[0].init.headers['X-WP-Nonce'], 'rest');
  assert.equal(eventRequests[0].init.headers['X-SWI-Nonce'], 'event');
});
