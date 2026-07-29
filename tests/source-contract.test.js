'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');

const root = path.join(__dirname, '..');
function read(relativePath) {
  return fs.readFileSync(path.join(root, relativePath), 'utf8');
}

const plugin = read('sabri-welcome-intro/sabri-welcome-intro.php');
const renderer = read('sabri-welcome-intro/includes/class-swi-renderer.php');
const admin = read('sabri-welcome-intro/includes/class-swi-admin.php');
const bootstrap = read('sabri-welcome-intro/assets/js/welcome-bootstrap.js');
const runtime = read('sabri-welcome-intro/assets/js/welcome-intro.js');
const css = read('sabri-welcome-intro/assets/css/welcome-intro.css');
const svg = read('sabri-welcome-intro/assets/images/sabri-sh-logo.svg');
const readme = read('sabri-welcome-intro/readme.txt');

test('release identity is normalized', () => {
  assert.match(plugin, /Version:\s*0\.2\.0/);
  assert.match(plugin, /SWI_VERSION', '0\.2\.0'/);
  assert.match(plugin, /Dr\. Allamah Majid Hussain Sabri Muhaddith Mursheed/);
  assert.match(readme, /Stable tag:\s*0\.2\.0/);
});

test('preview is capability and nonce protected on the server', () => {
  assert.match(admin, /wp_nonce_url\s*\(/);
  assert.match(renderer, /current_user_can\( 'manage_options' \)/);
  assert.match(renderer, /wp_verify_nonce\( \$nonce, 'swi_preview' \)/);
  assert.match(renderer, /DONOTCACHEPAGE/);
  assert.match(renderer, /X-Robots-Tag: noindex, noarchive/);
  assert.match(renderer, /redirect_unauthorized_preview/);
  assert.doesNotMatch(bootstrap, /URLSearchParams|window\.location\.search/);
  assert.doesNotMatch(runtime, /URLSearchParams|window\.location\.search/);
});

test('overlay is hidden by default and bounded by a CSS fail-safe', () => {
  assert.match(css, /\.swi-intro\s*\{[\s\S]*?display:\s*none;/);
  assert.match(css, /html\.swi-intro-pending \.swi-intro\s*\{[\s\S]*?swi-overlay-exit/);
  assert.match(css, /to\s*\{[^}]*pointer-events:\s*none;[^}]*visibility:\s*hidden;/);
  assert.match(css, /prefers-reduced-motion:[\s\S]*1200ms/);
  assert.match(renderer, /data-no-optimize/);
  assert.match(renderer, /missing bootstrap fails open/i);
});

test('session state is claimed before the intro completes', () => {
  assert.match(bootstrap, /claimSession\(now\)/);
  assert.match(bootstrap, /sessionStorage\.setItem/);
  assert.match(bootstrap, /localStorage\.setItem/);
  assert.match(bootstrap, /document\.cookie = cookieName \+ '=1;/);
});

test('dialog lifecycle contains focus and restores background state', () => {
  assert.match(runtime, /setBackgroundInert/);
  assert.match(runtime, /setAttribute\('inert'/);
  assert.match(runtime, /setAttribute\('aria-hidden', 'true'\)/);
  assert.match(runtime, /restoreBackground/);
  assert.match(runtime, /restoreFocus/);
  assert.match(runtime, /event\.key !== 'Tab'/);
  assert.match(runtime, /event\.key === 'Escape'/);
  assert.match(runtime, /removeEventListener\('keydown', onKeydown, true\)/);
  assert.match(runtime, /animationName === 'swi-overlay-exit'/);
});

test('touch target and vector logo meet the approved contract', () => {
  assert.match(css, /min-height:\s*44px/);
  assert.match(css, /min-width:\s*44px/);
  assert.match(svg, /<circle[^>]+fill="#FF8A1F"[^>]+stroke="#102A43"/);
  assert.match(svg, /M256 126v260/);
  assert.doesNotMatch(svg, /<text\b/);
  assert.doesNotMatch(svg, /<rect\b/);
});
