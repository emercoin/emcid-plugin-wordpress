<?php

/**
 *
 * @link              https://github.com/emercoin/emcid-plugin-wordpress
 * @since             1.0.0
 * @package           Emercoin_ID
 *
 * @wordpress-plugin
 * Plugin Name:       Emercoin ID
 * Plugin URI:        https://github.com/emercoin/emcid-plugin-wordpress
 * Description:       Emercoin ID Authorization Plugin.
 * Version:           1.0.0
 * Author:            Aspanta Limited
 * Author URI:        https://www.aspanta.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       emcl
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EMCL_VERSION', '1.0.0');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-emc-login-activator.php
 */
function activate_emc_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-emc-login-activator.php';

	Emc_Login_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_emc_login' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-emc-login.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_emc_login() {

	$plugin = Emc_Login::instance();
	$plugin->run();
	return $plugin;
}

$GLOBALS['emcl'] = run_emc_login();
