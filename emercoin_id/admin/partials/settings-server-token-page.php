<?php
/**
 * Represents the partial view for where users can enter Server Token Page
 *
 * @since      1.0.0
 *
 * @subpackage Emercoin_ID/Admin/Partials
 * @package    Emercoin_ID
 *
 */
?>

<input type="text" size="52" name="emcl_settings[emc_token_page]" value="<?php echo $emc_token_page; ?>" placeholder="Token Page" />
<p class="description" ><?php _e('Emercoin ID Token Page (example: https://id.emercoin.net/oauth/v2/token)', 'emcl');?></p>
