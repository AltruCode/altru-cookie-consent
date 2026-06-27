<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/AltruCode/altru-cookie-consent
 * @since      1.0.0
 *
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/public/partials
 */

$options = get_option( 'altru_cookie_consent_options' );
$title   = isset( $options['cookie_bar_title'] ) ? $options['cookie_bar_title'] : '';
$message = isset( $options['cookie_bar_message'] ) ? $options['cookie_bar_message'] : '';
$accept  = isset( $options['btn_accept_all'] ) ? $options['btn_accept_all'] : 'Accept All';
$reject  = isset( $options['btn_reject_all'] ) ? $options['btn_reject_all'] : 'Reject All';
$pref    = isset( $options['btn_preferences'] ) ? $options['btn_preferences'] : 'Customize';
$cats    = isset( $options['consent_categories'] ) ? $options['consent_categories'] : array();
?>

<!-- Altru Cookie Consent Wrapper -->
<div id="altru-cookie-consent" class="altru-cookie-consent-wrapper altru-hidden" aria-labelledby="altru-cookie-title" aria-describedby="altru-cookie-desc" role="dialog">
	
	<!-- Cookie Banner (Main Bar) -->
	<div class="altru-cookie-banner">
		<div class="altru-cookie-banner-content">
			<h3 id="altru-cookie-title" class="altru-title"><?php echo esc_html( $title ); ?></h3>
			<p id="altru-cookie-desc" class="altru-message"><?php echo esc_html( $message ); ?></p>
		</div>
		<div class="altru-cookie-banner-actions">
			<button id="altru-cookie-btn-preferences" class="altru-btn altru-btn-secondary" type="button"><?php echo esc_html( $pref ); ?></button>
			<button id="altru-cookie-btn-reject" class="altru-btn altru-btn-outline" type="button"><?php echo esc_html( $reject ); ?></button>
			<button id="altru-cookie-btn-accept" class="altru-btn altru-btn-primary" type="button"><?php echo esc_html( $accept ); ?></button>
		</div>
	</div>

	<!-- Cookie Preferences Modal (Drawer/Popup) -->
	<div id="altru-cookie-modal" class="altru-cookie-modal altru-hidden" aria-modal="true" role="dialog" aria-labelledby="altru-modal-title">
		<div class="altru-cookie-modal-backdrop"></div>
		<div class="altru-cookie-modal-container">
			<div class="altru-cookie-modal-header">
				<h3 id="altru-modal-title"><?php esc_html_e( 'Cookie Preferences', 'altru-cookie-consent' ); ?></h3>
				<button id="altru-cookie-modal-close" class="altru-modal-close" aria-label="<?php esc_attr_e( 'Close', 'altru-cookie-consent' ); ?>" type="button">&times;</button>
			</div>
			<div class="altru-cookie-modal-body">
				<p class="altru-modal-intro"><?php esc_html_e( 'Manage your consent preferences for cookies and similar technologies used on this website.', 'altru-cookie-consent' ); ?></p>
				
				<div class="altru-cookie-categories">
					<?php foreach ( $cats as $key => $cat ) : ?>
						<div class="altru-cookie-category-item">
							<div class="altru-cookie-category-header">
								<div class="altru-cookie-category-title-wrap">
									<span class="altru-cookie-category-title"><?php echo esc_html( $cat['title'] ); ?></span>
									<?php if ( ! empty( $cat['required'] ) ) : ?>
										<span class="altru-cookie-category-badge"><?php esc_html_e( 'Required', 'altru-cookie-consent' ); ?></span>
									<?php endif; ?>
								</div>
								<div class="altru-cookie-toggle-wrapper">
									<label class="altru-switch">
										<input type="checkbox" id="altru-cat-<?php echo esc_attr( $key ); ?>" class="altru-cookie-cat-checkbox" data-category="<?php echo esc_attr( $key ); ?>" <?php checked( $cat['enabled'] || ! empty( $cat['required'] ) ); ?> <?php disabled( ! empty( $cat['required'] ) ); ?> />
										<span class="altru-slider round"></span>
									</label>
								</div>
							</div>
							<p class="altru-cookie-category-desc"><?php echo esc_html( $cat['description'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="altru-cookie-modal-footer">
				<button id="altru-cookie-btn-save" class="altru-btn altru-btn-primary" type="button"><?php esc_html_e( 'Save Preferences', 'altru-cookie-consent' ); ?></button>
			</div>
		</div>
	</div>
</div>
