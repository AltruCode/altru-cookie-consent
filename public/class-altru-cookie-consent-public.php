<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/AltruCode/altru-cookie-consent
 * @since      1.0.0
 *
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/public
 * @author     AltruCode
 */
class Altru_Cookie_Consent_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/altru-cookie-consent-public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/altru-cookie-consent-public.js',
			array(),
			$this->version,
			false
		);

		// Localize parameters for Javascript
		$options = get_option( 'altru_cookie_consent_options' );
		wp_localize_script(
			$this->plugin_name,
			'altruCookieConsentData',
			array(
				'options' => $options,
			)
		);
	}

	/**
	 * Render the cookie consent bar in the page footer.
	 *
	 * @since    1.0.0
	 */
	public function render_cookie_consent_bar() {
		// Check if user has already accepted cookies (using Javascript cookie check mostly, but we can do a check or just render and let JS hide it)
		require_once plugin_dir_path( __FILE__ ) . 'partials/altru-cookie-consent-public-display.php';
	}
}
