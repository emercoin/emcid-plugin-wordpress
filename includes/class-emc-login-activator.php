<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/emercoin/emcid-plugin-wordpress
 * @since      1.0.0
 *
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/includes
 */
class Emc_Login_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// $upgrader = new Emcl_Upgrader( 'emcl', EMCL_VERSION);
		// $upgrader->upgrade_plugin();

		update_option('emcl_version', EMCL_VERSION);
	}

}
