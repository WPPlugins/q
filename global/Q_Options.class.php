<?php

/**
 * Q Options
 *
 * @description     Get, Set and Define Q Options
 * @since           1.0
 * @author          Q Studio
 * @link            http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Options' ) ) 
{

    // Q_Options Class ##
    class Q_Options extends Q
    {
        
        public $radio_options;
        protected $q_options;
        
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
         * @since       1.0
         * @return      void
         */
        public function __construct()
        {
            
            // extend the radio_options property ##
            $this->radio_options = array(
                'yes' => array(
                    'value' => '1',
                    'label' => __( 'Yes', 'q-textdomain' )
                ),
                'no' => array(
                    'value' => '0',
                    'label' => __( 'No', 'q-textdomain' )
                ),
            );
            
            
            if ( is_admin() ) { // make sure this is only loaded up in the admin ##
                
                // options nags, panels and notices ##
                add_action( 'admin_notices', array( $this, 'options_notices' ) );
                add_action( 'admin_init', array( $this, 'options_notices_ignore' ) );
                
                // register settings API ##
                add_action( 'admin_init', array ( $this, 'register_setting' ) );
                
                // framework options page ##
                add_action( 'admin_menu', array ( $this, 'add_submenu_page' ) );
                
            }
            
        }
        
        
        /**
         * Define Default Q Options
         * 
         * @since       1.0
         * @return      void
         */
        protected function options_define()
        {
            
            // build options array ##
            $this->q_options = array ( 

                // framework settings ##
                "framework_css"             => true, // add framework css styling ##
                "framework_js"              => true, // add framework javascript ##

                // theme settings ##
                "parent_css"                => true, // add parent css styling to child theme ##
                "parent_js"                 => true, // add parent javascript files to child theme ##

                // google codes ##
                "google_analytics"          => '', // tracking code ##
                "google_webmasters"         => '', // verification code ##

            );
            
        }
        
        
        /**
         * Expose options to other functions 
         * 
         * @since       1.0
         * @return      Object
         */
        public function options_get() 
        {
            
            // check for options ##
            $q_options = get_option( 'q_options' );
            
            // no options loaded from wp_options ##
            if ( ! $q_options ) {

                // define default options ##
                $this->options_define();
                
                // grab those options ##
                $q_options = $this->q_options;

                // still no options !! ##
                if ( ! is_array( $q_options ) ) { 

                    // kill WP ##
                    wp_die( 
                        _e( 
                            "<h2>Q Error!</h2><p>There was an error loading the required Q Options.</p>" 
                            ,'q-textdomain'
                        ) ); 

                } else { 

                    // add wp_options reference ##
                    q_add_update_option( 'q_options', $q_options, '', 'yes' );

                }

            }
            
            // kick it back as an object ##
            return q_array_to_object( $q_options );
            
        }
        
        
        /**
         * Delete Q Options - could be used to clear old settings
         */
        private function options_delete()
        {
            
            delete_option( 'q_options' ); // delete option ##
            
        }
        
        
        /**
        * Init plugin options to white list our options
        */
        function register_setting()
        {
            
            register_setting( 'q_options', 'q_options', array ( $this, 'options_validate' ) );
            
        }
        
        
        /**
        * Load up the menu page
        */
        public function add_submenu_page() 
        {
            
            add_submenu_page( 
                'options-general.php' 
                ,__( 'Q', 'q-textdomain' )
                ,__( 'Q', 'q-textdomain' )
                , 'manage_options'
                , 'q'
                , array ( $this, 'options_page')
            ); 
            
        }


        /**
         * Create the options page
         */
        public function options_page() 
        {
            
            // get Q Plugin data ##
            $plugin_data = q_plugin_data();

            // get Q options ##
            $options = $this->options_get();

            // domain ##
            #$options->q_key_domain = isset( $options->q_key_domain ) ? $options->q_key_domain : get_option('home') ;

?>
        <style>
            .update-nag { display: none; }
            .small { font-size: 70%; }
            .form-table th { font-weight: 100; }
            input[type="radio"] { margin: 0 10px; }
        </style>
        <script type="text/javascript">

        jQuery(document).ready(function() {
<?php
    
            // check for update in request headers ##
            if ( isset( $_GET['settings-updated'] ) ) { 
                
?>
                jQuery("div.updated p strong").html('<?php _e( 'Settings Saved.', 'q-textdomain' ); ?>'); 
<?php

            }
?>

        });
        </script>
        <div class="wrap">
<?php

            // page header ##
            $version = ' <span class="small">( version '.$plugin_data->version.' )</span>';
            echo "<h2>".__( 'Q Options', 'q-textdomain' ).$version."</h2>"; 
            echo "<p>".__( 'If the option you are looking for is not listed on this page, it has probably been added via a plugin or theme.', 'q-textdomain' ) . " - <a href='http://qstudio.us/releases/q/#q-options' target='_blank'>Help</a></p>"; 

?>
            <form method="post" action="options.php">
<?php                 

            // add nonce field ##
            settings_fields( 'q_options' );


        // Framework settings ##
        
        if ( ! defined ( 'Q_THEME' ) ) { 
            
?>
                <h3>Q Framework</h3>
                <p>To activate the Q Theme Framework, you need to add the following code snippet to the top of the parent or child functions.php - <a target="_blank" href="http://qstudio.com/codex/q_plugin/">Read More</a></p>
                <code>add_action( 'init', 'q_theme' ); // activate Q Theme Framework options ##</code>

<?php            
            
        // Q_THEME is defined, so show the options ##
        } else { 
        
        ?>
                <h3>Framework Settings:</h3>
                <table class="form-table">
                    <tr valign="top"><th scope="row"><?php _e( 'Load Framework CSS', 'q-textdomain' ); ?></th><td><fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Load Framework CSS', 'q-textdomain' ); ?></span></legend>
        <?php

                        // no checked option ##
                        if ( !isset( $checked ) ) {  $checked = ''; }

                        // loop all options ##
                        foreach ( $this->radio_options as $option ) {

                            // get value from options ##
                            $radio_setting = $options->framework_css;
                            $bool_option_value = (bool)$option['value']; // cast to boolean ##
                            if ( $radio_setting === $bool_option_value ) {
                                $checked = "checked=\"checked\"";
                            } else {
                                $checked = '';
                            }

        ?>
                        <label class="description" style="margin-right: 10px;">
                            <input type="radio" name="q_options[framework_css]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                        </label>
        <?php
                        } // foreach radio ##
        ?>
                    </fieldset></td></tr>

                    <tr valign="top"><th scope="row"><?php _e( 'Load Framework JavaScript', 'q-textdomain' ); ?></th><td><fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Load Framework JavaScript', 'q-textdomain' ); ?></span></legend>
        <?php

                        // no checked option ##
                        if ( !isset( $checked ) ) {  $checked = ''; }

                        // loop all options ##
                        foreach ( $this->radio_options as $option ) {

                            // get value from options ##
                            $radio_setting = $options->framework_js;
                            $bool_option_value = (bool)$option['value']; // convert to boolean ##
                            if ( $radio_setting === $bool_option_value ) {
                                $checked = "checked=\"checked\"";
                            } else {
                                $checked = '';
                            }

        ?>
                        <label class="description" style="margin-right: 10px;">
                            <input type="radio" name="q_options[framework_js]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                        </label>
        <?php
                            }
        ?>
                    </fieldset></td></tr>
                    
                </table>
                
                <h3>Parent Theme Settings:</h3>
                <table class="form-table">
                    <tr valign="top"><th scope="row"><?php _e( 'Load Parent CSS', 'q-textdomain' ); ?></th><td><fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Load Parent CSS', 'q-textdomain' ); ?></span></legend>
        <?php

                        // no checked option ##
                        if ( !isset( $checked ) ) {  $checked = ''; }

                        // loop all options ##
                        foreach ( $this->radio_options as $option ) {

                            // get value from options ##
                            $radio_setting = $options->parent_css;
                            $bool_option_value = (bool)$option['value']; // convert to boolean ##
                            if ( $radio_setting === $bool_option_value ) {
                                $checked = "checked=\"checked\"";
                            } else {
                                $checked = '';
                            }

        ?>
                        <label class="description" style="margin-right: 10px;">
                            <input type="radio" name="q_options[parent_css]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                        </label>
        <?php
                            }
        ?>
                    </fieldset></td></tr>

                    <tr valign="top"><th scope="row"><?php _e( 'Load Parent JavaScript', 'q-textdomain' ); ?></th><td><fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Load Parent JavaScript', 'q-textdomain' ); ?></span></legend>
        <?php

                        // no checked option ##
                        if ( !isset( $checked ) ) {  $checked = ''; }

                        // loop all options ##
                        foreach ( $this->radio_options as $option ) {

                            // get value from options ##
                            $radio_setting = $options->parent_js;
                            $bool_option_value = (bool)$option['value']; // convert to boolean ##
                            if ( $radio_setting === $bool_option_value ) {
                                $checked = "checked=\"checked\"";
                            } else {
                                $checked = '';
                            }

        ?>
                        <label class="description" style="margin-right: 10px;">
                            <input type="radio" name="q_options[parent_js]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                        </label>
        <?php
                            }
        ?>
                    </fieldset></td></tr>
                </table>
                
                <h3>Google Settings:</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="q_options[google_analytics]"><?php _e( 'Google Analytics', 'q-textdomain' ); ?></label></th>
                        <td>
                            <input id="q_options[google_analytics]" class="regular-text" type="text" name="q_options[google_analytics]" value="<?php esc_attr_e( $options->google_analytics ); ?>" />
                            <p class="description" ><?php _e( 'Enter Your Google Analytics UA', 'q-textdomain' ); ?> - <a href="http://www.google.co.uk/analytics/" target="_blank">Sign Up</a></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="q_options[google_webmasters]"><?php _e( 'Google Webmasters', 'q-textdomain' ); ?></label></th>
                        <td>
                            <input id="q_options[google_webmasters]" class="regular-text" type="text" name="q_options[google_webmasters]" value="<?php esc_attr_e( $options->google_webmasters ); ?>" />
                            <p class="description" ><?php _e( 'Enter Your Google Webmasters Verify Code', 'q-textdomain' ); ?> - <a href="https://www.google.com/webmasters/" target="_blank">Sign Up</a></p>
                        </td>
                    </tr>
                </table>
<?php
                
            } // q_theme CONSTANT ##

?>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'q-textdomain' ); ?>" />
                </p>
                
            </form>
        </div>
