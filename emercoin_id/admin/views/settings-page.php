<?php
/**
 * Displays the UI for editing Emc Login
 *
 * @since      1.0.0
 *
 * @subpackage Emercoin_ID/Admin/Views
 * @package    Emercoin_ID
 *
 */
?>

<div class="wrap">

	<h2>Emercoin ID</h2>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'emcl_settings' );
		do_settings_sections( 'emcl-section' );
		submit_button();
		?>
	</form>

</div><!-- .wrap -->