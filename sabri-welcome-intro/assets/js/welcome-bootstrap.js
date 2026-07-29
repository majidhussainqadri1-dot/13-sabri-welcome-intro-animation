(function () {
  'use strict';

  var isPreview = false;

  try {
    isPreview = new URLSearchParams(window.location.search).get('swi_preview') === '1';
  } catch (error) {
    isPreview = window.location.search.indexOf('swi_preview=1') !== -1;
  }

  if (isPreview) {
    return;
  }

  var seen = document.cookie.split(';').some(function (item) {
    return item.trim().indexOf('sabri_welcome_seen=') === 0;
  });

  if (seen) {
    document.documentElement.classList.add('swi-intro-seen');
  }
}());
