<?php

/**
 * Obfuscate Shortcode
 *
 * @since 0.8
 */

// shortcode [obfuscate text="blahblah"] ##
add_shortcode('obfuscate', 'q_shortcode_obfuscate');

if ( ! function_exists('q_shortcode_obfuscate') ) {
function q_shortcode_obfuscate( $args, $content = null ) {
    
    // args ##
    $text = $args["text"];
    $class = isset($args["class"]) ? $args["class"] : "";
    
    // bale out ##
    if (empty($text)) return;
    
    // return html ##
    return q_obfuscate( $text, $class );
        
}}

// obfuscate function ##
if ( ! function_exists('q_obfuscate') ) {
function q_obfuscate( $text, $class = '' ) {
        
    $html  = "<span class=\"q_ob $class\">";
    $html .= strrev($text).'';
    #$html .= '<a href="mailto:'.$text.'">'.$text.'</a>';
    $html .= "</span><br />";
    
    // return html ##
    return $html;
    
}}