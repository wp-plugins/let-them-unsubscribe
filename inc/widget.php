<?php

class LTU_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function LTU_Widget() {
        $widget_ops = array( 'classname' => 'ltu-widget', 'description' => __( 'Displays a link that leads to the Unsubscribe Admin Screen', IW_LTU_LANG_DOMAIN ) );
        $this->WP_Widget( 'ltu-widget', 'Let Them Unsubscribe', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {

    	if ( ! is_user_logged_in() )
    		return;
    	extract( $args );

    	extract( $instance );

    	echo $before_widget;
		
		echo $before_title . apply_filters( 'widget_title', $title ). $after_title;
		
		$class = ! empty( $css_class ) ? 'class="' . esc_attr( $css_class ) . '"' : ''; 
		$url = admin_url( 'profile.php' );
		if ( iw_ltu_user_can_unsubscribe() ) {
			$url = add_query_arg( 'page', 'ltu_unsubscribe', $url );
			?><a href="<?php echo esc_url( $url ); ?>" <?php echo $class; ?> title="<?php echo esc_attr( 'Delete your account', IW_LTU_LANG_DOMAIN ); ?>"><?php echo esc_attr( 'Delete your account', IW_LTU_LANG_DOMAIN ); ?></a><?php
		}
		else {
			?><a href="<?php echo esc_url( $url ); ?>" <?php echo $class; ?> title="<?php echo esc_attr( 'Your profile', IW_LTU_LANG_DOMAIN ); ?>"><?php echo esc_attr( 'Your profile', IW_LTU_LANG_DOMAIN ); ?></a><?php	
		}

		echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {

        $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['css_class'] = ( ! empty( $new_instance['css_class'] ) ) ? sanitize_text_field( $new_instance['css_class'] ) : '';

		return $instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        $instance = wp_parse_args( 
        	$instance, 
        	array( 
        		'title' => __( 'Delete your account', IW_LTU_LANG_DOMAIN ), 
        		'css_class' => ''
        	) 
        );
        extract( $instance );
        ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php _e( 'Link CSS class:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'css_class' ); ?>" name="<?php echo $this->get_field_name( 'css_class' ); ?>" type="text" value="<?php echo esc_attr( $css_class ); ?>">
			</p>
        	<p class="description"><?php _e( 'If the user is not allowed to delete its account, the link will lead to its profile page.', IW_LTU_LANG_DOMAIN ); ?></p>
        <?php
    }
}

add_action( 'widgets_init', 'ltu_register_widget' );
function ltu_register_widget() {
	register_widget( 'LTU_Widget' );
}