<?php

/**
 * Widget - WP Socialite
 *
 * @since 0.1
 */

if ( ! class_exists( 'Q_WP_Socialite' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_WP_Socialite");'));

    class Q_WP_Socialite extends WP_Widget 
    {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
            parent::__construct(
                'q_wp_socialite', // Base ID
                __('Q - Socialite','q-textdomain'), // Name
                array( 'description' => __( 'Share your content with social networks - plugin required.', 'q-textdomain' ), ) // Args
            );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
            
            extract( $args );
            $title = apply_filters( 'widget_title', $instance['title'] );
            
            // add share class to sidepanel ##
            $before_widget = str_replace('class="', 'class="share ', $before_widget);
            
            echo $before_widget;
            if ( !empty( $title ) )
                
                #if ( q_settings::wpsocialite === TRUE ) { // check defined settings ##

                    // check if wpsocialite plugin is active
                    if ( q_plugin_is_active( 'wpsocialite/wpsocialite.php' ) ) { 

                        // load global variables ##
                        global $wpsocialite; 
                        $value = get_option('wpsocialite_style'); // pass setting size to widget ##
                        #echo 'VALUE: '.$value;

                    ?>
                            <?php echo $before_title; ?><?php _e( $title, 'q-textdomain' ); ?><?php echo $after_title; ?>
                            <?php #echo $wpsocialite->wpsocialite_markup($value); ?>
                            <?php wpsocialite_markup($value); ?>
                    <?php

                    } else { // issue error ##

                        q_plugin_warning( "WPSocialite", "Socialite", "wpsocialite/wpsocialite.php" );

                    } // plugin active ##

                #} // check defined settings ##
                
            echo $after_widget;
            
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['title'] = strip_tags( $new_instance['title'] );

            return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
            if ( isset( $instance[ 'title' ] ) ) {
                $title = $instance[ 'title' ];
            }
            else {
                $title = __( 'Share', 'q-textdomain' );
            }
            ?>
            <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <?php 
	}

    } // class q_Latest_Images

}