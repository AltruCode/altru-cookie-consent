<?php
/**
 * Fired during plugin activation
 *
 * @link       https://github.com/AltruCode/altru-cookie-consent
 * @since      1.0.0
 *
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/includes
 * @author     AltruCode
 */
class Altru_Cookie_Consent_Activator {

	/**
	 * Short description.
	 *
	 * Long description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Default options setup can go here
		if ( false === get_option( 'altru_cookie_consent_options' ) ) {
			$default_options = array(
				'cookie_bar_title'   => __( 'We value your privacy', 'altru-cookie-consent' ),
				'cookie_bar_message' => __( 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.', 'altru-cookie-consent' ),
				'btn_accept_all'     => __( 'Accept All', 'altru-cookie-consent' ),
				'btn_reject_all'     => __( 'Reject All', 'altru-cookie-consent' ),
				'btn_preferences'    => __( 'Customize', 'altru-cookie-consent' ),
				'cookie_expiry_days' => 365,
				'consent_categories' => array(
					'necessary'  => array(
						'title'       => __( 'Necessary', 'altru-cookie-consent' ),
						'description' => __( 'These cookies are required for the website to function and cannot be switched off.', 'altru-cookie-consent' ),
						'required'    => true,
						'enabled'     => true,
					),
					'functional' => array(
						'title'       => __( 'Functional', 'altru-cookie-consent' ),
						'description' => __( 'These cookies enable the website to provide enhanced functionality and personalization.', 'altru-cookie-consent' ),
						'required'    => false,
						'enabled'     => false,
					),
					'analytics'  => array(
						'title'       => __( 'Analytics', 'altru-cookie-consent' ),
						'description' => __( 'These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site.', 'altru-cookie-consent' ),
						'required'    => false,
						'enabled'     => false,
					),
					'marketing'  => array(
						'title'       => __( 'Marketing', 'altru-cookie-consent' ),
						'description' => __( 'These cookies may be set through our site by our advertising partners to build a profile of your interests.', 'altru-cookie-consent' ),
						'required'    => false,
						'enabled'     => false,
					),
				),
			);
			update_option( 'altru_cookie_consent_options', $default_options );
		}
	}
}
