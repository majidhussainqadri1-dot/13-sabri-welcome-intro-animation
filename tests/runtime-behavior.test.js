'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(
  path.join(__dirname, '..', 'sabri-welcome-intro', 'assets', 'js', 'welcome-intro.js'),
  'utf8'
);

function classList(initial = []) {
  const values = new Set(initial);
  return {
    add(...names) { names.forEach((name) => values.add(name)); },
    remove(...names) { names.forEach((name) => values.delete(name)); },
    contains(name) { return values.has(name); },
    values
  };
}

function element(tagName, documentRef) {
  const attributes = new Map();
  const listeners = new Map();
  return {
    tagName,
    nodeType: 1,
    classList: classList(),
    children: [],
    parentElement: null,
    isConnected: true,
    attributes,
    listeners,
    setAttribute(name, value) { attributes.set(name, String(value)); },
    getAttribute(name) { return attributes.has(name) ? attributes.get(name) : null; },
    hasAttribute(name) { return attributes.has(name); },
    removeAttribute(name) { attributes.delete(name); },
    addEventListener(name, callback) { listeners.set(name, callback); },
    removeEventListener(name, callback) {
      if (listeners.get(name) === callback) listeners.delete(name);
    },
    focus() { documentRef.activeElement = this; this.focused = true; },
    remove() {
      this.isConnected = false;
      if (this.parentElement) {
        this.parentElement.children = this.parentElement.children.filter((child) => child !== this);
      }
    }
  };
}

function createRuntime(options = {}) {
  const documentListeners = new Map();
  const timers = new Map();
  let nextTimer = 1;
  let cookieValue = '';

  const document = {
    activeElement: null,
    addEventListener(name, callback) { documentListeners.set(name, callback); },
    removeEventListener(name, callback) {
      if (documentListeners.get(name) === callback) documentListeners.delete(name);
    }
  };

  const root = element('HTML', document);
  root.classList = classList(options.pending === false ? [] : ['swi-intro-pending']);
  document.documentElement = root;

  const body = element('BODY', document);
  body.classList = classList();
  body.parentElement = root;
  root.children = [body];
  document.body = body;

  const previous = element('A', document);
  previous.parentElement = body;
  const intro = element('ASIDE', document);
  intro.parentElement = body;
  const skip = element('BUTTON', document);
  skip.parentElement = intro;
  intro.children = [skip];
  body.children = [previous, intro];
  document.activeElement = previous;

  intro.setAttribute('data-cookie-name', 'sabri_welcome_seen');
  intro.setAttribute('data-duration', '8000');
  intro.setAttribute('data-reduced-duration', '1200');
  intro.setAttribute('data-preview', options.preview ? '1' : '0');
  intro.querySelector = (selector) => selector === '[data-swi-skip]' ? skip : null;

  document.getElementById = (id) => id === 'swi-intro' ? intro : null;
  Object.defineProperty(document, 'cookie', {
    get() { return cookieValue; },
    set(value) { cookieValue = value; }
  });

  const window = {
    location: { protocol: 'https:' },
    sessionStorage: { setItem() {} },
    matchMedia: () => ({ matches: Boolean(options.reducedMotion) }),
    getComputedStyle: () => options.cssCompleted
      ? ({ visibility: 'hidden', pointerEvents: 'none', opacity: '0' })
      : ({ visibility: 'visible', pointerEvents: 'auto', opacity: '1' }),
    requestAnimationFrame(callback) { callback(); },
    setTimeout(callback, delay) {
      const id = nextTimer++;
      timers.set(id, { callback, delay });
      return id;
    },
    clearTimeout(id) { timers.delete(id); }
  };

  vm.runInNewContext(source, { Boolean, document, window }, { filename: 'welcome-intro.js' });

  return { document, documentListeners, timers, root, body, previous, intro, skip, cookie: () => cookieValue };
}

test('runtime applies modal focus and background isolation', () => {
  const runtime = createRuntime();
  assert.equal(runtime.body.classList.contains('swi-intro-active'), true);
  assert.equal(runtime.previous.getAttribute('inert'), '');
  assert.equal(runtime.previous.getAttribute('aria-hidden'), 'true');
  assert.equal(runtime.document.activeElement, runtime.skip);
  assert.equal(runtime.documentListeners.has('keydown'), true);
  assert.equal(runtime.intro.listeners.has('animationend'), true);
  assert.match(runtime.cookie(), /^sabri_welcome_seen=1;/);
});

test('Tab is contained and Escape performs complete cleanup and focus restoration', () => {
  const runtime = createRuntime();
  let prevented = false;
  const keydown = runtime.documentListeners.get('keydown');

  keydown({ key: 'Tab', preventDefault() { prevented = true; } });
  assert.equal(prevented, true);
  assert.equal(runtime.document.activeElement, runtime.skip);

  keydown({ key: 'Escape', preventDefault() { prevented = true; } });
  const skipTimer = [...runtime.timers.values()].find((timer) => timer.delay === 260);
  assert.ok(skipTimer, 'skip cleanup timer was not scheduled');
  skipTimer.callback();

  assert.equal(runtime.intro.isConnected, false);
  assert.equal(runtime.body.classList.contains('swi-intro-active'), false);
  assert.equal(runtime.root.classList.contains('swi-intro-pending'), false);
  assert.equal(runtime.root.classList.contains('swi-intro-seen'), true);
  assert.equal(runtime.previous.hasAttribute('inert'), false);
  assert.equal(runtime.previous.hasAttribute('aria-hidden'), false);
  assert.equal(runtime.document.activeElement, runtime.previous);
  assert.equal(runtime.documentListeners.has('keydown'), false);
});

test('animationend is the authoritative normal completion signal', () => {
  const runtime = createRuntime();
  const animationEnd = runtime.intro.listeners.get('animationend');
  animationEnd({ target: runtime.intro, animationName: 'unrelated' });
  assert.equal(runtime.intro.isConnected, true);
  animationEnd({ target: runtime.intro, animationName: 'swi-overlay-exit' });
  assert.equal(runtime.intro.isConnected, false);
});

test('late runtime does not re-block the page after the CSS fail-safe completed', () => {
  const runtime = createRuntime({ cssCompleted: true });
  assert.equal(runtime.intro.isConnected, false);
  assert.equal(runtime.body.classList.contains('swi-intro-active'), false);
  assert.equal(runtime.previous.hasAttribute('inert'), false);
});

test('runtime fails open when bootstrap did not authorize display', () => {
  const runtime = createRuntime({ pending: false });
  assert.equal(runtime.intro.isConnected, false);
  assert.equal(runtime.body.classList.contains('swi-intro-active'), false);
});
