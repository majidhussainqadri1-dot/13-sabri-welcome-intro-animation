'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(
  path.join(__dirname, '..', 'sabri-welcome-intro', 'assets', 'js', 'welcome-bootstrap.js'),
  'utf8'
);

function createClassList() {
  const values = new Set();
  return {
    add(...names) {
      names.forEach((name) => values.add(name));
    },
    remove(...names) {
      names.forEach((name) => values.delete(name));
    },
    contains(name) {
      return values.has(name);
    },
    values
  };
}

function runBootstrap(options = {}) {
  let cookieValue = options.cookie || '';
  const cookieWrites = [];
  const sessionMap = new Map(Object.entries(options.sessionStorage || {}));
  const localMap = new Map(Object.entries(options.localStorage || {}));
  const rootClassList = createClassList();
  const attributes = {
    'data-swi-preview': options.preview ? '1' : '0',
    'data-swi-cookie': 'sabri_welcome_seen'
  };

  const document = {
    currentScript: {
      getAttribute(name) {
        return attributes[name] || null;
      }
    },
    documentElement: {
      classList: rootClassList
    }
  };

  Object.defineProperty(document, 'cookie', {
    get() {
      if (options.throwCookieRead) {
        throw new Error('cookie read blocked');
      }
      return cookieValue;
    },
    set(value) {
      if (options.throwCookieWrite) {
        throw new Error('cookie write blocked');
      }
      cookieWrites.push(value);
      cookieValue = value;
    }
  });

  function storage(map, shouldThrow) {
    return {
      getItem(key) {
        if (shouldThrow) {
          throw new Error('storage blocked');
        }
        return map.has(key) ? map.get(key) : null;
      },
      setItem(key, value) {
        if (shouldThrow) {
          throw new Error('storage blocked');
        }
        map.set(key, String(value));
      }
    };
  }

  const now = options.now || 100000;
  const context = {
    Boolean,
    Date: { now: () => now },
    Number,
    String,
    document,
    window: {
      location: { protocol: options.protocol || 'https:' },
      sessionStorage: storage(sessionMap, options.throwSessionStorage),
      localStorage: storage(localMap, options.throwLocalStorage)
    }
  };

  vm.runInNewContext(source, context, { filename: 'welcome-bootstrap.js' });

  return {
    classes: rootClassList.values,
    cookieWrites,
    sessionMap,
    localMap
  };
}

test('returning visitor is suppressed before overlay rendering', () => {
  const result = runBootstrap({ cookie: 'sabri_welcome_seen=1; other=value' });
  assert.equal(result.classes.has('swi-intro-seen'), true);
  assert.equal(result.classes.has('swi-intro-pending'), false);
  assert.equal(result.cookieWrites.length, 0);
});

test('first accepted visit claims the session immediately', () => {
  const result = runBootstrap();
  assert.equal(result.classes.has('swi-intro-pending'), true);
  assert.equal(result.classes.has('swi-intro-seen'), false);
  assert.match(result.cookieWrites[0], /^sabri_welcome_seen=1;/);
  assert.equal(result.sessionMap.get('sabri_welcome_seen_session'), '1');
  assert.equal(result.localMap.get('sabri_welcome_seen_claim'), '100000');
});

test('authorized preview bypasses seen state without mutating session state', () => {
  const result = runBootstrap({ preview: true, cookie: 'sabri_welcome_seen=1' });
  assert.equal(result.classes.has('swi-intro-pending'), true);
  assert.equal(result.classes.has('swi-intro-seen'), false);
  assert.equal(result.cookieWrites.length, 0);
  assert.equal(result.sessionMap.size, 0);
  assert.equal(result.localMap.size, 0);
});

test('fresh cross-tab claim suppresses a duplicate intro', () => {
  const result = runBootstrap({
    now: 100000,
    localStorage: { sabri_welcome_seen_claim: '95000' }
  });
  assert.equal(result.classes.has('swi-intro-seen'), true);
  assert.equal(result.classes.has('swi-intro-pending'), false);
  assert.equal(result.cookieWrites.length, 0);
});

test('restricted cookie and storage modes still fail open without throwing', () => {
  const result = runBootstrap({
    throwCookieRead: true,
    throwCookieWrite: true,
    throwSessionStorage: true,
    throwLocalStorage: true
  });
  assert.equal(result.classes.has('swi-intro-pending'), true);
});
