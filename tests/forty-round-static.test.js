'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');

const root = path.join(__dirname, '..');
const read = (relative) => fs.readFileSync(path.join(root, relative), 'utf8');
const js = read('sabri-welcome-intro/assets/js/welcome-intro.js');
const css = read('sabri-welcome-intro/assets/css/welcome-intro.css');
const renderer = read('sabri-welcome-intro/includes/class-swi-renderer.php');
const rest = read('sabri-welcome-intro/includes/class-swi-rest.php');
const activator = read('sabri-welcome-intro/includes/class-swi-activator.php');
const systemCheck = read('sabri-welcome-intro/includes/class-swi-system-check.php');
const config = read('sabri-welcome-intro/includes/class-swi-config.php');
const analytics = read('sabri-welcome-intro/includes/class-swi-analytics.php');

test('account-level timestamp is authoritative while same-session suppression remains', () => {
  assert.match(renderer, /data-account-authoritative/);
  assert.match(js, /if \(accountAuthoritative\) return false/);
  assert.match(js, /safeGet\(window\.sessionStorage, sessionKey\)/);
});

test('authenticated REST requests carry the WordPress nonce for every event path', () => {
  assert.match(js, /if \(restNonce\) headers\['X-WP-Nonce'\] = restNonce/);
});

test('runtime exception path is fail-open and restores page state', () => {
  assert.match(js, /function failOpen\(reason\)/);
  assert.match(js, /body\.classList\.remove\('swi-intro-active'\)/);
  assert.match(js, /restoreBackground\(\)/);
  assert.match(js, /try \{[\s\S]*previewState[\s\S]*\} catch \(error\) \{[\s\S]*failOpen\('runtime_exception'\)/);
});

test('public analytics nonce is only exposed when analytics is enabled', () => {
  assert.match(renderer, /analytics_enabled[^\n]+wp_create_nonce\( 'swi_public_event' \)/);
});

test('mixed-language brand copy uses automatic direction and RTL-safe controls', () => {
  assert.match(renderer, /class="swi-brand-name"[^\n]*dir="auto"/);
  assert.match(renderer, /class="swi-brand-claim"[^\n]*dir="auto"/);
  assert.doesNotMatch(css, /\.swi-brand-name[^}]*direction:\s*ltr/s);
  assert.match(css, /\[dir="rtl"\] \.swi-controls/);
  assert.match(css, /\[dir="rtl"\] \.swi-button-primary svg/);
});

test('closing transition does not hide the overlay before animation completion', () => {
  const closing = css.match(/\.swi-is-closing\s*\{([^}]*)\}/s);
  assert.ok(closing);
  assert.doesNotMatch(closing[1], /visibility:\s*hidden/);
});

test('REST mutation contracts require idempotency and rate-limit aggregate events', () => {
  assert.match(rest, /swi_missing_idempotency/);
  assert.match(rest, /rate_limited\( 'event', 'global', 300/);
});

test('configuration governance preserves recorded metadata and validates site-local time', () => {
  assert.match(config, /config_version[^\n]+updated_at[^\n]+updated_by/);
  assert.match(config, /wp_timezone\(\)/);
  assert.match(config, /DateTimeImmutable::getLastErrors\(\)/);
});

test('analytics success is checked against the persisted aggregate', () => {
  assert.match(analytics, /get_option\( SWI_Config::OPTION_METRICS/);
  assert.match(analytics, /\$persisted === \$metrics/);
});

test('deactivation removes the preview rewrite before flushing', () => {
  assert.match(activator, /extra_rules_top/);
  assert.match(activator, /welcome-intro-preview/);
  assert.match(activator, /flush_rewrite_rules/);
});

test('system check distinguishes local callbacks from external owner contracts', () => {
  assert.match(systemCheck, /shell_registry_callback/);
  assert.match(systemCheck, /shell_contract_version/);
  assert.match(systemCheck, /'fallback'/);
});
