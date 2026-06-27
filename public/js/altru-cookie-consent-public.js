/**
 * Frontend JavaScript for Altru Cookie Consent
 *
 * @since      1.0.0
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/public/js
 */

(function() {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
		// Elements
		var wrapper = document.getElementById('altru-cookie-consent');
		if (!wrapper) return;

		var btnAccept = document.getElementById('altru-cookie-btn-accept');
		var btnReject = document.getElementById('altru-cookie-btn-reject');
		var btnPref = document.getElementById('altru-cookie-btn-preferences');
		var modal = document.getElementById('altru-cookie-modal');
		var modalClose = document.getElementById('altru-cookie-modal-close');
		var modalBackdrop = modal ? modal.querySelector('.altru-cookie-modal-backdrop') : null;
		var btnSave = document.getElementById('altru-cookie-btn-save');
		var checkboxes = document.querySelectorAll('.altru-cookie-cat-checkbox');

		// Cookie name & expiry
		var cookieName = 'altru_cookie_consent';
		var expiryDays = (window.altruCookieConsentData && window.altruCookieConsentData.options && window.altruCookieConsentData.options.cookie_expiry_days) 
			? parseInt(window.altruCookieConsentData.options.cookie_expiry_days, 10) 
			: 365;

		// Initialize
		init();

		function init() {
			var consent = getCookie(cookieName);
			if (!consent) {
				// Show cookie banner
				wrapper.classList.remove('altru-hidden');
			} else {
				// Apply consent if already saved (trigger scripts)
				try {
					var consentData = JSON.parse(consent);
					triggerConsentEvent(consentData);
				} catch (e) {
					wrapper.classList.remove('altru-hidden');
				}
			}

			// Bind Events
			if (btnAccept) btnAccept.addEventListener('click', acceptAll);
			if (btnReject) btnReject.addEventListener('click', rejectAll);
			if (btnPref) btnPref.addEventListener('click', openPreferences);
			if (modalClose) modalClose.addEventListener('click', closePreferences);
			if (modalBackdrop) modalBackdrop.addEventListener('click', closePreferences);
			if (btnSave) btnSave.addEventListener('click', savePreferences);
		}

		function acceptAll() {
			var consent = {};
			checkboxes.forEach(function(cb) {
				consent[cb.dataset.category] = true;
			});
			save(consent);
		}

		function rejectAll() {
			var consent = {};
			checkboxes.forEach(function(cb) {
				// Necessary is always true, others false
				consent[cb.dataset.category] = cb.dataset.category === 'necessary';
			});
			save(consent);
		}

		function openPreferences() {
			if (modal) {
				// Load current consent to checkboxes if exists
				var currentConsent = getCookie(cookieName);
				if (currentConsent) {
					try {
						var consentData = JSON.parse(currentConsent);
						checkboxes.forEach(function(cb) {
							if (cb.dataset.category !== 'necessary') {
								cb.checked = !!consentData[cb.dataset.category];
							}
						});
					} catch(e) {}
				}
				modal.classList.remove('altru-hidden');
			}
		}

		function closePreferences() {
			if (modal) {
				modal.classList.add('altru-hidden');
			}
		}

		function savePreferences() {
			var consent = {};
			checkboxes.forEach(function(cb) {
				consent[cb.dataset.category] = cb.checked || cb.dataset.category === 'necessary';
			});
			save(consent);
			closePreferences();
		}

		function save(consentData) {
			setCookie(cookieName, JSON.stringify(consentData), expiryDays);
			wrapper.classList.add('altru-hidden');
			triggerConsentEvent(consentData);
		}

		// Cookie Helpers
		function setCookie(name, value, days) {
			var expires = "";
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
		}

		function getCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		}

		// Trigger custom event for other scripts to listen to
		function triggerConsentEvent(consentData) {
			var event;
			if (typeof window.CustomEvent === 'function') {
				event = new CustomEvent('altruCookieConsentUpdated', { detail: consentData });
			} else {
				event = document.createEvent('CustomEvent');
				event.initCustomEvent('altruCookieConsentUpdated', true, true, consentData);
			}
			window.dispatchEvent(event);
		}
	});
})();
