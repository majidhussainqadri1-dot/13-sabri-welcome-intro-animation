(function () {
  'use strict';

  var script = document.currentScript;
  var root = document.documentElement;
  var preview = Boolean(script && script.getAttribute('data-swi-preview') === '1');
  var cookieName = (script && script.getAttribute('data-swi-cookie')) || 'sabri_welcome_seen';
  var sessionKey = cookieName + '_session';
  var claimKey = cookieName + '_claim';
  var claimLifetime = 15000;

  function hasSessionCookie() {
    try {
      return document.cookie.split(';').some(function (item) {
        return item.trim().indexOf(cookieName + '=') === 0;
      });
    } catch (error) {
      return false;
    }
  }

  function readSessionFallback() {
    try {
      return window.sessionStorage.getItem(sessionKey) === '1';
    } catch (error) {
      return false;
    }
  }

  function hasFreshCrossTabClaim(now) {
    try {
      var claimedAt = parseInt(window.localStorage.getItem(claimKey), 10);
      return Number.isFinite(claimedAt) && now - claimedAt >= 0 && now - claimedAt < claimLifetime;
    } catch (error) {
      return false;
    }
  }

  function claimSession(now) {
    var secure = window.location.protocol === 'https:' ? '; Secure' : '';

    try {
      document.cookie = cookieName + '=1; Path=/; SameSite=Lax' + secure;
    } catch (error) {
      // Session storage remains the fail-safe when cookies are restricted.
    }

    try {
      window.sessionStorage.setItem(sessionKey, '1');
    } catch (error) {
      // The CSS fail-open behavior still prevents a blocking overlay.
    }

    try {
      window.localStorage.setItem(claimKey, String(now));
    } catch (error) {
      // Cross-tab coordination is best-effort only.
    }
  }

  var now = Date.now();
  var alreadySeen = hasSessionCookie() || readSessionFallback();
  var anotherTabClaimed = !alreadySeen && hasFreshCrossTabClaim(now);

  root.classList.remove('swi-intro-pending', 'swi-intro-seen');

  if (!preview && (alreadySeen || anotherTabClaimed)) {
    root.classList.add('swi-intro-seen');
    return;
  }

  if (!preview) {
    claimSession(now);
  }

  root.classList.add('swi-intro-pending');
}());
