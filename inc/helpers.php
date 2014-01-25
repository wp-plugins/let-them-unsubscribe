<?php
/**
 * Get the plugin settings
 */
function iw_ltu_get_settings() {
	$defaults = array(
		'roles' => array(),
		'info-text' => __( 'Attention: All your data will be deleted!', IW_LTU_LANG_DOMAIN ),
		'end-text' => __( 'We will miss you', IW_LTU_LANG_DOMAIN )
	);

	$settings = get_option( 'lt_unsubscribe_options', array() );

	return wp_parse_args( $settings, $defaults );
}

/**
 * Get the roles that can be erased
 */
function iw_ltu_get_roles() {
	global $wp_roles;

	if ( $wp_roles == null )
		$wp_roles = new WP_Roles();

	$roles = $wp_roles->roles;
	unset( $roles['administrator'] );

	return apply_filters( 'ltu_settings_roles', $roles );
}

/**
 * Check if the user can unsubscribe by himself
 */
function iw_ltu_user_can_unsubscribe( $user_id = 0 ) {
	if ( ! absint( $user_id ) )
		$user = wp_get_current_user();
	else
		$user = get_userdata( $user_id );

	if ( empty( $user ) )
		return false;

	$can_unsubscribe = false;
	$settings = iw_ltu_get_settings();
	$user_roles = $user->roles;

	foreach ( $user_roles as $key => $role ) {
		if ( in_array( $role, $settings['roles'] ) ) {
			$can_unsubscribe = true;
			break;
		}

	}

	return $can_unsubscribe;
}