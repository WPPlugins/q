<?php

/**
 * Widget - Social Connect
 *
 * @since 0.1
 */


if ( ! class_exists( 'Q_Widget_Social_Connect' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action( 'widgets_init', create_function( '', 'return register_widget("Q_Widget_Social_Connect");' ) );
    
    class Q_Widget_Social_Connect extends WP_Widget 
    {

        /**
         * Class Constructor
         */
        public function __construct(  ) {
            
            // call it directly ##
            $this->social_connect();
            
        }

        
        /**
         * Do Social Connecting
         */
        public function social_connect()
        {
            
            // check if wpsocialite plugin is active
            if ( q_plugin_is_active( 'social-connect/social-connect.php' ) ) { 

                // only required if the user is not logged in ##
                if ( !is_user_logged_in() ) {

                    do_action( 'social_connect_form' );

                } // user login check ##    

            } else { // isser error ##

                q_plugin_warning( "Social Connect", "Social Connect", "social-connect/social-connect.php" );

            } // plugin active ##
            
        }
        

    }
    
}