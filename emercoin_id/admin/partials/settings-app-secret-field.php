<?php
/**
 * Represents the partial view for where users can enter App Secret key
 *
 * @since      1.0.0
 *
 * @subpackage Emercoin_ID/Admin/Partials
 * @package    Emercoin_ID
 *
 */
?>

<input type="text" size="52" name="emcl_settings[emc_app_secret]" value="<?php echo $emc_app_secret; ?>" placeholder="App Secret key" />
<p class="description" ><?php _e("Paste your App's Secret Key", 'emcl');?></p>
