(function () {
	'use strict';

	var cfg = window.SWI_INTRO || {};
	var root = document.getElementById('swi-welcome-intro-preview');
	if (!root || !cfg.preview) { return; }

	var reduced = !!cfg.forceReducedMotion || !!(
		window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
	);
	var duration = Math.max(800, Math.min(12000, parseInt(cfg.durationMs || '3200', 10)));
	var timer = null;
	var closed = false;

	function close() {
		if (closed) { return; }
		closed = true;
		if (timer) { window.clearTimeout(timer); timer = null; }
		root.classList.add('swi-intro--leave');
		window.setTimeout(function () {
			root.hidden = true;
			root.classList.remove('swi-intro--enter', 'swi-intro--leave');
		}, reduced ? 0 : 170);
	}

	try {
		root.style.setProperty('--swi-duration', reduced ? '0ms' : duration + 'ms');
		root.hidden = false;
		root.classList.add('swi-intro--enter');
		var skip = root.querySelector('.swi-intro__skip');
		if (skip) { skip.addEventListener('click', close, { once: true }); }
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !root.hidden) { close(); }
		});
		timer = window.setTimeout(close, reduced ? Math.min(800, duration) : duration);
	} catch (error) {
		root.hidden = true;
	}
}());
