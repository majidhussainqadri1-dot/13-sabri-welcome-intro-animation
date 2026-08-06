(function () {
  'use strict';
  var form = document.querySelector('[data-swi-confirm-reset]');
  if (!form) return;
  form.addEventListener('submit', function (event) {
    if (!window.confirm((window.swiAdmin && window.swiAdmin.confirmReset) || 'Restore the safe default Welcome Intro settings?')) event.preventDefault();
  });
}());