<?php

        } // theme_options_do_page ##

        
        /**
         * Sanitize and validate input. Accepts an array, return a sanitized array.
         */
        function options_validate( $input ) 
        {

            // grab options ##
            #$q_options = $this->options_get();

            #$input['q_key']                 = wp_filter_nohtml_kses( $input['q_key'] );
            #$input['q_key_liberate']        = ( $input['q_key_liberate'] == 1 ? true : false );
            #if ( isset( $input['q_key_liberate'] ) && $input['q_key_liberate'] === true ) { // free ##
            #    $input['q_key']             = $this->options_q_key( $q_options->q_key, true );
            #} else { // check ##
            #    $input['q_key']             = $this->options_q_key( $input['q_key'], $input['q_key_liberate'] );
            #} 
            // text option must be safe text with no HTML tags ##
            #$input['q_key_domain']          = wp_filter_nohtml_kses( $input['q_key_domain'] );

            // force default value if radio empty ##
            $input['framework_css']         = ( $input['framework_css'] == 1 ? true : false );
            $input['framework_js']          = ( $input['framework_js'] == 1 ? true : false );
            #$input['framework_warning']     = ( $input['framework_warning'] == 1 ? true : false );

            $input['parent_css']            = ( $input['parent_css'] == 1 ? true : false );
            $input['parent_js']             = ( $input['parent_js'] == 1 ? true : false );

            $input['google_analytics']      = wp_filter_nohtml_kses( $input['google_analytics'] );
            $input['google_webmasters']     = wp_filter_nohtml_kses( $input['google_webmasters'] );

            return $input;

        }
        
        
        /**
         * Validate Q_Key
         * 
         * @since       1.0
         * @todo        move API to qstudio.us/api/
         */
        private function options_q_key( $key, $do ) 
        {
    
            $do = ( $do === true ) ? "free" : "check" ;
            $ch = curl_init();
            curl_setopt_array( $ch, array(
                CURLOPT_RETURNTRANSFER  => 1,
                CURLOPT_REFERER         => preg_replace( '#^https?://#', '', get_option('home') ),
                CURLOPT_URL             => "http://api.4tre.es/query/key/?treekey=".$key."&do=".$do,
                CURLOPT_VERBOSE         => 1,
            ));
            $data = curl_exec($ch);
            curl_close($ch);
            $key = ( is_connected() ? $data : $key );
            return mb_substr( $key, 0, 10 );

        }
        
        
        /**
         * 
         * Update options
         * 
         * @since       1.0
         * @param       Array   $r
         * @param       String  $url
         * @return      Array
         * @todo        Include localhost updates
         */
        public function options_update( $r, $url ) 
        {
            
            if ( isset( $options->q_key ) && $options->q_key ) { return false; }
            
            if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check/1.1/' ) ) {

                if ( defined ( Q_PLUGIN_PATH ) ) {

                    $plugins = json_decode( $r['body']['plugins'], true );

                    if ( array_key_exists( $this->q_plugin_path, $plugins['plugins'] ) ){

                        wp_die("don't update: {$this->q_plugin_path}");

                        unset( $plugins['plugins'][$this->q_plugin_path] );

                    }

                    $r['body']['plugins'] = json_encode( $plugins );

                }

            }
                
            // return updated Array
            return $r;
            
        }

        
        /**
         * Display a notice that can be dismissed 
         * 
         * @since       1.0
         */
        function options_notices() 
        {

            // grab options ##
            $q_options = $this->options_get();

            // get options ##
            $q_key = isset( $q_options->q_key ) ? $q_options->q_key : false ;

            // user ##
            global $current_user;
            $user_id = $current_user->ID;

            // Framework ##
            $url_q_options = admin_url( 'options-general.php?page=q', 'http' );
            $button_q_options = '<input type="button" style="margin-left: 10px;" class="button" value="'.__("Q Options", "q").'" onclick="document.location.href=\''.$url_q_options.'\'">';

        }
        
        
        /**
         * Hide Options Notice
         * 
         * @since       1.0
         * @return      void
         */
        function options_notices_ignore() 
        {
            
            global $current_user;
            $user_id = $current_user->ID;
            /* If user clicks to ignore the notice, add that to their user meta */
            if ( isset($_GET['q_nag_ignore']) && '0' == $_GET['q_nag_ignore'] ) {
                add_user_meta($user_id, 'q_ignore_notice', 'true', true);
            }
            
        }
        
    }
    
    // instatiate Q_Options Class
    $q_options_class = new Q_Options();
    
    // declare a global variable ##
    global $q_options;
    
    // grab the options ##
    $q_options = $q_options_class->options_get();
    
}
