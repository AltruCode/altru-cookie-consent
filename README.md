# Altru Cookie Consent

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A lightweight, modular, and 100% free cookie consent manager for WordPress. Clean code with zero premium upsells.

---

## Features

- **100% GDPR / DSGVO Compliant Interface:** Visual equality for "Accept All" and "Reject All" buttons to prevent dark patterns and ensure legal compliance.
- **Granular Consent Panel:** Users can toggle individual cookie categories, including Necessary, Functional, Analytics, and Marketing.
- **Subtle Re-open Trigger Button:** A discreet, monochrome, translucent floating cookie (🍪) icon in the bottom-left corner of the viewport, allowing users to modify or withdraw consent at any time.
- **Bulletproof Server-side Script Blocking:** Intercepts script tags using Output Buffering (`ob_start` / `template_redirect`) to rewrite type tags and prevent script preloading before consent is granted. It even intercepts hardcoded scripts in theme files.
- **Multilingual Support:** Fully translatable utilizing the WordPress-standard `apply_filters` hook, plus built-in native integration with **Polylang** and **WPML String Translation** (`icl_register_string`).

---

## Installation

1. Go to the GitHub repository **Releases** section and download the latest `altru-cookie-consent.zip` file.
2. Log into your WordPress admin dashboard (`/wp-admin`).
3. Navigate to **Plugins** -> **Add New** -> **Upload Plugin**.
4. Choose the downloaded `.zip` file and click **Install Now**.
5. Once installed, click **Activate**.
6. Navigate to **Settings** -> **Altru Cookie Consent** to configure your cookie banner settings and customize the appearance.

---

## Developer API

You can customize the texts and strings programmatically or integrate custom translations using the `altru_cookie_consent_text` filter.

### Filter Hook Example

```php
/**
 * Customize Altru Cookie Consent strings programmatically.
 *
 * @param string $translated_text The translated text value.
 * @param string $name            The unique identifier of the string.
 * @return string
 */
function my_custom_altru_cookie_texts( $translated_text, $name ) {
    if ( 'cookie_bar_title' === $name ) {
        return 'We value your privacy on our website!';
    }
    return $translated_text;
}
add_filter( 'altru_cookie_consent_text', 'my_custom_altru_cookie_texts', 10, 2 );
```

Available string identifiers for `$name`:
- `cookie_bar_title`
- `cookie_bar_message`
- `btn_accept_all`
- `btn_reject_all`
- `btn_preferences`
- `modal_title`
- `modal_intro`
- `btn_save_settings`
- `label_required`

---

## Contributing

We welcome community contributions, bug reports, and pull requests! 
If you find a bug, please create a new issue on GitHub or submit a Pull Request with your suggested changes.

---

## License

This project is licensed under the MIT License - see the LICENSE file for details.
