<?php
/**
 * Represents the partial view for where users can enter App Client ID
 *
 * @since      1.0.0
 *
 * @subpackage Emercoin_ID/Admin/Partials
 * @package    Emercoin_ID
 *
 */
?>

<input type="text" size="52" name="emcl_settings[emc_client_id]" value="<?php echo $emc_client_id; ?>" placeholder="App Client ID" />
<p class="description" ><?php _e('Create a new App and paste Client ID here', 'emcl');?></p>
