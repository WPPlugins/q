<?php

/**
 * Actions to call on after_switch_theme() hook
 *
 * @since 0.4
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_Init' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'init', array ( 'Q_Hook_Init', 'init' ), 0 );
    
    // Declare Class
    class Q_Hook_Init extends Q
    {

        // private property ##
        public $widgets_add_default = array();
        
        
        /**
        * Creates a new instance.
        *
        * @wp-hook      init
        * @see          __construct()
        * @since        0.1
        * @return       void
        */
        public static function init() 
        {
            new self;
        }
        
        
        /**
         * Class Constructor
         */
        private function __construct()
        {
            
            // stack up the default widgets ##
            add_action( 'widgets_init', array ( $this, 'widgets_add_default' ), 1 );
            
            // define widgets to add ##
            add_action( 'widgets_init', array ( $this, 'widgets_add' ), 2 );
            
            // add defined widgets ##
            add_action( 'widgets_init', array ( $this, 'do_widgets_add' ), 3 );
            
            // define widgets to remove ##
            add_action( 'widgets_init', array ( $this, 'widgets_remove' ), 1 );
            
            // remove defined widgets ##
            add_action( 'widgets_init', array ( $this, 'do_widgets_remove' ), 2 );
            
        }
        
        
        /**
         * Default list of widgets to activate
         * 
         * @since       1.2.0
         * @return      void
         */
        public function widgets_add_default()
        {
            
            $this->widgets_add_default = array(
            
                #    'q_widget_latest_images' => 'Q_Widget_Latest_Images.class.php' // Latest Images ##
                #,   'q_widget_wp_socialite' => 'Q_Widget_WP_Socialite.class.php' // WP Socialite Plugin ##
                    'q_widget_follow' => 'Q_Widget_Follow.class' // Simple Follow ##
                ,   'q_widget_mailchimp' => 'Q_Widget_Mailchimp.class' // MailChimp ##
                ,   'q_widget_sidebar_blog' => 'Q_Widget_Sidebar_Blog.class' // Sidebar Blog ##
                ,   'q_widget_sidebar_general' => 'Q_Widget_Sidebar_General.class' // Sidebar General ##
                #,   'q_widget_hero' => 'Q_Widget_Hero.class.php' // Hero ##
                #,   'q_widget_breadcrumb' => 'Q_Widget_Breadcrumb.class.php' // Breadcrumb ##
                ,   'q_widget_twitter' => 'Q_Widget_Twitter.class' // Twitter ##
                ,   'q_widget_post_filter' => 'Q_Widget_Post_Filter.class' // Post Filter ##
                ,   'q_widget_flickr' => 'Q_Widget_Flickr.class' // Flickr ##
                ,   'q_widget_facebook_share' => 'Q_Widget_Facebook_Share.class' // Facebook Share ##
                          
            );
            
        }
        
        
        
        /**
         * Add Widgets
         * 
         * See list to define which to add
         * 
         * @since       1.0
         * @return      void
         */
        public function widgets_add( $widgets )
        {
            
            // build list of widgets to add ##
            $this->widgets_add = array();
            
            // add default widgets ##
            $this->widgets_add = array_merge( $this->widgets_add_default, $this->widgets_add );
            
            // add each seleted widget to the load list ##
            if ( $widgets || is_array( $widgets ) ) { 
            
                // merge extra widgets ##
                $this->widgets_add = array_merge( $this->widgets_add, $widgets );
                
            }
                
            // let's remove duplicate keys - taking the used added ones over the default ##
            
            
        }
        
        
        /**
         * Do Add Widgets
         * 
         * @since       1.0
         * @return      void
         */
        public function do_widgets_add()
        {
            
            // sanity check ##
            if ( ! $this->widgets_add || ! is_array( $this->widgets_add ) ) { return false; }
            
            // test ##
            #wp_die( pr( $this->widgets_add ) );
            
            // add each seleted widget to the load list ##
            foreach ( $this->widgets_add as $key => $value ) {
                
                q_locate_template( "widget/{$value}.php", false, true );
                    
            }
            
        }
        
        
        /**
         * Remove Widgets
         * 
         * See list to define which to remove
         * 
         * @since       1.0
         * @return      void
         * @todo        Handle extra removals
         */
        public function widgets_remove( $widgets ) {
            
            // build our list of default widgets to remove ##
            $this->widgets_remove = array(
                
                'WP_Widget_Pages' // Pages ##
                /*
                ,'WP_Widget_Search' // Search ##
                ,'WP_Widget_Calendar'
                ,'WP_Widget_Archives'
                ,'WP_Widget_Links'
                ,'WP_Widget_Meta'
                ,'WP_Widget_Text'
                ,'WP_Widget_Categories'
                ,'WP_Widget_Recent_Posts'
                ,'WP_Widget_Recent_Comments'
                ,'WP_Widget_RSS'
                ,'WP_Widget_Tag_Cloud'
                */
                
            );
            
            // handle extra removals ##
            if ( $widgets && is_array( $widgets ) ) {
                
                $this->widgets_remove = array_merge( $widgets, $this->widgets_remove );
                
            }

        }
        
        
        /**
         * Do Remove Widgets
         * 
         * @since       1.0
         * @return      void
         */
        public function do_widgets_remove()
        {
            
            #wp_die( pr($this->widgets_remove) );
            if ( ! $this->widgets_remove || ! is_array( $this->widgets_remove ) ) { return false; }
            
            foreach ( $this->widgets_remove as $remove ) {
                
                unregister_widget( $remove );
            
            }
            
        }
        
        
    }
    
}

