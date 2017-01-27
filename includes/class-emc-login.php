<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/emercoin/emcid-plugin-wordpress
 * @since      1.0.0
 *
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/includes
 */
class Emc_Login {
	/**
	 * Public class where all hooks are added
	 * @var Emc_Login_Public   $emcl
	 */
	public $emcl;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Emc_Login_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * @var array of plugin settings
	 */
	protected $opts;
	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Emcl plugin instance
	 */
	protected static $_instance = null;

	/**
	 * The plugin text domain for translations
	 *
	 * @since    1.1.3
	 * @access   protected
	 * @var      string    $text_domain    The string used to uniquely identify this plugin.
	 */
	protected $text_domain;

	private $shortcodes;

	/**
	 * Main Emcl Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WSI()
	 * @return Emcl - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 * @param mixed $key
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name  = 'emc-login';
		$this->text_domain  = 'emcl';
		$this->version      = EMCL_VERSION;
		$this->opts         = get_option('emcl_settings');

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Emc_Login_Loader. Orchestrates the hooks of the plugin.
	 * - Emc_Login_i18n. Defines internationalization functionality.
	 * - Emc_Login_Admin. Defines all hooks for the admin area.
	 * - Emc_Login_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emc-login-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emc-login-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emc-login-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-emc-login-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-emc-login-settings.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-emc-login-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emcl-notices.php';

		$this->loader = new Emc_Login_Loader();
		$this->shortcodes = new Emc_Login_Shortcodes( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Emc_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Emc_Login_i18n();
		$plugin_i18n->set_domain( $this->text_domain );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Emc_Login_Admin( $this->get_plugin_name(), $this->get_version() );
		$notices = new Emcl_Notices();

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_items');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'create_settings');
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'profile_buttons' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'profile_buttons' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->emcl = new Emc_Login_Public( $this->get_plugin_name(), $this->get_version() );

		if( !empty( $this->opts['emc_client_id'] ) ) {
			$this->loader->add_action( 'login_form', $this->emcl, 'print_button' );
			$this->loader->add_action( 'register_form', $this->emcl, 'print_button' );
			$this->loader->add_action( 'login_enqueue_scripts', $this->emcl, 'enqueue_styles' );
			$this->loader->add_action( 'login_enqueue_scripts', $this->emcl, 'enqueue_scripts' );
			$this->loader->add_action( 'wp_enqueue_scripts', $this->emcl, 'enqueue_scripts' );
			$this->loader->add_action( 'wp_enqueue_scripts', $this->emcl, 'enqueue_styles' );
			$this->loader->add_action( 'wp_ajax_emcl_emc_login', $this->emcl, 'login_or_register_user' );
			$this->loader->add_action( 'wp_ajax_nopriv_emcl_emc_login', $this->emcl, 'login_or_register_user' );
			$this->loader->add_action( 'emc_login_button', $this->emcl, 'print_button' );
			$this->loader->add_action( 'bp_before_account_details_fields', $this->emcl, 'add_emcl_button' );
			$this->loader->add_action( 'bp_core_general_settings_before_submit', $this->emcl, 'profile_buttons' );
			$this->loader->add_action( 'init', $this->emcl, 'disconnect_emc' );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Emc_Login_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
