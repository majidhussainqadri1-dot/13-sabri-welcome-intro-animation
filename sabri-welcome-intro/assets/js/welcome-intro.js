(function () {
  'use strict';

  var intro = document.getElementById('swi-intro');

  if (!intro) {
    return;
  }

  var root = document.documentElement;
  var cookieName = intro.getAttribute('data-cookie-name') || 'sabri_welcome_seen';
  var duration = parseInt(intro.getAttribute('data-duration'), 10) || 8000;
  var reducedDuration = parseInt(intro.getAttribute('data-reduced-duration'), 10) || 1200;
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var isPreview = false;
  var closed = false;
  var timer;

  try {
    isPreview = new URLSearchParams(window.location.search).get('swi_preview') === '1';
  } catch (error) {
    isPreview = window.location.search.indexOf('swi_preview=1') !== -1;
  }

  if (root.classList.contains('swi-intro-seen') && !isPreview) {
    intro.remove();
    return;
  }

  if (reduceMotion) {
    intro.classList.add('swi-reduced-motion');
    duration = reducedDuration;
  }

  document.body.classList.add('swi-intro-active');
  intro.classList.add('swi-is-running');

  function rememberVisit() {
    if (isPreview) {
      return;
    }

    var secure = window.location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = cookieName + '=1; path=/; SameSite=Lax' + secure;
    root.classList.add('swi-intro-seen');
  }

  function removeIntro() {
    intro.remove();
    document.body.classList.remove('swi-intro-active');
  }

  function completeIntro() {
    if (closed) {
      return;
    }

    closed = true;
    rememberVisit();
    removeIntro();
  }

  function skipIntro() {
    if (closed) {
      return;
    }

    closed = true;
    window.clearTimeout(timer);
    rememberVisit();
    intro.classList.add('swi-is-skipped');
    window.setTimeout(removeIntro, reduceMotion ? 0 : 260);
  }

  var skipButton = intro.querySelector('[data-swi-skip]');

  if (skipButton) {
    skipButton.addEventListener('click', skipIntro);
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      skipIntro();
    }
  });

  timer = window.setTimeout(completeIntro, duration);
}());
