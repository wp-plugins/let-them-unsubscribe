<?php

class IW_Unsubscribe_Unsubscribe_Page {

	private static $instance = null;
	private $options = null;
	private $menu_slug = 'ltu_unsubscribe';

	/**
	 * Constructor
	 * @param array Let Them Unsubscribe options already saved in DB
	 */
	public function __construct( $options ) {

		$this -> page_id = add_users_page( __( 'Delete your account', 'lt_unsubscribe' ), __( 'Delete your account', 'lt_unsubscribe' ), 'read', $this -> menu_slug, array( $this,'unsubscribe_page_html' ) );

		$this -> options = $options;

		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Checks if the user has clicked the button to delete its account
		add_action( 'admin_init', array( $this, 'delete_user_account' ) );

    } // end __construct

    /**
     * If the user has enough permissions, returns an instance of the Unsubscribe Page
     */
    public static function get_instance()
    {

    	$options = get_option( 'lt_unsubscribe_options' );

    	$user = get_user_by( 'id', get_current_user_id() );
		$user_role = $user -> roles[0];

        if ( self::$instance === null && isset( $options['roles'][$user_role] ) && $options['roles'][$user_role] )
            self::$instance = new IW_Unsubscribe_Unsubscribe_Page( $options );

        return self::$instance;

    } // end get_instance

    /**
     * Unsubscribe Page output
     */
    public function unsubscribe_page_html() {

    	if ( !current_user_can( 'read' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'lt_unsubscribe' ) );

		?>
			<div class="wrap">
				
				<?php screen_icon(); ?>

				<h2><?php _e( 'Delete your account', 'lt_unsubscribe' ); ?></h2>
				
				<?php settings_errors( 'unsubscribe_error_settings' ); ?>
				
				<form id="iw-user-unsubscribe" action="" method="post" >
					
					<p class="unsubscribe-text">

						<?php echo nl2br( $this -> options['info-text'] ); ?>

					</p>		

					<?php wp_nonce_field( 'delete-account', '_wpnonce' ); ?>
					<input type="hidden" name="iw-ltu-delete-action" value="true"></input>

					<?php submit_button( __( 'I want to delete my account', 'lt_unsubscribe' ), 'delete' ) ?>
					
				</form>
				
			</div>

		<?php

    } // end unsubscribe_page_html

	/**
     * Admin scripts for Unsubscribe Page
     */
    public function register_admin_scripts() {
	
		if ( get_current_screen() -> id == $this -> page_id ) {
			
			wp_enqueue_script( 'admin-scripts', plugins_url( IW_LTU_PLUGIN_NAME . '/js/admin.js' ) );
			
			$data = array( 'areusure' => __( 'Are you sure?' ), 'endtext' => $this -> options['end-text'] );
			wp_localize_script( 'admin-scripts', 'data_object', $data );

		}
	
	} // end register_admin_scripts


	/**
     * Deletes a user account and redirects to Home URL
     */
	public function delete_user_account() {

		if ( isset( $_POST['iw-ltu-delete-action'] ) && 'true' == $_POST['iw-ltu-delete-action'] && wp_verify_nonce( $_POST['_wpnonce'], 'delete-account' ) ) {

			global $current_user;
			$current_user_id = $current_user -> data -> ID;

			wp_delete_user( $current_user_id );
			wp_logout();

			wp_redirect( home_url() );

		}

	} // end delete_user_account


}

?>