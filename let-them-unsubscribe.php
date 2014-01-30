<?php
/*
Plugin Name: Let Them Unsubscribe
Plugin URI: 
Description: Let the users delete their accounts 
Version: 1.1.1
Author: igmoweb
Author URI: http://www.igmoweb.com  
*/


class IW_LTU {

	public $settings_menu;
	public $unsubscribe_menu;

	/**
	 * Constructor
	 */
	function __construct() {

		$this->set_globals();
		$this->includes();
		
		// load plugin text domain
		add_action( 'plugins_loaded', array( &$this, 'load_text_domain' ) );

		// Adds an options menu
		add_action( 'init', array ( $this, 'init_plugin' ) );

		register_activation_hook( __FILE__, array( &$this, 'activate' ) );

	} // end constructor

	/**
	 * Set constants needed for the plugin
	 */
	private function set_globals() {
		// Basename
		define( 'IW_LTU_PLUGIN_BASENAME', plugin_dir_path( __FILE__ ) );
		
		// Includes dir
		define( 'IW_LTU_INCLUDES_DIR', IW_LTU_PLUGIN_BASENAME . 'inc/' );

		// Admin dir
		define( 'IW_LTU_ADMIN_DIR', IW_LTU_PLUGIN_BASENAME . 'admin/' );

		// Language domain
		define( 'IW_LTU_LANG_DOMAIN', 'let-them-unsubscribe' );

		define( 'IW_LTU_VERSION', '1.1' );
	}

	/**
	 * Include basic files for the plugin
	 */
	private function includes() {
		if ( is_admin() ) {
			include_once( IW_LTU_INCLUDES_DIR . 'admin-page.php' );
			include_once( IW_LTU_INCLUDES_DIR . 'helpers.php' );
			include_once( IW_LTU_ADMIN_DIR . 'settings-menu.php' );
			include_once( IW_LTU_ADMIN_DIR . 'user-profile.php' );
		}
	}

	public function activate() {
		$settings = iw_ltu_get_settings();
		add_option( 'lt_unsubscribe_options', $settings, '', 'no' );
	}
	
	
	/**
	 * Load the plugin text domain and MO files
	 * 
	 * These can be uploaded to the main WP Languages folder
	 * or the plugin one
	 */
	public function load_text_domain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), IW_LTU_LANG_DOMAIN );

		load_textdomain( IW_LTU_LANG_DOMAIN, WP_LANG_DIR . '/' . IW_LTU_LANG_DOMAIN . '/' . IW_LTU_LANG_DOMAIN . '-' . $locale . '.mo' );
		load_plugin_textdomain( IW_LTU_LANG_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Initializes the plugin
	 */
	public function init_plugin() {

		if ( is_admin() ) {
			$args = array(
				'parent' => 'options-general.php',
				'menu_title' => __( 'Let Them Unsubscribe', IW_LTU_LANG_DOMAIN ),
				'page_title' => __( 'Let Them Unsubscribe Settings', IW_LTU_LANG_DOMAIN ),
				'forbidden_message' => __( 'You do not have enough permissions to access to this page', IW_LTU_LANG_DOMAIN ),
			);
			$this->settings_menu = new IW_LTU_Settings_Menu( 'ltu_settings', 'manage_options', $args );

			
			if ( iw_ltu_user_can_unsubscribe() ) {
				include_once( IW_LTU_ADMIN_DIR . 'unsubscribe-menu.php' );
				$args = array(
					'parent' => 'users.php',
					'menu_title' => __( 'Delete your account', IW_LTU_LANG_DOMAIN ),
					'page_title' => __( 'Delete your account', IW_LTU_LANG_DOMAIN ),
					'forbidden_message' => __( 'You do not have enough permissions to access to this page', IW_LTU_LANG_DOMAIN ),
				);
				$this->unsubscribe_menu = new IW_LTU_Unsubscribe_Menu( 'ltu_unsubscribe', 'read', $args );
			} 
		}

		$current_version = get_option( 'iw_ltu_version', '1.0' );
		if ( IW_LTU_VERSION != $current_version ) {
			// Upgrade the plugin
			include_once( IW_LTU_INCLUDES_DIR . 'upgrade.php' );
		}

	}

  
} // end class

global $iw_ltu;
$iw_ltu = new IW_LTU();