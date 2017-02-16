<?php
/**
 * Represents the partial view for where users can enter Server Infocard Page
 *
 * @since      1.0.0
 *
 * @subpackage Emercoin_ID/Admin/Partials
 * @package    Emercoin_ID
 *
 */
?>

<input type="text" size="52" name="emcl_settings[emc_infocard]" value="<?php echo $emc_infocard; ?>" placeholder="Infocard Page" />
<p class="description" ><?php _e('Emercoin ID Infocard Page (example: https://id.emercoin.net/infocard)', 'emcl');?></p>
