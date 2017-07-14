<?php

/**
 * Actions attached to API hook - save_post
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_WP_Enqueue_Scripts' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'wp_enqueue_scripts', array ( 'Q_Hook_WP_Enqueue_Scripts', 'init' ), 0 );
    
    // Define Class ##
    class Q_Hook_WP_Enqueue_Scripts extends Q
    {

        
        // property to control Q themes ##
        public static $wp_get_theme = '';
        
        
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
        

        private function __construct()
        {
            
            // get theme info ##
            self::$wp_get_theme = wp_get_theme();
            
            // add Q stylesheet - removable by const parent_css ##
            add_action( 'wp_enqueue_scripts', array ( $this, 'q_enqueue_styles_wordpress' ), 1 );

            // enqueue theme scripts and styles -- the priority loads these in the correct order ##
            add_action( 'wp_enqueue_scripts', array ( $this, 'q_enqueue_scripts_styles' ), 8 );
            
            // add parent stylesheet - removable by Q options configuration ##
            add_action( 'wp_enqueue_scripts', array ( $this, 'q_enqueue_styles_parent' ), 10 );

            // add child stylesheets - if child theme active ##
            add_action( 'wp_enqueue_scripts', array ( $this, 'q_enqueue_styles_child' ), 20 );
            
        }
        
        
        
        /**
         * include framework stylesheet
         */
        public function q_enqueue_styles_wordpress() {

            if ( defined ( 'Q_THEME' ) ) {

                // parent stylesheet ##
                global $q_options; // load framework options ##

                if ( $q_options->framework_css === TRUE ) {

                    wp_register_style( 'q-wordpress', q_locate_template( "css/q.wordpress.css", false ), '', '0.1', 'all' );
                    wp_enqueue_style( 'q-wordpress' );

                }

            }

        }


        /**
         * enqueue css & javascript files ##
         */
        public function q_enqueue_scripts_styles() {

            if ( defined ( 'Q_THEME' ) ) {

                if ( ! is_admin() ) { // probably not required ##

                    global $q_browser; // get browser agent info ##
                    global $q_options; // load framework options ##
                    #wp_die(pr($q_options)); // test options ##

                    // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions ##
                    if ( $q_browser['type'] == 'ie8' || $q_browser['type'] == 'ie7' || $q_browser['type'] == 'ie6' && $q_options->framework_js === TRUE ) {

                        wp_register_script( 'html5', q_locate_template( "javascript/q.html5.js", false ), array(), '0.1', 'all' );
                        wp_enqueue_script( 'html5' );

                    }

                    // add jquery ##
                    // could be loaded from google to improve caching.. ##
                    // http://www.wpbeginner.com/wp-themes/replace-default-wordpress-jquery-script-with-google-library/

                    wp_enqueue_script( "jquery" );

                    // Required for nested reply function that moves reply inline with JS ##
                    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                        wp_enqueue_script( 'comment-reply' ); // enqueue the javascript that performs in-link comment reply fanciness
                    }

                    // bxslider ##
                    if ( isset( $q_options->bxslider ) && $q_options->bxslider === TRUE ) {

                        // jquery bxslider ##
                        wp_register_script( 'jquery-bxslider', q_locate_template( "javascript/jquery.bxslider.js", false ), array('jquery'),'3.0',true );
                        wp_enqueue_script( 'jquery-bxslider' );  

                        wp_register_style( 'q-bxslider', q_locate_template( "css/jquery.bxslider.css", false ), array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-bxslider' );

                    }

                    // slides JS ##
                    if ( isset($q_options->slides) && $q_options->slides === TRUE ) {

                        // jquery bxslider ##
                        wp_register_script( 'jquery-slides-js', q_locate_template( "javascript/jquery.slides.js", false ), array('jquery'),'3.0',true );
                        wp_enqueue_script( 'jquery-slides-js' );  

                        wp_register_style( 'q-slides', q_locate_template( "css/jquery.slides.css", false ), array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-slides' );

                    }
                    
                    
                    // ANOTHER -- slides JS ##
                    if ( isset($q_options->slidesjs) && $q_options->slidesjs === TRUE ) {

                        // jquery bxslider ##
                        wp_register_script( 'jquery-slides-ajs', q_locate_template( "javascript/jquery.slidesjs.js", false ), array('jquery'),'3.0',true );
                        wp_enqueue_script( 'jquery-slides-ajs' );  

                    }
                    
                    
                    // Hashchange - http://benalman.com/projects/jquery-hashchange-plugin/ ##
                    if ( isset($q_options->ba_hashchange) && $q_options->ba_hashchange === TRUE ) {

                        // jquery bxslider ##
                        wp_register_script( 'jquery-ba-hashchange', q_locate_template( "javascript/jquery.ba-hashchange.js", false ), array( 'jquery' ), '3.0', true );
                        wp_enqueue_script( 'jquery-ba-hashchange' );  

                    }
                    
                    
                    // Easy Tabs - http://os.alfajango.com/easytabs/ ##
                    if ( isset($q_options->easy_tabs) && $q_options->easy_tabs === TRUE ) {

                        // jquery bxslider ##
                        wp_register_script( 'jquery-easy-tabs', q_locate_template( "javascript/jquery.easytabs.min.js", false ), array( 'jquery', 'jquery-ba-hashchange' ), '3.2.0', true );
                        wp_enqueue_script( 'jquery-easy-tabs' );  

                    }
                    
                    
                    // Code Prettyfier - https://code.google.com/p/google-code-prettify/ ##
                    if ( isset($q_options->prettify) && $q_options->prettify === TRUE ) {
                        
                        // jquery bxslider ##
                        wp_register_script( 'prettify-js', q_locate_template( "javascript/prettify.js", false ), '', '0.1', false );
                        wp_enqueue_script( 'prettify-js' );  

                    }
                    
                    
                    // Tipsy - OG Version - http://onehackoranother.com/projects/jquery/tipsy/ ##
                    if ( isset($q_options->tipsy) && $q_options->tipsy === TRUE ) {

                        // jquery bxslider ##
                        wp_register_script( 'jquery-tipsy', q_locate_template( "javascript/jquery.tipsy.js", false ), array( 'jquery' ), '1.0.0', true );
                        wp_enqueue_script( 'jquery-tipsy' );  

                    }
                    

                    // colorbox ##
                    if ( isset( $q_options->colorbox ) && $q_options->colorbox === TRUE ) {

                        // colorbox js ##
                        wp_register_script( 'jquery-colorbox', q_locate_template( "javascript/jquery.colorbox.js", false ), array('jquery'),'1.3.17.2', true );
                        wp_enqueue_script( 'jquery-colorbox' );   

                        // colorbox css ##
                        wp_register_style( 'q-colorbox', q_locate_template( "css/jquery.colorbox.css", false ), array( 'q-wordpress' ), '1.3.17.2', 'all' );
                        wp_enqueue_style( 'q-colorbox' );

                    }

                    // twitter ##
                    if ( isset( $q_options->twitter ) && $q_options->twitter === TRUE ) {
                        
                        // twitter css ##
                        wp_register_style( 'q-twitter', q_locate_template( "css/jquery.twitter.css", false ),  array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-twitter' );

                        // oauth library ##
                        #q_locate_template( "functions/q_twitter.php", false, true );

                    }


                    // masonry ##
                    if ( isset($q_options->masonry) && $q_options->masonry === TRUE ) {

                        // isotope js ##
                        wp_register_script( 'jquery-masonry', q_locate_template( "javascript/jquery.masonry.js", false ), array('jquery'), '3.1.2', true );
                        wp_enqueue_script( 'jquery-masonry' );   


                    } 
                    
                    // freewall ##
                    if ( isset($q_options->freewall) && $q_options->freewall === TRUE ) {

                        // isotope js ##
                        wp_register_script( 'jquery-freewall', q_locate_template( "javascript/jquery.freewall.js", false ), array('jquery'), '1.0.5', true );
                        wp_enqueue_script( 'jquery-freewall' );   

                    } 
                    
                    // simplemodal ##
                    if ( isset($q_options->simplemodal) && $q_options->simplemodal === TRUE ) {

                        // isotope js ##
                        wp_register_script( 'jquery-simplemodal', q_locate_template( "javascript/jquery.simplemodal.js", false ), array('jquery'), '1.4.4', true );
                        wp_enqueue_script( 'jquery-simplemodal' );   

                    } 

                    // Hover Intent ##
                    // http://cherne.net/brian/resources/jquery.hoverIntent.html
                    if ( isset($q_options->hoverintent) && $q_options->hoverintent === TRUE ) {

                        // isotope js ##
                        wp_register_script( 'jquery-hoverintent', q_locate_template( "javascript/jquery.hoverintent.js", false ), array('jquery'),'7.0.0',true );
                        wp_enqueue_script( 'jquery-hoverintent' );   


                    } 

                    // flickr ##
                    if ( isset($q_options->flickr) && $q_options->flickr === TRUE ) {

                        // flickr js ##
                        wp_register_script( 'jquery-flickr', q_locate_template( "javascript/jquery.flickr.js", false ), array('jquery'),'0.1', false );
                        wp_enqueue_script( 'jquery-flickr' );   

                    }


                    // Gravity Forms ##
                    if ( q_plugin_is_active( 'gravityforms/gravityforms.php' ) && $q_options->framework_css === TRUE ) {

                        wp_register_style( 'q-gravityforms', q_locate_template( "css/plugin.gravityforms.css", false, '', '', true ), array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-gravityforms' );

                    }

                    // tubepress ##
                    if ( ( q_plugin_is_active( 'tubepress/tubepress.php' ) || q_plugin_is_active( 'tubepress_pro/tubepress.php') ) && $q_options->framework_css === TRUE ) {

                        wp_register_style( 'q-tubepress', q_locate_template( "css/plugin.tubepress.css", false, '', '', true ), array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-tubepress' );

                    } // tubepress ##

                    // google calendar events ##
                    if ( q_plugin_is_active( 'google-calendar-events/google-calendar-events.php' ) && $q_options->framework_css === TRUE ) {

                        wp_register_style( 'q-google-calendar-events', q_locate_template( "css/plugin.google-calendar-events.css", false, '', '', true ), array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-google-calendar-events' );

                    } // google calendar events ##

                    // Contact Form 7 ##
                    if ( q_plugin_is_active( 'contactform7/contactform7.php' ) && $q_options->framework_css === TRUE ) {

                        wp_register_style( 'q-contactform7', q_locate_template( "css/plugin.contactform7.css", false, '', '', true ), array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-contactform7' );

                    } // Contact Form 7 ##

                    // mailchimp ##
                    if ( q_plugin_is_active( 'mailchimp/mailchimp.php' ) && $q_options->framework_css === TRUE ) {

                        wp_register_style( 'q-mailchimp', q_locate_template( "css/plugin.mailchimp.css", false, '', '', true ), array( 'q-wordpress' ), '0.1', 'all' );
                        wp_enqueue_style( 'q-mailchimp' );

                    } // mailchimp ##

                    // include default framework JS files ##
                    if ( $q_options->framework_js === TRUE ) {

                        // jquery validate ##
                        wp_register_script( 'jquery-validate', q_locate_template( "javascript/jquery.validate.js", false ), array('jquery'),'1.10.0',true );
                        wp_enqueue_script( 'jquery-validate' );  

                        // jquery ScrollTo ##
                        wp_register_script( 'jquery-scrollto', q_locate_template( "javascript/jquery.scroll.to.js", false ), array('jquery'),'1.4.3.1',true );
                        wp_enqueue_script( 'jquery-scrollto' );   

                        // jquery equal heights ##
                        wp_register_script( 'jquery-equalheights', q_locate_template( "javascript/jquery.equal.heights.js", false ), array('jquery'),'2.0',true );
                        wp_enqueue_script( 'jquery-equalheights' );   

                        // jquery easing ##
                        wp_register_script( 'jquery-easing', q_locate_template( "javascript/jquery.easing.js", false ), array('jquery'),'1.3',true );
                        wp_enqueue_script( 'jquery-easing' );   

                    } // register parent JS files ##
                    
                    // add parent scripts - removable by framework options page ##
                    if ( $q_options->parent_js === TRUE ) {

                        wp_register_script( 'q-parent-scripts', q_locate_template( "javascript/scripts.js", false ), array( 'jquery' ),'0.9',true );
                        wp_enqueue_script( 'q-parent-scripts' );

                        // lozalize script ##
                        /*
                        $translation_array = array( 
                                'open' => __( 'Open' ), 
                                'close' => __( 'Close' ), 
                            );
                        wp_localize_script( 'q_scripts', 'object_name', $translation_array );
                        */

                    }
                    
                }

            }

        }
        
        
        /**
         * include parent stylesheet ##
         */
        function q_enqueue_styles_parent() {

            if ( defined ( 'Q_THEME' ) ) {
                
                // parent stylesheet ##
                global $q_options; // load framework options ##
                if ( $q_options->parent_css === TRUE ) {
                        
                    if ( is_child_theme() ) {
                        
                        $version = wp_get_theme( self::$wp_get_theme->get( 'Template' ) )->get( 'Version' );
                        
                    } else {
                        
                        $version = self::$wp_get_theme->get( 'Version' );
                        
                    }
                    
                    wp_register_style( 'q-style-parent', q_get_option("uri_parent").'style.css', array(), $version, 'all' );
                    wp_enqueue_style( 'q-style-parent' );

                }

            }

        }


        /**
         * include child stylesheet ##
         */
        function q_enqueue_styles_child() {

            if ( defined ( 'Q_THEME' ) ) {

                // child stylesheet ##
                if ( is_child_theme() ) { // only add child theme if this really is a child theme ##
                    if ( file_exists( q_get_option("path_child").'style.css' ) ) { // load child stylesheet ##
                        wp_register_style( 'q-style-child', q_get_option("uri_child").'style.css', array(  ), self::$wp_get_theme->get( 'Version' ), 'all' );
                        wp_enqueue_style( 'q-style-child' );
                    }
                }

            }

        }
        
        
    }
    
}

