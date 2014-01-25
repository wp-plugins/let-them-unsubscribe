<?php

if ( version_compare( $current_version, '1.1', '<' ) ) {
	$settings = iw_ltu_get_settings();
	$roles = $settings['roles'];
	foreach ( $roles as $role => $value ) {
		if ( $value === false )
			unset( $roles[ $role] );
	}

	delete_option( 'lt_unsubscribe_options' );
	add_option( 'lt_unsubscribe_options', $settings, '', 'no' );

	update_option( 'iw_ltu_version', '1.1' );
}