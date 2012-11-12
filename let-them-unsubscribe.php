<?php
/*
Plugin Name: Let Them Unsubscribe
Plugin URI: 
Description: Let the users delete their accounts 
Version: 1.0
Author: Ignacio Cruz
Author URI: http://www.igmoweb.com

Developed with Plugin Wordpress Boilerplate by Tom McFarlin: https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate
  
*/

define( 'IW_LTU_PLUGIN_BASENAME', dirname( __FILE__ ) );
define( 'IW_LTU_ADMIN_PAGES_DIR', IW_LTU_PLUGIN_BASENAME . '/views' );
define( 'IW_LTU_PLUGIN_NAME', 'let-them-unsubscribe' );

class IW_LTU {

	/**
	 * Constructor
	 */
	function __construct() {
		
		// load plugin text domain
		add_action( 'init', array( $this, 'textdomain' ) );

		// Adds an options menu
		add_action( 'admin_menu', array ( $this, 'add_menus' ) );

	} // end constructor
	
	
	/**
	 * Loads the plugin text domain for translation
	 */
	public function textdomain() {

		load_plugin_textdomain( 'lt_unsubscribe', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	} // end textdomain
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
	/**
	 * Adds admin menus
	 */
	public function add_menus() {

		if ( is_user_logged_in() ) {

			include_once( IW_LTU_ADMIN_PAGES_DIR . '/options.php' );
			$options_page = IW_Unsubscribe_Options_Page::get_instance();

			include_once( IW_LTU_ADMIN_PAGES_DIR . '/unsubscribe.php' );
			$options_page = IW_Unsubscribe_Unsubscribe_Page::get_instance();

		}

	} // end add_menus
  
} // end class

$plugin_name = new IW_LTU();