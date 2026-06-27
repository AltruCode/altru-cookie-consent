<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/AltruCode/altru-cookie-consent
 * @since      1.0.0
 *
 * @package    Altru_Cookie_Consent
 * @subpackage Altru_Cookie_Consent/admin/partials
 */
?>

<div class="wrap">
	<h2>Altru Cookie Consent Beállítások</h2>
	<form method="post" action="options.php">
		<?php
			settings_fields( 'altru_cookie_consent_settings' );
			do_settings_sections( 'altru-cookie-consent' );
			submit_button();
		?>
	</form>
</div>
