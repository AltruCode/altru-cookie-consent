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

		var btnReopen = document.getElementById('altru-cookie-btn-reopen');
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
				// Show cookie banner, hide reopen button
				wrapper.classList.remove('altru-hidden');
				if (btnReopen) btnReopen.classList.add('altru-hidden');
			} else {
				// Hide cookie banner, show reopen button
				wrapper.classList.add('altru-hidden');
				if (btnReopen) btnReopen.classList.remove('altru-hidden');
				
				// Apply consent if already saved (trigger scripts)
				try {
					var consentData = JSON.parse(consent);
					triggerConsentEvent(consentData);
				} catch (e) {
					wrapper.classList.remove('altru-hidden');
				}
			}

			// Bind Events
			if (btnReopen) {
				btnReopen.addEventListener('click', function() {
					wrapper.classList.remove('altru-hidden');
					btnReopen.classList.add('altru-hidden');
				});
			}
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
			var isChange = !!getCookie(cookieName);
			
			setCookie(cookieName, JSON.stringify(consentData), expiryDays);
			
			// Delete cookies of rejected categories
			for (var cat in consentData) {
				if (consentData.hasOwnProperty(cat) && !consentData[cat]) {
					deleteCookiesForCategory(cat);
				}
			}

			wrapper.classList.add('altru-hidden');
			if (btnReopen) btnReopen.classList.remove('altru-hidden');
			triggerConsentEvent(consentData);

			// Reload page to clean state and stop trackers if consent changed or declined
			if (isChange || !consentData['analytics'] || !consentData['marketing']) {
				location.reload();
			}
		}

		function deleteCookiesForCategory(category) {
			var cookies = document.cookie.split(";");
			var domains = [
				window.location.hostname,
				'.' + window.location.hostname,
				window.location.hostname.replace(/^www\./, ''),
				'.' + window.location.hostname.replace(/^www\./, '')
			];

			var patterns = [];
			if (category === 'analytics') {
				patterns = [/^__utma/, /^__utmb/, /^__utmc/, /^__utmz/, /^_ga/, /^_gid/, /^_gat/, /^_hj/];
			} else if (category === 'marketing') {
				patterns = [/^_fbp/, /^_gcl_au/, /^_uetsid/, /^_uetvid/, /^li_sugr/, /^UserMatchHistory/, /^AnalyticsSyncHistory/];
			}

			if (patterns.length === 0) return;

			for (var i = 0; i < cookies.length; i++) {
				var cookie = cookies[i].trim();
				var eqPos = cookie.indexOf("=");
				var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;

				var match = false;
				for (var p = 0; p < patterns.length; p++) {
					if (patterns[p].test(name)) {
						match = true;
						break;
					}
				}

				if (match) {
					domains.forEach(function(domain) {
						document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + domain;
						document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
					});
				}
			}
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

		// Trigger custom event for other scripts to listen to and activate accepted scripts
		function triggerConsentEvent(consentData) {
			var event;
			if (typeof window.CustomEvent === 'function') {
				event = new CustomEvent('altruCookieConsentUpdated', { detail: consentData });
			} else {
				event = document.createEvent('CustomEvent');
				event.initCustomEvent('altruCookieConsentUpdated', true, true, consentData);
			}
			window.dispatchEvent(event);

			// Activate the scripts that are now allowed
			activateAcceptedScripts(consentData);
		}

		// Dynamically activate scripts that have been accepted by the user
		function activateAcceptedScripts(consentData) {
			if (!consentData) return;

			var scripts = document.querySelectorAll('script[type="text/plain"][data-altru-category]');
			
			scripts.forEach(function(oldScript) {
				var category = oldScript.dataset.altruCategory;
				
				if (consentData[category] === true) {
					var newScript = document.createElement('script');
					
					// Copy all attributes except type and data-altru-src
					Array.from(oldScript.attributes).forEach(function(attr) {
						if (attr.name !== 'type' && attr.name !== 'data-altru-src' && attr.name !== 'src') {
							newScript.setAttribute(attr.name, attr.value);
						}
					});
					
					newScript.type = 'text/javascript';
					
					if (oldScript.dataset.altruSrc) {
						newScript.src = oldScript.dataset.altruSrc;
					}
					
					if (oldScript.innerHTML) {
						newScript.innerHTML = oldScript.innerHTML;
					}
					
					if (oldScript.parentNode) {
						oldScript.parentNode.insertBefore(newScript, oldScript);
						oldScript.parentNode.removeChild(oldScript);
					}
				}
			});
		}
	});
})();
