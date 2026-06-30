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

	/**
	 * Start the output buffer to capture HTML before it is sent to the client.
	 *
	 * @since    1.0.0
	 */
	public function start_buffer() {
		if ( ! is_admin() && ! wp_doing_ajax() && ! wp_is_json_request() ) {
			ob_start( array( $this, 'filter_html_output' ) );
		}
	}

	/**
	 * Process and filter the captured HTML output.
	 *
	 * @since    1.0.0
	 * @param    string $html The captured HTML.
	 * @return   string The filtered HTML.
	 */
	public function filter_html_output( $html ) {
		if ( empty( $html ) || false === stripos( $html, '<html' ) ) {
			return $html;
		}

		// Parse and filter script tags
		return preg_replace_callback( '/<script([^>]*)>(.*?)<\/script>/is', array( $this, 'filter_script_tag' ), $html );
	}

	/**
	 * Callback to modify script tags containing trackers based on cookie consent.
	 *
	 * @since    1.0.0
	 * @param    array $matches Regex match containing the script tag, attributes, and content.
	 * @return   string The unmodified or modified script tag.
	 */
	private function filter_script_tag( $matches ) {
		$full_tag   = $matches[0];
		$attributes = $matches[1];
		$content    = $matches[2];

		// Check if it already has a non-JS type (e.g. application/json, ld+json)
		if ( preg_match( '/type\s*=\s*[\'"]([^\'"]+)[\'"]/i', $attributes, $type_match ) ) {
			$type = strtolower( trim( $type_match[1] ) );
			if ( ! in_array( $type, array( '', 'text/javascript', 'application/javascript', 'module' ) ) ) {
				return $full_tag;
			}
		}

		// Tracker patterns mapped to consent categories
		$blocked_patterns = array(
			'googletagmanager.com/gtag/js' => 'analytics',
			'googletagmanager.com/gtm.js'  => 'analytics',
			'google-analytics.com'         => 'analytics',
			'gtag('                        => 'analytics',
			'ga('                          => 'analytics',
			'connect.facebook.net'         => 'marketing',
			'snap.licdn.com'               => 'marketing',
			'analytics.tiktok.com'         => 'marketing',
			'static.hotjar.com'            => 'analytics',
		);

		$matched_category = null;
		foreach ( $blocked_patterns as $pattern => $category ) {
			if ( false !== stripos( $attributes, $pattern ) || false !== stripos( $content, $pattern ) ) {
				$matched_category = $category;
				break;
			}
		}

		// If no tracker pattern matched, let it load normally
		if ( ! $matched_category ) {
			return $full_tag;
		}

		// Read cookie consent value
		$consent = array();
		if ( isset( $_COOKIE['altru_cookie_consent'] ) ) {
			$decoded = json_decode( stripslashes( $_COOKIE['altru_cookie_consent'] ), true );
			if ( is_array( $decoded ) ) {
				$consent = $decoded;
			}
		}

		// If the specific category is accepted, let it load normally
		if ( isset( $consent[ $matched_category ] ) && true === $consent[ $matched_category ] ) {
			return $full_tag;
		}

		// Otherwise, block the script
		// 1. Set type attribute to text/plain
		if ( preg_match( '/type\s*=\s*[\'"]([^\'"]+)[\'"]/i', $attributes ) ) {
			$attributes = preg_replace( '/type\s*=\s*[\'"]([^\'"]+)[\'"]/i', 'type="text/plain"', $attributes );
		} else {
			$attributes .= ' type="text/plain"';
		}

		// 2. Add altru category data attribute
		$attributes .= ' data-altru-category="' . esc_attr( $matched_category ) . '"';

		// 3. Rename src to data-altru-src to prevent browser preload/download
		if ( preg_match( '/src\s*=\s*[\'"]([^\'"]+)[\'"]/i', $attributes, $src_match ) ) {
			$src        = $src_match[1];
			$attributes = preg_replace( '/src\s*=\s*[\'"]([^\'"]+)[\'"]/i', 'data-altru-src="' . esc_url( $src ) . '"', $attributes );
		}

		return '<script' . $attributes . '>' . $content . '</script>';
	}

	/**
	 * Translate a string using Polylang or WPML and apply filters.
	 *
	 * @since    1.0.0
	 * @param    string $name The string identifier.
	 * @param    string $default_value The default string value.
	 * @return   string The translated string.
	 */
	public function translate_string( $name, $default_value ) {
		$translated = $default_value;

		if ( function_exists( 'pll__' ) ) {
			$translated = pll__( $default_value );
		} elseif ( function_exists( 'icl_t' ) ) {
			$translated = icl_t( 'altru-cookie-consent', $name, $default_value );
		}

		return apply_filters( 'altru_cookie_consent_text', $translated, $name );
	}
}
