<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and starts the plugin.
 *
 * @link              https://github.com/AltruCode/altru-cookie-consent
 * @since             1.0.0
 * @package           Altru_Cookie_Consent
 *
 * @wordpress-plugin
 * Plugin Name:       Altru Cookie Consent
 * Plugin URI:        https://github.com/AltruCode/altru-cookie-consent
 * Description:       A modern, customisable, and GDPR-compliant cookie consent plugin for WordPress.
 * Version:           1.0.0
 * Author:            AltruCode
 * Author URI:        https://github.com/AltruCode
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       altru-cookie-consent
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-altru-cookie-consent-activator.php
 */
function activate_altru_cookie_consent() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-altru-cookie-consent-activator.php';
	Altru_Cookie_Consent_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-altru-cookie-consent-deactivator.php
 */
function deactivate_altru_cookie_consent() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-altru-cookie-consent-deactivator.php';
	Altru_Cookie_Consent_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_altru_cookie_consent' );
register_deactivation_hook( __FILE__, 'deactivate_altru_cookie_consent' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-altru-cookie-consent.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything in the plugin is registered through one or more hooks
 * with WordPress, initiating it from this point with run() is sufficient
 * to start the plugin.
 *
 * @since    1.0.0
 */
function run_altru_cookie_consent() {
	$plugin = new Altru_Cookie_Consent();
	$plugin->run();
}
run_altru_cookie_consent();
