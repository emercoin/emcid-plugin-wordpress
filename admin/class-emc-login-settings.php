<?php
class Emc_Login_Settings {

	public function __construct() {
		$this->views    = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/' );
		$this->app_fields   = array(
			'app_id'       => __('App Client ID', 'emcl'),
			'app_secret'   => __('App Secret Key', 'emcl'),
		);
	}

	/**
	 * Register sections fields and settings
	 */
	public function register() {

		register_setting(
			'emcl_settings',			// Group of options
			'emcl_settings',     	    // Name of options
			array( $this, 'sanitize' )	// Sanitization function
		);

		add_settings_section(
			'emcl-app',			    // ID of the settings section
			'App Settings',			// Title of the section
			'',
			'emcl-section'			// ID of the page
		);

		foreach( $this->app_fields as $key => $name) {
			add_settings_field(
				$key,        			// The ID of the settings field
				$name,                	// The name of the field of setting(s)
				array( $this, 'display_'.$key ),
				'emcl-section',        	// ID of the page on which to display these fields
				'emcl-app'            	// The ID of the setting section
			);
		}

		add_settings_field(
			'',
			'',
			array( $this, 'display_hidden_fields' ),
			'emcl-section',
			'emcl-app'
		);
	}

	/**
	 * Display Client ID field
	 */
	public function display_app_id() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'emcl_settings' );
		$emc_client_id = isset( $opts['emc_client_id'] ) ? $opts['emc_client_id'] : '';
		// And display the view
		include_once $this->views . 'settings-app-client-id-field.php';
	}

	/**
	 * Display Secret key field
	 */
	public function display_app_secret() {
		// Now grab the options based on what we're looking for
		$opts = get_option( 'emcl_settings' );
		$emc_app_secret = isset( $opts['emc_app_secret'] ) ? $opts['emc_app_secret'] : '';
		// And display the view
		include $this->views . 'settings-app-secret-field.php';
	}

	/**
	 * Output Server settings in hidden fields
	 */
	public function display_hidden_fields() {
		include $this->views . 'settings-server-hidden-fields.php';
	}

	/**
	 * Simple sanitize function
	 * @param $input
	 *
	 * @return array
	 */
	public function sanitize( $input ) {

		$new_input = array();

		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {
			$new_input[ $key ] = sanitize_text_field( $val );
		}

		return $new_input;
	}
}
