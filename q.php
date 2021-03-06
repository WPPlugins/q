<?php

/* 
 * Plugin Name:     Q
 * Plugin URI:      http://www.qstudio.us
 * Description:     Q is a Development Framework that provides an API to manage libraries, themes, plugins and widgets.
 * Version:         1.6.3
 * Author:          Q Studio
 * Author URI:      http://www.qstudio.us
 * License:         GPL
 * Copyright:       Q Studio
 * Class:           Q
 * Text Domain:     q
 * Domain Path:     /languages
*/

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

/* Check for Class */
if ( ! class_exists( 'Q' ) ) {
    
    // instatiate plugin via WP Hook:plugins_loaded ##
    add_action ( 'plugins_loaded', array( 'Q', 'init' ), 0 );
    
    define( 'Q_DEBUG', false );
    define( 'Q_VERSION', '1.6.3' );
    
    // activation ##
    register_activation_hook( __FILE__, array ( 'Q', 'q_register_activation_hook' ) );

    // deactivation ##
    register_deactivation_hook( __FILE__, array ( 'Q', 'q_register_deactivation_hook' ) );

    // uninstall ##
    // handled via uninstall.php
    
    // Q Class ##
    class Q {
        
        
        /**
        * Class Properties
        *
        * @var string
        */
        public 
                $version = Q_VERSION      // The Plug-in version ##
            ,   $wp_version = "3.6"    // The minimal required version of WordPress for this plug-in to function correctly ##
            ,   $dependencies_global   // global libraries to include ##
            ,   $dependencies_admin    // admin libraries to include ##
            ,   $dependencies_theme    // theme libraries to include ##
            ,   $widgets_add           // widgets to allow ##
            ,   $widgets_remove        // widgets to remove - by Class Name ##
        ;
        
        // page load debugging ##
        public static $store;
        
        
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
         * 
         * @since       0.1
         * @return      void
         */
        private function __construct()
        {
            
            // Plug-in requirements ##
            add_action ( 'plugins_loaded', array( $this, 'check_requirements' ), 1 );

            // Declare constants
            add_action ( 'plugins_loaded', array( $this, 'define_constants' ), 2 );
            
            // define dependencies ##
            add_action ( 'plugins_loaded', array( $this, 'define_dependencies' ), 3 );
            
            // text-domain ##
            add_action ( 'load_plugin_textdomain', array ( $this, 'q_load_plugin_textdomain' ), 1 );
            
            // shutdown cache dump ##
            #add_action ( 'shutdown', array ( 'Q_Plugin', 'shutdown' ), 10000000 );
            
        }
        
        
         
        /**
         * plugin activation 
         * 
         * @since   0.2
         */
        public function q_register_activation_hook() 
        {
            
            $q_options = array( 
                'configured'    => true 
                ,'version'      => Q_VERSION
            );
            
            // init running, so update configuration flag ##
            add_option( 'q_plugin', $q_options, '', true );
            
        }

        
        /**
         * plugin deactivation 
         * 
         * @since   0.2
         */
        public function q_register_deactivation_hook() 
        {
            
            // de-configure plugin ##
            delete_option('q_plugin');
            
        }

        
        /**
         * Checks that the WordPress setup meets the plugin requirements
         * 
         * @since   0.1
         * @global  string      $wp_version
         * @return  boolean
         */
        public function check_requirements() 
        {
            
            global $wp_version;

            if ( ! version_compare( $wp_version, $this->wp_version, '>=' ) ) {
                
                // add a notice ##
                add_action( 'admin_notices', array ( $this, 'display_requirements_notice' ) );
                
                // turn this plugin off ##
                add_action('admin_notices', function(){
                    deactivate_plugins( plugin_basename( __FILE__ ) );
                });

                // stop this plugin running ##
                return;
                
            }
            return true;
            
        }
        
        
        /**
         * Display the requirement notice
         * 
         * @return  void
         */
        public function display_requirements_notice() 
        {
            
            $plugin = get_plugin_data( __FILE__ );
            
            echo '<div id="message" class="error"><p><strong>';
            echo __( $plugin["Name"].' requires WordPress ' . $this->wp_version . ' or higher - please upgrade WordPress', 'q-textdomain');
            echo '</strong></p></div>';
               
        }

        
        /**
         * Define constants needed across the plug-in.
         */
        public function define_constants() 
        {
            
            define( 'Q_DIR', dirname(__FILE__) ); // full path to Q plugin directory ##
            define( 'Q_URLPATH', trailingslashit(WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__))) );
            define( 'Q_PLUGIN_PATH', plugin_basename(__FILE__) ); // Q directory and filename ##
            #define( 'Q_VERSION', $this->version ); // Q plugin version ##
            
            /*
            define( 'Q_BASENAME', plugin_basename(__FILE__) );
            define( 'Q_FOLDER', plugin_basename(dirname(__FILE__)) );
            define( 'Q_ABSPATH', trailingslashit(str_replace("\\", "/", WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)))) );
            define( 'Q_URLPATH', trailingslashit(WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__))) );
            define( 'Q_ADMINPATH', get_admin_url() );
            */
            
        }

        
        /**
         * Loads PHP files required by the plug-in
         * 
         * @since   0.1
         */
        public function define_dependencies()
        {
            
            // list of global required files - admin or not ##
            $this->dependencies_global = array(
                
                // Pluggable Functions ##
                    array ( 'pluggable', 'helpers.php'  ) // General PHP Helper Functions ##
                ,   array ( 'pluggable', 'wordpress.php' ) // WordPress specific functions ##
                ,   array ( 'pluggable', 'wrappers.php' ) // Q Class Wrappers ##
                ,   array ( 'pluggable', 'theme.php' ) // Pluggable Theme Functions ##
                ,   array ( 'pluggable', 'deprecated.php' ) // Deprecated Theme Functions ##
                
                
                // Global Classes ##
                ,   array ( 'global', 'Q_Options.class.php'  ) // Q Options ##
                ,   array ( 'global', 'Q_Functions.class.php' ) // Functions.php Class ##
                ,   array ( 'global', 'Q_Transients.class.php' ) // add simpler transients class ##
                ,   array ( 'global', 'Q_Map.class.php'  ) // Google Maps building class ##
                ,   array ( 'global', 'Q_Twitter.class.php'  ) // Q Twitter Class ##
                #,   array ( 'global', 'Q_Debug.class.php' ) // Debugging object ##
                
                
                // Shortcodes ##
                ,   array ( 'shortcode', 'shortcode.obfuscate.php' ) // Simple Obfuscation ##
                ,   array ( 'shortcode', 'shortcode.pastebin.php' ) // Embed from PasteBin ##
                
                
                // global action hooks ##
                ,   array ( 'hook', 'Q_Hook_After_Setup_Theme.class.php' ) // hook: after_setup_theme ##
                ,   array ( 'hook', 'Q_Hook_Init.class.php' ) // hook: init ( include widiget_init ) ##
                ,   array ( 'hook', 'Q_Hook_WP_Enqueue_Scripts.class.php' ) // hook: wp_enqueue_scripts ##
                ,   array ( 'hook', 'Q_Hook_WP_Head.class.php' ) // hook: wp_head ##
                ,   array ( 'hook', 'Q_Hook_The_Post.class.php' ) // hook: the_post ##
                ,   array ( 'hook', 'Q_Hook_WP_Footer.class.php' ) // hook: wp_footer ##
                
            );
            
            // list of admin library files ##
            $this->dependencies_admin = array (
                
                // admin modifications ##
                    array ( 'admin', 'Q_Admin.class.php' ) // General Admin Class ##
                ,   array ( 'admin', 'Tax_CPT_Filter.class.php' ) // filter custom post type by a custom taxonomy ##
                ,   array ( 'admin', 'CPT_columns.class.php'  ) // define columns and content for custom post types ##
                ,   array ( 'admin', 'WeDevs_Settings_API.class.php' ) // add admin options page and save data correctly ##
                
                
                // admin hooks ##
                ,   array ( 'hook', 'Q_Hook_Switch_Theme.class.php' ) // hook: switch_theme ##
                ,   array ( 'hook', 'Q_Hook_After_Switch_Theme.class.php' ) // hook: after_switch_theme ##
                ,   array ( 'hook', 'Q_Hook_Save_Post.class.php' ) // hook: save_post ##
                ,   array ( 'hook', 'Q_Hook_Comment_Post.class.php' ) // hook: comment_post ##
                
                
            );
            
            // list of non-admin library files ##
            $this->dependencies_theme = array ( );
            
            
            
            
            // load global dependencies ##
            $this->load_dependencies( $this->dependencies_global );
            
            if ( is_admin() ) {
                
                // load admin dependencies ##
                $this->load_dependencies( $this->dependencies_admin );
            
            } else {
                
                // load non-admin dependencies ##
                $this->load_dependencies( $this->dependencies_theme );
            
            }
            
        }
        
        
        /**
         * Include all libraries in a defined directory
         * 
         * @since       0.1
         * @param       Array   $files      Array of files to search for and include
         * @param       string  $method     Method to use to include files
         * @return      void
         */
        public function load_dependencies( $files = null, $debug = false, $method = 'require_once' ) 
        {
            
            // some error checking ##
            if ( is_null ( $files ) || ! is_array( $files ) || empty ( $files ) ) { return false; }
            
            // grab our root path ##
            $path = Q_DIR;
            
            // loop over array of file names ##
            foreach( $files as $load ) {
                
                if ( ! is_array( $load ) ) { continue; } // skip this item, as mal-formed ##
                
                // get file and directory ##
                $directory = $load[0]; // grab the directory ##
                $file = $load[1]; // grab the file ##
                
                // check if the file exists ##
                if ( file_exists( "{$path}/{$directory}/{$file}" ) ) {
                    
                    #if ( $debug ) wp_die( var_dump($file) );
                    
                    // how to include the file ##
                    switch ( $method ) {

                        // require once ##
                        case 'require_once':

                            #if ( $debug ) wp_die( "{$path}/{$directory}/{$file}" );

                            require_once ( "{$path}/{$directory}/{$file}" );

                            break;

                    }

                }
                 
            }
            
        }
        
        
        /**
         * Load Plugin Text Domain ##
         * 
         * @since   0.1
         */
        public function q_load_plugin_textdomain() 
        {
            
            load_plugin_textdomain( 'q-textdomain', false, basename( dirname( __FILE__ ) ) . '/languages/' );
            
        }
        
        
        /**
         * Request data safely using $_GET, $_POST & $_REQUEST
         * 
         * @since   0.1
         * @param   string      $key:           Key to search for
         * @param   string      $sanitize       Sanatize method to apply to the data
         * @param   Boolean     $debug          Allows for individual debugging of keys
         * @return  mixed       boolean | string         
         */
        public static function request_safe( $key = null, $sanitize = null, $debug = false, $methods = array( 'post' => true ) )
        {
            
            // quick check ##
            if ( ! $key ) { return false; }
            
            // debugging on - so allow broader range of request methods ##
            if ( defined( 'Q_DEBUG' ) && Q_DEBUG === true ) {
                
                $methods['get'] = true; // allow $_GET method ##
                #$methods['request'] = true; // allow $_REQUEST method ##
                
            }
            
            // check for key in allowed superglobals ##
            foreach( $methods as $method => $value ) {
                
                if ( $value === true ) { // method allowed ##
                    
                    switch ( $method ) {
                        
                        case 'get':
                            
                            if ( isset( $_GET[ $key ] ) ) {  
                                
                                if ( $debug === true ) { pr($_GET[ $key ]); } // debug ##
                                
                                return self::sanitize( $_GET[ $key ], sanitize_text_field ( $sanitize ) );
                                
                            }
                                
                            break;
                        
                        case 'post':
                        default:
                            
                            if ( isset( $_POST[ $key ] ) ) {  
                                
                                if ( $debug === true ) { wp_die($_POST[ $key ]); } // debug ##
                                
                                return self::sanitize( $_POST[ $key ], sanitize_text_field ( $sanitize ) );
                                
                            }
                            
                            break;
                        
                    }
                    
                }
                
            }
            
            // nothing happening ##
            return false;
            
        }
        
        
        /**
         * Sanitize user input data using WordPress functions
         * 
         * @since       0.1
         * @param       string      $value      Value to sanitize
         * @param       string      $type       Type of value ( email, user, int, key, text[default] )
         * @link        http://codex.wordpress.org/Validating_Sanitizing_and_Escaping_User_Data
         * @link        http://wp.tutsplus.com/tutorials/creative-coding/data-sanitization-and-validation-with-wordpress/
         * @return      string      HTML output
         */
        public static function sanitize( $value = null, $type = 'text' )
        {
            
            // check submitted data ##
            if ( is_null( $value ) ) {
                
                return false;
                
            }
            
            switch ( $type ) {
                
                case( 'email' ):
                
                    return sanitize_email( $value );
                    break;
                
                case( 'user' ):
                
                    return sanitize_user( $value );
                    break;
                
                case( 'integer' ):
                
                    return intval( $value );
                    break;
                
                case( 'filename' ):
                
                    return sanitize_file_name( $value );
                    break;
                
                case( 'key' ):
                
                    return self::sanitize_key( $value ); // altered version of wp sanitize_key
                    break;
                
                case( 'sql' ):
                    
                    return esc_sql( $value );
                    break;
                
                case( 'stripslashes' ):
                    
                    return preg_replace("~\\\\+([\"\'\\x00\\\\])~", "$1", $value);
                    #stripslashes( $value );
                    break;
                
                case( 'none' ):
                    
                    return $value;
                    break;
                
                case( 'text' ):
                default;
                     
                    // text validation
                    return sanitize_text_field( $value );
                    break;
                    
            }
            
        }
        
        
        /**
        * Sanitizes a string key.
        *
        * @since 1.3.0
        * @param string $key String key
        * @return string Sanitized key
        */
        public static function sanitize_key( $key = null ) 
        {
            
            // sanity check ##
            if ( ! $key ) { return false; }
            
            // scan the key for allowed characters ##
            $key = preg_replace( '/[^a-zA-Z0-9_\-~!$^+]/', '', $key );
            
            // return the key ##
            return $key;
            
       }
        
        
    }
    
// class_exists check ##
}

