<?php

/**
 * Class that handle all admin notices
 *
 * @since      1.0.4.1
 * @package    Emercoin_ID
 * @subpackage Emercoin_ID/includes
 */
class Emcl_Notices {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.4.1
	 */
	public function __construct() {
		if( isset( $_GET['emcl_notice'])){
			update_option('emcl_'.esc_attr($_GET['emcl_notice']), true);
		}
	}
}
