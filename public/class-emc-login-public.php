<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/emercoin/emcid-plugin-wordpress
 * @since      1.0.0
 *
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/public
 */
class Emc_Login_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->opts         = get_option('emcl_settings');
		$this->redirect_uri = admin_url('admin-ajax.php') . '?action=emcl_emc_login';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/emc-login.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/emc-login.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'emcl', apply_filters( 'emcl/js_vars', array(
			'redirect'     => urlencode($this->redirect_uri),
			'appId'        => $this->opts['emc_client_id'],
			'authPage'     => $this->opts['emc_auth_page'],
			'l18n'         => array(
				'chrome_ios_alert'      => __( 'Please login into Emercoin ID and then click the button again', 'emcl' ),
			)
		)));
	}

	/**
	 * Print the button on login page
	 * @since   1.0.0
	 */
	public function print_button() {
		$redirect = apply_filters( 'flp/redirect_url', ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		// if we are in login page we don't want to redirect back to it
		if ( isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) )
			$redirect = apply_filters( 'flp/redirect_url', '');

		if ( isset( $_GET['emc_error'] ) ) {
			echo "<p id='login_error' class='message emcl_error'>{$_GET['emc_error']}</p>";
		}

		echo apply_filters('emcl/login_button', '<a href="#" class="css-emcl js-emcl" data-redirect="'.$redirect.'"><div>'. __('Sign in with Emercoin ID', 'emcl') .'<img data-no-lazy="1" src="'.site_url('/wp-includes/js/mediaelement/loading.gif').'" alt="" style="display:none"/></div></a>');
	}

	/**
	 * Main function that handles user login/registration
	 */
	public function login_or_register_user() {

		if (array_key_exists('code', $_REQUEST) && array_key_exists('state', $_REQUEST) && !array_key_exists(
		        'error',
		        $_REQUEST
		    )
		) {
		    $connect = $this->opts['emc_token_page'];

		    $opts = [
		        'http' => [
		            'method' => 'POST',
		            'header' => join(
		                "\r\n",
		                [
		                    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
		                    'Accept-Charset: utf-8;q=0.7,*;q=0.7',
		                ]
		            ),
		            'content' => http_build_query(
		                [
		                    'code'          => $_REQUEST['code'],
		                    'client_id'     => $this->opts['emc_client_id'],
		                    'client_secret' => $this->opts['emc_app_secret'],
		                    'grant_type'    => 'authorization_code',
		                    'redirect_uri'  => $this->redirect_uri,
		                ]
		            ),
		            'ignore_errors' => true,
		            'timeout'       => 10,
		        ],
		        'ssl' => [
		            "verify_peer"      => false,
		            "verify_peer_name" => false,
		        ],
		    ];


		    $response = @file_get_contents($connect, false, stream_context_create($opts));
		    $response = json_decode($response, true);

		    if (!array_key_exists('error', $response)) {
		        $infocard_url = $this->opts['emc_infocard'];
		        $infocard_url .= '/'.$response['access_token'];
		        $opts = [
		            'http' => [
		                'method'        => 'GET',
		                'ignore_errors' => true,
		                'timeout'       => 10,
		            ],
		            'ssl' => [
		                "verify_peer"      => false,
		                "verify_peer_name" => false,
		            ],
		        ];
		        $info = @file_get_contents($infocard_url, false, stream_context_create($opts));
		        $info = json_decode($info, true);

				$emc_user = [
	                'emc_user_id'   => strtolower($info['SSL_CLIENT_M_SERIAL']),
	                'email'         => isset($info['infocard']['Email'])     ? $info['infocard']['Email']     : '',
	                'first_name'    => isset($info['infocard']['FirstName']) ? $info['infocard']['FirstName'] : '',
	                'last_name'     => isset($info['infocard']['LastName'])  ? $info['infocard']['LastName']  : '',
	                'alias'         => isset($info['infocard']['Alias'])     ? $info['infocard']['Alias']     : '',
	            ];

				if ( empty( $emc_user['emc_user_id'] ) )
					header('Location: ' . wp_login_url() . '?emc_error=' . urlencode(__('Invalid User')) );

				// Map our response fields to the correct user fields as found in wp_update_user
				$user = apply_filters( 'emcl/user_data_login', array(
					'emc_user_id' => $emc_user['emc_user_id'],
					'user_pass'   => wp_generate_password(),
				));

				do_action( 'emcl/before_login', $user);

				$user_obj = $this->getUserBy( $user );

				$meta_updated = false;

				if ( $user_obj ) { // LOGIN
					$user_id = $user_obj->ID;

					// check if user email exist or update accordingly
					if( empty( $user_obj->user_email ) && isset($emc_user['user_email']) )
						wp_update_user( array( 'ID' => $user_id, 'user_email' => $emc_user['user_email'] ) );

				} else { // REGISTER USER
					if( ! get_option('users_can_register') && apply_filters( 'emcl/registration_disabled', true ) )
						header('Location: ' . wp_login_url() . '?emc_error=' . urlencode(__('User registration is disabled')) );

					$new_user = apply_filters( 'emcl/user_data_login', array(
						'emc_user_id' => $emc_user['emc_user_id'],
						'first_name'  => $emc_user['first_name'],
						'last_name'   => $emc_user['last_name'],
						'user_pass'   => wp_generate_password(),
					));

					// generate a new username
					$new_user['user_login'] = apply_filters( 'emcl/generateUsername', $this->generateUsername( $emc_user ) );

					// does email from certificate already exist in DB?
					$user_data = get_user_by('email', $emc_user['email']);

					if ($user_data || empty($emc_user['email'])) {
						// generate email
						$new_user['user_email']  = "{$new_user['user_login']}@emercoinid.local";

						// check if somehow this email is already in use
						$user_data = get_user_by('email', $emc_user['email']);
						while ($user_data) {
							// "generate" unique suffix
							global $wpdb;
							$suffix = $wpdb->get_var( $wpdb->prepare(
								"SELECT 1 + SUBSTR(user_login, %d) FROM $wpdb->users WHERE user_login REGEXP %s ORDER BY 1 DESC LIMIT 1",
								strlen( $username ) + 2, '^' . $username . '(-[0-9]+)?$' ) );

							$suffix  = !empty($suffix) ? $suffix : mt_rand(12547, 31086);
							$suffix .= mt_rand(947, 186);
							$new_user['user_email'] = "{$new_user['user_login']}-{$suffix}@emercoinid.local";

							// check whether new email is unique again and again
							$user_data = get_user_by('email', $new_user['user_email']);
						}
					} else {
						// use email from certificate
						$new_user['user_email']  = $emc_user['email'];
					}

					$user_id = $this->register_user( apply_filters( 'emcl/user_data_register', $new_user ) );

					if( !is_wp_error( $user_id ) ) {
						$this->notify_new_registration( $user_id );
						update_user_meta( $user_id, '_emc_user_id', $new_user['emc_user_id'] );
						$meta_updated = true;
					} else {
						header('Location: ' . wp_login_url() . '?emc_error=' . urlencode(__('User cannot be registered: ') . $user_id->get_error_message() ) );
					}
				}

				if( is_numeric( $user_id ) ) {
					wp_set_auth_cookie( $user_id, true );

					if( !$meta_updated )
						update_user_meta( $user_id, '_emc_user_id', $user['emc_user_id'] );

					do_action( 'emcl/after_login', $user, $user_id);
					header('Location: ' . get_admin_url());

				} else {
					die(var_export($user_id, true));
					header('Location: ' . wp_login_url() . '?emc_error=' . urlencode(__('Cookie auth failed')) );
				}

		    } else {
		    	header('Location: ' . wp_login_url() . '?emc_error=' . urlencode($response['error_description']) );
		    }

		} else {
	    	header('Location: ' . wp_login_url() . '?emc_error=' . urlencode($_REQUEST['error_description']) );
		}
	}

	/**
	 * Register new user
	 * @param $user Array of user values captured in EMC certificate
	 *
	 * @return int user id
	 */
	private function register_user( $user ) {
		do_action( 'emcl/register_user', $user );
		return wp_insert_user( $user );
	}

	/**
	 * Function to send ajax response in script
	 * @param $status
	 */
	private function ajax_response( $status ) {
		wp_send_json( $status );
		die();
	}

	/**
	 * Try to retrieve an user by email or username
	 *
	 * @param $user array of username and pass
	 *
	 * @return false|WP_User
	 */
	private function getUserBy( $user ) {

		// if the user is logged in, pass current user
		if( is_user_logged_in() )
			return wp_get_current_user();

		$user_data = get_user_by('email', $user['user_email']);

		if( ! $user_data ) {
			$users     = get_users(
				array(
					'meta_key'    => '_emc_user_id',
					'meta_value'  => $user['emc_user_id'],
					'number'      => 1,
					'count_total' => false
				)
			);
			if( is_array( $users ) )
				$user_data = reset( $users );
		}
		return $user_data;
	}

	/**
	 * Generated a friendly username for emc users
	 * @param $user
	 *
	 * @return string
	 */
	private function generateUsername( $user ) {
		global $wpdb;

		do_action( 'emcl/generateUsername', $user );

		if( !empty( $user['first_name'] ) && !empty( $user['last_name'] ) )
			$username = $this->cleanUsername( trim( $user['first_name'] ) .'-'. trim( $user['last_name'] ) );

		if( ! validate_username( $username ) ) {
			$username = '';
			// use email
			$email    = explode( '@', $user['email'] );
			if( validate_username( $email[0] ) )
				$username = $this->cleanUsername( $email[0] );
		}

		// User name can't be on the blacklist or empty
		$illegal_names = get_site_option( 'illegal_names' );
		if ( empty( $username ) || in_array( $username, (array) $illegal_names ) ) {
			// we used all our options to generate a nice username. Use random id instead
			$username = 'emcid_' . $this->generateSuffix();
			while ( get_user_by('login', $username) ) {
				// guarantee of unique login
				$username = 'emcid_' . $this->generateSuffix();
			}
		}

		// "generate" unique suffix
		$suffix = $wpdb->get_var( $wpdb->prepare(
			"SELECT 1 + SUBSTR(user_login, %d) FROM $wpdb->users WHERE user_login REGEXP %s ORDER BY 1 DESC LIMIT 1",
			strlen( $username ) + 2, '^' . $username . '(-[0-9]+)?$' ) );

		if( !empty( $suffix ) ) {
			$username .= "-{$suffix}";
		}
		return apply_filters( 'emcl/generateUsername', $username );
	}

	/**
	 * Generate a 5 chars string [a-z0-9]
	 *
	 * @return string
	 */
	private function generateSuffix() {
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$suffix     = '';

		for ($i = 0; $i < 5; $i++) {
			$suffix .= $characters[mt_rand(0, 35)];
		}

		return $suffix;
	}

	/**
	 * Simple pass sanitazing functions to a given string
	 * @param $username
	 *
	 * @return string
	 */
	private function cleanUsername( $username ) {
		return sanitize_title( str_replace('_','-', sanitize_user(  $username  ) ) );
	}

	/**
	 * Send notifications to admin and bp if active
	 * @param $user_id
	 */
	private function notify_new_registration( $user_id ) {
		// Notify the site admin of a new user registration.
		wp_new_user_notification( $user_id,'','admin' );
		// notify the user
		wp_new_user_notification( $user_id,'','user' );
		do_action( 'emcl/notify_new_registration', $user_id );
		// bp notifications
		// fires xprofile_sync_wp_profile, bp_core_new_user_activity, bp_core_clear_member_count_caches
		do_action( 'bp_core_activated_user', $user_id );
	}

	/**
	 * Add EMC button if user is not logged
	 */
	public function add_emcl_button() {
		if( ! is_user_logged_in() )
			do_action( 'emc_login_button' );
	}

	/**
	 * Add extra section on Bp Settings Area
	 */
	public function profile_buttons( ) {
		$current_user = wp_get_current_user();

		if( ! isset( $current_user->ID ) )
			return;
		?>
		<div id="emcl_connection">
			<label for="emcl_connection"><?php _e("Emercoin ID", 'emcl'); ?></label>
			<table class="form-table">
				<tbody>
					<tr>
						<th>_e( 'Emercoin SSL Certificate ID', 'emcl');</th>
						<td>
						<?php
							$user_meta = get_user_meta( $current_user->ID, '_emc_user_id' );

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
		</div><?php
	}

	/**
	 * Check if disconnect button was pressed
	 *
	 * @return bool
	 */
	public function disconnect_emc( ) {
		$current_user = wp_get_current_user();
		if( ! isset( $current_user->ID ) )
			return;

		if ( !current_user_can( 'edit_user', $current_user->ID ) || ! isset( $_GET['emcl_disconnect'] ) )
			return;

		delete_user_meta( $current_user->ID, '_emc_user_id' );
		// refresh page
		wp_redirect( esc_url( $_GET['redirect'] ) );
		exit();
	}
}
