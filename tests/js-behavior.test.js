'use strict';

const fs = require('fs');
const vm = require('vm');
const path = require('path');

const source = fs.readFileSync(
  path.join(__dirname, '..', 'sabri-welcome-intro-13', 'assets', 'js', 'welcome-intro.js'),
  'utf8'
);

function runCase(options = {}) {
  const classes = new Set();
  const styles = {};
  const events = {};
  const timeouts = [];
  const skip = { addEventListener(type, fn) { events['skip:' + type] = fn; } };
  const root = {
    hidden: true,
    style: { setProperty(name, value) { styles[name] = value; } },
    classList: {
      add(...names) { names.forEach(n => classes.add(n)); },
      remove(...names) { names.forEach(n => classes.delete(n)); },
    },
    querySelector(sel) { return sel === '.swi-intro__skip' ? skip : null; },
  };
  const document = {
    getElementById(id) { return id === 'swi-welcome-intro-preview' ? root : null; },
    addEventListener(type, fn) { events['document:' + type] = fn; },
  };
  const storage = {
    getItem() { throw new Error('legacy preview must not read storage'); },
    setItem() { throw new Error('legacy preview must not write storage'); },
  };
  const window = {
    SWI_INTRO: {
      preview: !!options.preview,
      durationMs: options.duration || 3200,
      forceReducedMotion: !!options.forceReduced,
    },
    sessionStorage: storage,
    localStorage: storage,
    matchMedia() { return { matches: !!options.reduced }; },
    setTimeout(fn, delay) { timeouts.push({ fn, delay }); return timeouts.length; },
    clearTimeout() {},
  };
  const context = { window, document, Number };
  vm.createContext(context);
  vm.runInContext(source, context);
  return { root, events, timeouts, styles, classes };
}

function assert(condition, message) {
  if (!condition) {
    console.error('FAIL:', message);
    process.exitCode = 1;
  }
}

// Public execution is permanently inert: File 13 is compatibility-only.
{
  const c = runCase({ preview: false });
  assert(c.root.hidden === true, 'non-preview execution must remain hidden');
  assert(c.timeouts.length === 0, 'non-preview execution must schedule no work');
}

// Authenticated legacy preview remains inspectable without persistence.
{
  const c = runCase({ preview: true });
  assert(c.root.hidden === false, 'legacy preview must become visible');
  assert(c.styles['--swi-duration'] === '3200ms', 'preview duration must be applied');
  assert(typeof c.events['skip:click'] === 'function', 'preview close handler must exist');
  assert(!c.classes.has('swi-intro--reduced'), 'normal preview must not force reduced-motion CSS');
}

// Escape closes only the non-persistent preview.
{
  const c = runCase({ preview: true });
  c.events['document:keydown']({ key: 'Escape' });
  assert(c.timeouts.some(t => t.delay === 170), 'Escape must schedule preview hide');
}

// Reduced motion removes animation duration and bounds preview completion.
{
  const c = runCase({ preview: true, reduced: true, duration: 9000 });
  assert(c.styles['--swi-duration'] === '0ms', 'reduced motion must disable motion duration');
  assert(c.classes.has('swi-intro--reduced'), 'OS reduced-motion preview must enable scoped CSS override');
  assert(c.timeouts.some(t => t.delay <= 800), 'reduced-motion completion must be bounded');
}

// A preview's forced reduced state must override OS preference, including CSS
// entrance/exit animations, not only the progress-line duration.
{
  const c = runCase({ preview: true, reduced: false, forceReduced: true, duration: 9000 });
  assert(c.root.hidden === false, 'forced reduced-motion preview must remain inspectable');
  assert(c.classes.has('swi-intro--reduced'), 'forced reduced-motion must enable scoped CSS override');
  assert(c.styles['--swi-duration'] === '0ms', 'forced reduced-motion must remove progress animation');
  assert(c.timeouts.some(t => t.delay <= 800), 'forced reduced-motion must bound preview completion');
  c.events['document:keydown']({ key: 'Escape' });
  assert(c.timeouts.some(t => t.delay === 0), 'forced reduced-motion must close without exit delay');
}

if (!process.exitCode) {
  console.log('File 13 compatibility-preview behavior suite: PASS');
}
