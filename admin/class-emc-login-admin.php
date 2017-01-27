<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/emercoin/emcid-plugin-wordpress
 * @since      1.0.0
 *
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/admin
 */
class Emc_Login_Admin {
	/**
	 * @var     string  $views    location of admin views
	 */
	protected $views;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->views = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views' );
	}

	public function add_menu_items() {

		add_submenu_page(
			'options-general.php',
			'Emercoin ID',
			'Emercoin ID',
			apply_filters('emcl/settings_capabilities', 'manage_options'),
			'emc_login',
			array( $this, 'display_settings_page' )
		);
	}

	public function display_settings_page() {
		include_once $this->views . 'settings-page.php';
	}

	public function create_settings() {
		$settings = new Emc_Login_Settings( $this->plugin_name, $this->version);
		$settings->register();
	}

	/**
	 *
	 * Register and enqueue scripts.
	 *
	 * @since     1.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function admin_scripts() {

		global $pagenow;
		if (  ( isset($_GET['page']) && 'emc_login' == $_GET['page']  ) || $pagenow == 'profile.php' ) {

			wp_enqueue_style( 'emcl-admin-css', plugins_url( 'assets/css/admin.css', __FILE__ ) , '', $this->version );
			wp_enqueue_style( 'emcl-public-css', plugins_url( 'public/css/emc-login.css', dirname( __FILE__ ) ) , '', $this->version );
			wp_enqueue_script( 'emcl-public-js', plugins_url( 'public/js/emc-login.js', dirname( __FILE__ ) ) , '', $this->version );
			wp_localize_script( 'emcl-public-js', 'emcl', apply_filters( 'emcl/js_vars', array(
				'redirect'     => urlencode(admin_url('admin-ajax.php') . '?action=emcl_emc_login'),
				'l18n'         => array(
					'chrome_ios_alert'      => __( 'Please login into Emercoin ID and then click connect button again', 'emcl' ),
				)
			)));
		}
	}

	/**
	 * Add extra section on wp-admin/profile.php
	 * @param $user
	 * @since 1.1
	 */
	public function profile_buttons( $user ) {
		?><h3><?php _e("Emercoin ID", "blank"); ?></h3><?php
		$user_meta = get_user_meta( $user->ID, '_emc_user_id' );
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e( 'Emercoin SSL Certificate ID', 'emcl'); ?></th>
					<td>
					<?php
						if( $user_meta[0] ) {
							echo "<em>{$user_meta[0]}</em>";
						} else {
							echo "<em>N/A</em>";
						}
					?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}
