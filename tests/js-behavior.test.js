'use strict';

const fs = require('fs');
const vm = require('vm');
const path = require('path');

const source = fs.readFileSync(
  path.join(__dirname, '..', 'sabri-welcome-intro-13', 'assets', 'js', 'welcome-intro.js'),
  'utf8'
);

function makeStorage(seed = {}) {
  const data = new Map(Object.entries(seed));
  return {
    getItem(key) { return data.has(key) ? data.get(key) : null; },
    setItem(key, value) { data.set(key, String(value)); },
    data,
  };
}

function runCase(options = {}) {
  const sessionStorage = makeStorage(options.session || {});
  const localStorage = makeStorage(options.local || {});
  const classes = new Set();
  const styles = {};
  const events = {};
  const timeouts = [];
  const skip = { addEventListener(type, fn) { events['skip:' + type] = fn; } };
  const root = {
    hidden: true,
    getAttribute(name) { return name === 'data-swi-version' ? String(options.version || 1) : ''; },
    style: { setProperty(name, value) { styles[name] = value; } },
    classList: {
      add(...names) { names.forEach(n => classes.add(n)); },
      remove(...names) { names.forEach(n => classes.delete(n)); },
    },
    querySelector(sel) { return sel === '.swi-intro__skip' ? skip : null; },
  };
  const document = {
    getElementById() { return root; },
    addEventListener(type, fn) { events['document:' + type] = fn; },
  };
  const window = {
    SWI_INTRO: {
      version: options.version || 1,
      durationMs: options.duration || 3200,
      recurrenceDays: options.days || 30,
      analyticsEnabled: false,
      preview: !!options.preview,
      forceReducedMotion: !!options.forceReduced,
    },
    sessionStorage,
    localStorage,
    matchMedia() { return { matches: !!options.reduced }; },
    dispatchEvent() {},
    setTimeout(fn, delay) { timeouts.push({ fn, delay }); return timeouts.length; },
    clearTimeout() {},
  };
  const context = {
    window,
    document,
    CustomEvent: function CustomEvent() {},
    URLSearchParams,
    Number,
    Date,
    fetch() { throw new Error('analytics fetch must not run when disabled'); },
  };
  vm.createContext(context);
  vm.runInContext(source, context);
  return { root, sessionStorage, localStorage, events, timeouts, styles, classes };
}

function assert(condition, message) {
  if (!condition) {
    console.error('FAIL:', message);
    process.exitCode = 1;
  }
}

// First eligible display.
{
  const c = runCase();
  assert(c.root.hidden === false, 'first eligible display must become visible');
  assert(c.sessionStorage.data.get('swi.seen.v1') === '1', 'session seen flag must be written');
  assert(c.styles['--swi-duration'] === '3200ms', 'configured duration must be applied');
}

// Existing >=30-day dismissal suppresses the intro.
{
  const until = String(Date.now() + 86400000);
  const c = runCase({ local: { 'swi.dismissed.until': until } });
  assert(c.root.hidden === true, 'future dismissal timestamp must suppress display');
  assert(c.timeouts.length === 0, 'suppressed display must not schedule completion');
}

// Skip writes durable dismissal.
{
  const c = runCase();
  assert(typeof c.events['skip:click'] === 'function', 'skip handler must exist');
  c.events['skip:click']();
  const until = Number(c.localStorage.data.get('swi.dismissed.until') || '0');
  assert(until > Date.now() + (29 * 86400000), 'skip must suppress for at least 30 days');
}

// Escape invokes skip/dismiss semantics.
{
  const c = runCase();
  assert(typeof c.events['document:keydown'] === 'function', 'Escape handler must exist');
  c.events['document:keydown']({ key: 'Escape' });
  assert(Number(c.localStorage.data.get('swi.dismissed.until') || '0') > Date.now(), 'Escape must dismiss');
}

// Reduced motion removes animation duration and bounds completion delay.
{
  const c = runCase({ reduced: true, duration: 9000 });
  assert(c.styles['--swi-duration'] === '0ms', 'reduced motion must disable motion duration');
  assert(c.timeouts.some(t => t.delay <= 800), 'reduced-motion completion must be bounded');
}

if (!process.exitCode) {
  console.log('File 13 JavaScript behavior suite: PASS');
}
