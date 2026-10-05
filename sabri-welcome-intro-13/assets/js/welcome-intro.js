(function () {
	'use strict';

	var cfg = window.SWI_INTRO || {};
	var root = document.getElementById(cfg.preview ? 'swi-welcome-intro-preview' : 'swi-welcome-intro');
	if (!root) { return; }

	var version = Math.max(1, parseInt(cfg.version || root.getAttribute('data-swi-version') || '1', 10));
	var duration = Math.max(800, Math.min(12000, parseInt(cfg.durationMs || '3200', 10)));
	var days = Math.max(30, Math.min(365, parseInt(cfg.recurrenceDays || '30', 10)));
	var preview = !!cfg.preview;
	var reduced = !!cfg.forceReducedMotion || !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
	var sessionKey = 'swi.seen.v' + version;
	var dismissalKey = 'swi.dismissed.until';
	var closed = false;
	var timer = null;

	function storageGet(store, key) {
		try { return store ? store.getItem(key) : null; } catch (e) { return null; }
	}
	function storageSet(store, key, value) {
		try { if (store) { store.setItem(key, value); return true; } } catch (e) {}
		return false;
	}
	function suppressed() {
		if (preview) { return false; }
		if (storageGet(window.sessionStorage, sessionKey) === '1') { return true; }
		var until = parseInt(storageGet(window.localStorage, dismissalKey) || '0', 10);
		return Number.isFinite(until) && until > Date.now();
	}
	function markDismissed() {
		if (preview) { return; }
		storageSet(window.sessionStorage, sessionKey, '1');
		storageSet(window.localStorage, dismissalKey, String(Date.now() + (days * 86400000)));
	}
	function emit(type) {
		var detail = { event: type, version: version };
		try { window.dispatchEvent(new CustomEvent('swi:intro:' + type, { detail: detail })); } catch (e) {}
		if (!cfg.analyticsEnabled || preview || !cfg.ajaxUrl || !cfg.eventNonce) { return; }
		try {
			var body = new URLSearchParams();
			body.set('action', 'swi_intro_event');
			body.set('_ajax_nonce', cfg.eventNonce);
			body.set('event', type);
			body.set('version', String(version));
			fetch(cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
				body: body.toString(),
				keepalive: true
			}).catch(function () {});
		} catch (e) {}
	}
	function close(type) {
		if (closed) { return; }
		closed = true;
		if (timer) { window.clearTimeout(timer); timer = null; }
		markDismissed();
		emit(type);
		root.classList.add('swi-intro--leave');
		window.setTimeout(function () {
			root.hidden = true;
			root.classList.remove('swi-intro--enter', 'swi-intro--leave');
		}, reduced ? 0 : 170);
	}
	function failOpen() {
		closed = true;
		if (timer) { window.clearTimeout(timer); timer = null; }
		root.hidden = true;
	}

	try {
		if (suppressed()) { root.hidden = true; return; }
		root.style.setProperty('--swi-duration', reduced ? '0ms' : duration + 'ms');
		root.hidden = false;
		root.classList.add('swi-intro--enter');
		if (!preview) { storageSet(window.sessionStorage, sessionKey, '1'); }
		emit('shown');

		var skip = root.querySelector('.swi-intro__skip');
		if (skip) { skip.addEventListener('click', function () { close('skipped'); }, { once: true }); }
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !root.hidden) { close('skipped'); }
		});
		timer = window.setTimeout(function () { close('completed'); }, reduced ? Math.min(800, duration) : duration);
	} catch (error) {
		failOpen();
	}
}());
