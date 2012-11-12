<?php

class IW_Unsubscribe_Options_Page {


	private static $instance = null;
	private $page_id = null;
	private $options = array();
	private $options_name = 'lt_unsubscribe_options';
	private $menu_slug = 'ltu_options';
	private $options_error_settings = 'ltu_errors_settings';
	private $roles = array();
    
    /**
     * Constructor
     */
    public function __construct() {
    	
    	$this -> set_roles();
    	
		$this -> page_id = add_users_page( __( 'Let Them Unsubscribe options', 'lt_unsubscribe' ), __( 'Let Them Unsubscribe options', 'lt_unsubscribe' ), 'delete_users', $this -> menu_slug, array( $this,'options_page_html' ) );

		$this -> options = get_option( $this -> options_name );

		if ( ! $this -> options )
			$this -> options = $this -> get_default_options();

		add_action( 'admin_init', array( $this, 'options_page_init' ) );

    } // end __construct


    /**
     * Returns an instance of the Options Page
     * @return IW_Unsubscribe_Options_Page instance
     */
    public static function get_instance()
    {

        if (self::$instance === null)
            self::$instance = new IW_Unsubscribe_Options_Page();

        return self::$instance;

    } // end get_instance


    /**
     * Returns the default options
     * @return array
     */
    private function get_default_options() {

    	$arr_roles = array();
    	foreach ( $this -> roles as $role ) {
    		$arr_roles[$role['slug']] = false;
    	}

    	return array(
    		'roles' => $arr_roles,
			'info-text' => __( 'Attention: All your data will be deleted!', 'lt_unsubscribe' ),
			'end-text' => __( 'We will miss you', 'lt_unsubscribe' )
		);

    } // end get_default_options


    /**
     * Gets all WP roles and save them in as array.
     */
    private function set_roles() {

    	$wp_roles = get_editable_roles();

    	foreach ( $wp_roles as $key => $value ) {

    		if ( 'administrator' != $key )
    			$this -> roles[] = array( 'slug' => $key, 'name' => $value['name'] );

    	}

    } // end set_roles


    /**
     * Add settings sections and fields
     */
    public function options_page_init() {

    	register_setting( $this -> options_name, $this -> options_name, array( $this, 'sanitize_options' ) );

		add_settings_section( 'ltu_general_options', __( 'Users who can delete their profiles', 'lt_unsubscribe' ), array( $this, 'ltu_general_options' ), $this -> menu_slug );

		add_settings_field( 'ltu_general_options_users_select', __( 'Select one or more users', 'lt_unsubscribe' ), array( $this, 'ltu_general_options_users_select' ), $this -> menu_slug, 'ltu_general_options' );


		add_settings_section( 'ltu_texts_options', __( 'Texts', 'lt_unsubscribe' ), array( $this, 'ltu_texts_options' ), $this -> menu_slug );

		add_settings_field( 'ltu_texts_options_info_text', __( 'Text showed on Delete Profile Page', 'lt_unsubscribe' ), array( $this, 'ltu_texts_options_info_text' ), $this -> menu_slug, 'ltu_texts_options' );

		add_settings_field( 'ltu_texts_options_end_text', __( 'Text showed once the user has deleted his profile', 'lt_unsubscribe' ), array( $this, 'ltu_texts_options_end_text' ), $this -> menu_slug, 'ltu_texts_options' );



    } // end options_page_init


    /**
     * Options Page output
     */
    public function options_page_html() {

    	if ( !current_user_can( 'edit_users' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'lt_unsubscribe' ) );
		
		?>
			<div class="wrap">
				
				<?php screen_icon(); ?>

				<h2><?php _e( 'Let Them Unsubscribe options', 'lt_unsubscribe' ); ?></h2>
				
				<?php settings_errors( 'options_error_settings' ); ?>
				
				<form id="iw-user-options" action="options.php" method="post" >
				
					<?php
						settings_fields( $this -> options_name );
						do_settings_sections( $this -> menu_slug );
					?>
									
					<p class="submit">
						<input id="iw-submit-users-options-button" name="ltu_users_options[submit]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'lt_unsubscribe'); ?>" />
						<input name="ltu_users_options[reset]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'lt_unsubscribe'); ?>" />		
					</p>
					
				</form>
				
			</div>
		<?php

    } // end options_page_html


    /**
     * General options section
     */
    public function ltu_general_options() {

    	?>

    	<p><?php _e('Manage which users roles can delete their profiles', 'lt_unsubscribe' ); ?></p>

    	<?php

    } // end ltu_general_options

    /**
     * User roles checkboxes fields
     */
	public function ltu_general_options_users_select() {

		// Save the default roles names for translation 
		$default_roles = array(
			'editor' => __( 'Editor', 'lt_unsubscribe' ),
			'author' => __( 'Author','lt_unsubscribe' ),
			'contributor' => __( 'Contributor','lt_unsubscribe' ),
			'subscriber' => __( 'Subscriber','lt_unsubscribe' )
		);

		foreach ( $this -> roles as $role ): ?>

			<label for="user_<?php echo $role['slug']; ?>">
				<input name="<?php echo $this -> options_name; ?>[user_<?php echo $role['slug'] ?>]" type="checkbox" id="user_<?php echo $role['slug'] ?>" value="editor" <?php if ($this -> options['roles'][$role['slug']]) echo 'checked'; ?>> <?php echo ( isset( $default_roles[$role['slug']] ) ) ? $default_roles[$role['slug']] : $role['name']; ?></input>
			</label><br/>

		<?php endforeach;

	} // end ltu_general_options_users_select


	/**
     * Texts options section
     */
	public function ltu_texts_options() {
	} // end ltu_texts_options


	/**
     * Info text field
     */
	public function ltu_texts_options_info_text() {

		?>
	
			<textarea name="<?php echo $this -> options_name; ?>[info-text]" class="large-text" id="info-text" rows="5" cols="30"><?php echo esc_textarea( $this -> options['info-text'] ); ?></textarea>
		
		<?php

	} // end ltu_texts_options_info_text


	/**
     * Text showed when user deletes its account field.
     */
	public function ltu_texts_options_end_text() {

		?>
	
			<textarea name="<?php echo $this -> options_name; ?>[end-text]" class="large-text" id="end-text" rows="5" cols="30"><?php echo esc_textarea( $this -> options['end-text'] ); ?></textarea>
		
		<?php

	} // end ltu_texts_options_end_text


	/**
     * Data validation
     * @param array form data
     * @return array sanitized data
     */
    public function sanitize_options( $input ) {

    	$valid_input = $this -> get_default_options();

    	$submit = ( ! empty( $input['submit'] ) );
		$reset = ( ! empty( $input['reset'] ) );

		foreach ( $this -> roles as $role ) {

			if ( isset( $input['user_' . $role['slug']] ) )
				$valid_input['roles'][$role['slug']] = true;
			else 
				$valid_input['roles'][$role['slug']] = false;

		}

		$valid_input['info-text'] = esc_textarea( $input['info-text'] );
		$valid_input['end-text'] = esc_textarea( $input['end-text'] );


		return $valid_input;

    } // end sanitize_options

    


}


?>