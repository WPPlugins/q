<?php

/**
 * Q Admin Class
 * 
 * @since       1.0
 * @Author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Admin' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'init', array ( 'Q_Admin', 'init' ), 1 );
    
    // Q_Admin Class
    class Q_Admin extends Q
    {

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
         * @since       1.2.0
         * @return      void
         */
        public function __construct()
        {
            
            if ( is_admin() ) {
            
                // set-up admin image sizes ##
                add_action( "admin_init", array( $this, 'admin_setup_images' ) );
                
                // add thumbnails to admin columns ##
                add_action( 'admin_init', create_function( '', Q_Admin::add_thumbnail_to( array( 'posts', 'pages' ) ) ) );
                
            }
            
        }
        
        
        /**
         * Add Thumbnail Column to Post Type in admin
         * 
         * @since       1.2.0
         * @param       Array    $post_types
         */
        public static function add_thumbnail_to( $post_types = null )
        {
            
            // sanity check ##
            if ( ! $post_types ) { return false; } // nothing to do ##
            
            // make sure this is only loaded up in the admin ##
            if ( is_admin() ) {
                
                foreach ( $post_types as $post_type ) {

                    // add thumbnails for post_type ##
                    add_filter( "manage_{$post_type}_columns", array( 'Q_Admin', 'admin_add_thumbnail_column' ) );
                    add_action( "manage_{$post_type}_custom_column", array( 'Q_Admin', 'admin_add_thumbnail_value' ), 10, 2 );

                }
                
            }
            
        }
        
        
        /**
         * Add thumbnail column
         * 
         * @param       Array    $cols
         * @return      Array
         */
        public static function admin_add_thumbnail_column( $cols ) 
        {
            
            $cols['thumbnail'] = __('Thumbnail');
            return $cols;
            
        }

        
        /**
         * Add row thumbnail value 
         * 
         * @param type $column_name
         * @param type $post_id
         */
        public static function admin_add_thumbnail_value( $column_name, $post_id ) 
        {

            $width = (int) 200;
            $height = (int) 125;

            if ( 'thumbnail' == $column_name ) {
                // thumbnail of WP 2.9
                $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
                // image from gallery
                $attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
                if ( $thumbnail_id ) {
                    #$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
                    #echo $thumbnail_id;
                    $thumb = wp_get_attachment_image( $thumbnail_id, 'admin-list-thumb', true );
                } elseif ($attachments) {
                    foreach ( $attachments as $attachment_id => $attachment ) {
                        #$thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
                        $thumb = wp_get_attachment_image( $attachment_id, 'admin-list-thumb', true );
                    }
                }
                if ( isset($thumb) && $thumb ) {
                    echo $thumb;
                }
            }
        }
        
        
        /**
         * Set-up image sizes in WP admin 
         * 
         * @since       1.2.0
         * @return      void
         */
        public static function admin_setup_images( )
        {
        
            // default thumb size in admin ##
            set_post_thumbnail_size( 260, 200, true );

            // this theme uses post thumbnails - set the sizes below ##
            add_image_size( 'admin-list-thumb', 60, 40, true ); // admin thumbs ##
            add_image_size( 'dashboard', 100, 40, true );
            
        }
        

    }


}

// Flush menu cache if menus are changed
if( isset($_POST['action']) && isset($pagenow) && $pagenow === 'nav-menus.php' ){
    array_map( 'unlink', glob(__DIR__ . '/cache/'.'*.html.cache') );
}
