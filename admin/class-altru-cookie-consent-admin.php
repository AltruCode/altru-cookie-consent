<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/AltruCode/altru-cookie-consent
 * @since      1.0.0
 *
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/admin
 * @author     AltruCode
 */
class Altru_Cookie_Consent_Admin {

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
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/altru-cookie-consent-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/altru-cookie-consent-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Add an admin menu page for the plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_options_page(
			'Altru Cookie Consent Beállítások',
			'Altru Cookie Consent',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/altru-cookie-consent-admin-display.php';
	}

	/**
	 * Register settings, sections and fields.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting(
			'altru_cookie_consent_settings',
			'altru_cookie_consent_options',
			array( $this, 'sanitize_options' )
		);

		add_settings_section(
			'altru_cookie_consent_main_section',
			__( 'Main Settings', 'altru-cookie-consent' ),
			array( $this, 'render_section_info' ),
			$this->plugin_name
		);

		add_settings_field(
			'cookie_bar_title',
			__( 'Cookie Bar Title', 'altru-cookie-consent' ),
			array( $this, 'render_title_field' ),
			$this->plugin_name,
			'altru_cookie_consent_main_section'
		);

		add_settings_field(
			'cookie_bar_message',
			__( 'Cookie Bar Message', 'altru-cookie-consent' ),
			array( $this, 'render_message_field' ),
			$this->plugin_name,
			'altru_cookie_consent_main_section'
		);
	}

	/**
	 * Sanitize options.
	 *
	 * @since    1.0.0
	 * @param    array $input
	 * @return   array
	 */
	public function sanitize_options( $input ) {
		$sanitized = array();
		if ( isset( $input['cookie_bar_title'] ) ) {
			$sanitized['cookie_bar_title'] = sanitize_text_field( $input['cookie_bar_title'] );
		}
		if ( isset( $input['cookie_bar_message'] ) ) {
			$sanitized['cookie_bar_message'] = wp_kses_post( $input['cookie_bar_message'] );
		}
		// Preserving other parameters (like buttons and categories)
		$existing = get_option( 'altru_cookie_consent_options', array() );
		return array_merge( $existing, $sanitized );
	}

	public function render_section_info() {
		echo '<p>' . esc_html__( 'Configure the general settings for the cookie consent bar.', 'altru-cookie-consent' ) . '</p>';
		echo '<div style="background-color: #f0f6fc; border-left: 4px solid #72aee6; padding: 12px 16px; margin: 15px 0 25px 0; border-radius: 0 4px 4px 0; max-width: 800px;">';
		echo '<p style="margin: 0; font-size: 13px; color: #1d2327; line-height: 1.5;">';
		echo '<strong>' . esc_html__( 'ℹ️ Multilingual Notice:', 'altru-cookie-consent' ) . '</strong> ';
		echo esc_html__( 'If you modify these texts, remember to update their translations in your multilingual plugin (Polylang or WPML String Translation) to keep all language versions in sync.', 'altru-cookie-consent' );
		echo '</p>';
		echo '</div>';
	}

	public function render_title_field() {
		$options = get_option( 'altru_cookie_consent_options' );
		$val = isset( $options['cookie_bar_title'] ) ? $options['cookie_bar_title'] : '';
		echo '<input type="text" class="regular-text" name="altru_cookie_consent_options[cookie_bar_title]" value="' . esc_attr( $val ) . '" />';
	}

	public function render_message_field() {
		$options = get_option( 'altru_cookie_consent_options' );
		$val = isset( $options['cookie_bar_message'] ) ? $options['cookie_bar_message'] : '';
		echo '<textarea class="large-text" rows="5" name="altru_cookie_consent_options[cookie_bar_message]">' . esc_textarea( $val ) . '</textarea>';
	}
}
