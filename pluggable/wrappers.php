<?php

/**
 * Q Class Wrapper Functions
 * 
 * @since       1.0
 */



/* 
* Delete all transient data 
* 
* @since 0.4
*/
if ( ! function_exists('q_transients_delete') ) 
{
    function q_transients_delete() 
    {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // grab global object ##
        global $q_transients;

        // delete all theme transients ##
        $q_transients->delete( 'all' );

    }
}


/* 
 * delete all comment transient data 
 * 
 * @since 0.4
 */
if ( ! function_exists('q_transients_delete_comments') ) 
{
    function q_transients_delete_comments() {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // grab global object ##
        global $q_transients;

        // delete all theme transients ##
        $q_transients->delete( 'comments' );

    }
}


/**
 * Response JS
 *
 * @todo    Deprecate in 1.1 - replace with external plugin
 * @since   1.0
 */
if ( ! function_exists('q_response') ) 
{
    function q_response( $breakpoints = array( '0', '320', '641', '961' ), $modes = array( 'markup' ) ) 
    {

        global $q_response, $q_options;

        if ( isset( $q_options->response ) && $q_options->response === TRUE && class_exists( 'Q_Response' ) ) {

            // instatiate global class object ##
            global $q_response;
            $q_response = new Q_Response();
            $q_response->get( $breakpoints, $modes );

        }

    }
}


/**
 * Google Maps
 *  
 * @since       1.0
 */
if ( ! function_exists('q_map') ) 
{
    function q_map( $key = '' ) 
    {

        global $q_map, $q_options;

        if ( isset( $q_options->map ) && $q_options->map === TRUE && class_exists( 'Q_Map' ) ) {

            // instantiate global class object ##
            $q_map = new Q_Map();

            // set map key ##
            if ( $key ) $q_map->set( $key );

        }

    }
}