(function () {
  'use strict';

  var intro = document.getElementById('swi-intro');

  if (!intro) {
    return;
  }

  var root = document.documentElement;
  var skipButton = intro.querySelector('[data-swi-skip]');
  var cookieName = intro.getAttribute('data-cookie-name') || 'sabri_welcome_seen';
  var duration = parseInt(intro.getAttribute('data-duration'), 10) || 8000;
  var reducedDuration = parseInt(intro.getAttribute('data-reduced-duration'), 10) || 1200;
  var isPreview = intro.getAttribute('data-preview') === '1';
  var reduceMotion = Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  var previousFocus = document.activeElement;
  var backgroundStates = [];
  var closed = false;
  var fallbackTimer = null;
  var skipTimer = null;

  if (!root.classList.contains('swi-intro-pending') && !isPreview) {
    intro.remove();
    return;
  }

  if (isPreview) {
    root.classList.remove('swi-intro-seen');
    root.classList.add('swi-intro-pending');
  }

  if (reduceMotion) {
    intro.classList.add('swi-reduced-motion');
    duration = reducedDuration;
  }

  function cssFailSafeCompleted() {
    if (typeof window.getComputedStyle !== 'function') {
      return false;
    }

    var style = window.getComputedStyle(intro);
    return style.visibility === 'hidden' || (style.pointerEvents === 'none' && parseFloat(style.opacity) === 0);
  }

  if (cssFailSafeCompleted()) {
    intro.remove();
    root.classList.remove('swi-intro-pending');

    if (!isPreview) {
      root.classList.add('swi-intro-seen');
    }

    return;
  }

  function rememberVisit() {
    if (isPreview) {
      return;
    }

    var secure = window.location.protocol === 'https:' ? '; Secure' : '';

    try {
      document.cookie = cookieName + '=1; Path=/; SameSite=Lax' + secure;
    } catch (error) {
      // The early bootstrap already attempted session persistence.
    }

    try {
      window.sessionStorage.setItem(cookieName + '_session', '1');
    } catch (error) {
      // A storage failure must not block the website.
    }
  }

  function setBackgroundInert() {
    var current = intro;

    while (current && current.parentElement && current.parentElement !== document.documentElement) {
      Array.prototype.forEach.call(current.parentElement.children, function (sibling) {
        if (sibling === current || sibling.nodeType !== 1 || sibling.tagName === 'SCRIPT' || sibling.tagName === 'STYLE') {
          return;
        }

        backgroundStates.push({
          element: sibling,
          hadInert: sibling.hasAttribute('inert'),
          ariaHidden: sibling.getAttribute('aria-hidden')
        });

        sibling.setAttribute('inert', '');
        sibling.setAttribute('aria-hidden', 'true');
      });

      current = current.parentElement;
    }
  }

  function restoreBackground() {
    backgroundStates.forEach(function (state) {
      if (!state.element || !state.element.isConnected) {
        return;
      }

      if (!state.hadInert) {
        state.element.removeAttribute('inert');
      }

      if (state.ariaHidden === null) {
        state.element.removeAttribute('aria-hidden');
      } else {
        state.element.setAttribute('aria-hidden', state.ariaHidden);
      }
    });

    backgroundStates = [];
  }

  function restoreFocus() {
    if (!previousFocus || previousFocus === document.body || previousFocus === document.documentElement) {
      return;
    }

    if (previousFocus.isConnected && typeof previousFocus.focus === 'function') {
      try {
        previousFocus.focus({ preventScroll: true });
      } catch (error) {
        previousFocus.focus();
      }
    }
  }

  function removeListeners() {
    document.removeEventListener('keydown', onKeydown, true);
    intro.removeEventListener('animationend', onAnimationEnd);

    if (skipButton) {
      skipButton.removeEventListener('click', skipIntro);
    }
  }

  function finalizeRemoval() {
    if (intro.isConnected) {
      intro.remove();
    }

    document.body.classList.remove('swi-intro-active');
    root.classList.remove('swi-intro-pending');

    if (!isPreview) {
      root.classList.add('swi-intro-seen');
    }

    restoreBackground();
    restoreFocus();
  }

  function closeIntro(skipped) {
    if (closed) {
      return;
    }

    closed = true;
    rememberVisit();
    window.clearTimeout(fallbackTimer);
    window.clearTimeout(skipTimer);
    removeListeners();

    if (skipped && !reduceMotion) {
      intro.classList.add('swi-is-skipped');
      skipTimer = window.setTimeout(finalizeRemoval, 260);
      return;
    }

    finalizeRemoval();
  }

  function skipIntro() {
    closeIntro(true);
  }

  function onAnimationEnd(event) {
    if (event.target === intro && event.animationName === 'swi-overlay-exit') {
      closeIntro(false);
    }
  }

  function onKeydown(event) {
    if (event.key === 'Escape') {
      event.preventDefault();
      skipIntro();
      return;
    }

    if (event.key !== 'Tab') {
      return;
    }

    event.preventDefault();

    if (skipButton) {
      skipButton.focus();
    } else {
      intro.focus();
    }
  }

  rememberVisit();
  document.body.classList.add('swi-intro-active');
  setBackgroundInert();

  if (skipButton) {
    skipButton.addEventListener('click', skipIntro);
  }

  document.addEventListener('keydown', onKeydown, true);
  intro.addEventListener('animationend', onAnimationEnd);

  window.requestAnimationFrame(function () {
    if (closed) {
      return;
    }

    try {
      (skipButton || intro).focus({ preventScroll: true });
    } catch (error) {
      (skipButton || intro).focus();
    }
  });

  // The CSS animation is authoritative; this timer is only a bounded cleanup fallback.
  fallbackTimer = window.setTimeout(function () {
    closeIntro(false);
  }, duration + 1250);
}());
