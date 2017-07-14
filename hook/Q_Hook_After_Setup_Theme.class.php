<?php

/**
 * Functions hooked to after_setup_theme action in WP
 * 
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @since       0.1
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_After_Setup_Theme' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'after_setup_theme', array ( 'Q_Hook_After_Setup_Theme', 'init' ), 1 ); // after_setup_theme ##
    
    // Q_Hook_After_Setup_Theme Class
    class Q_Hook_After_Setup_Theme extends Q
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
        

        private function __construct()
        {
            
            // filter meta title ##
            add_filter( 'wp_title', array ( $this, 'q_wp_title' ), 10, 2 );
            
        }
        
        
        /**
         * Filters the page title appropriately depending on the current page
         * This function is attached to the 'wp_title' filter hook.
         *
         * @uses	get_bloginfo()
         * @uses	is_home()
         * @uses	is_front_page()
         * 
         * @since       0.1
         */
        public function q_wp_title( $title, $sep ) {

            global $page, $paged, $post;
            
            $page_title = $title;
             
            // get site desription ##
            $site_description = get_bloginfo( 'description' );
            
            if ( $post ) { 

                // allow for custom title - via post meta "metatitle" ##
                $page_title = get_post_meta( $post->ID, "metatitle", true ) ? get_post_meta( $post->ID, "metatitle", true ).' '.$sep. ' ' : $title;
                
                // if this is a singular post - but not of type page or post add post type name as parent ##
                if ( is_singular( get_post_type() ) && get_post_type() !== 'post' && get_post_type() !== 'page' ) {
                    
                    if ( $obj = get_post_type_object( get_post_type() ) ) {
                    
                        $page_title = $page_title.' '.$obj->labels->menu_name.' '.$sep.' ';

                    }
                    
                }
                
                // add parent page, if page ##
                if ( $post->post_parent && $post->post_type === 'page' ) {

                    if ( $get_post_ancestor = get_post_ancestors( $post->ID ) ) {

                        $page_title = $page_title.' '.get_the_title( array_pop( $get_post_ancestor ) ).' '.$sep.' ';

                    }

                }
                
            }
            
            // if we're on a single category check if that page has a parent ##
            if ( is_archive() ) {

                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

                if ( $term && $term->parent > 0 ) {

                    // get parent name ##
                    $term_parent = get_term_by( 'ID', $term->parent, get_query_var( 'taxonomy' ) ) ;

                    if ( $term_parent->name ) {

                        $page_title .= $term_parent->name.' '.$sep.' ';

                    }

                }

            }
            
            // @todo -- get page template ##
            #page-template-taxonomy-set-php
            
            // compile ##
            $page_title = $page_title . get_option( 'blogname' ); // with site name ##
            #$filtered_title = $page_title; // without site name ##
            
            // add site description if not empty and on front page ##
            $page_title .= ( ! empty( $site_description ) && ( is_front_page() ) ) ? ' | ' . $site_description : '' ;
            
            // add paging number, if paged ##
            $page_title .= ( 2 <= $paged || 2 <= $page ) ? ' | ' . sprintf( __( 'Page %s' ), max( $paged, $page ) ) : '' ;

            // return title ##
            return $page_title;

        }
        

    }


}

