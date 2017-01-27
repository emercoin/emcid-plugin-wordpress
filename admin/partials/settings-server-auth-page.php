<?php
/**
 * Represents the partial view for where users can enter Server Auth Page
 *
 * @since      1.0.0
 *
 * @subpackage Emercoin_ID/Admin/Partials
 * @package    Emercoin_ID
 *
 */
?>

<input type="text" size="52" name="emcl_settings[emc_auth_page]" value="<?php echo $emc_auth_page; ?>" placeholder="Auth Page" />
<p class="description" ><?php _e('Emercoin ID Auth Page (example: https://id.emercoin.net/oauth/v2/auth)', 'emcl');?></p>
