<?php

/**
 * Pastebin Shortcode
 *
 * @since 0.3
 * @link http://miloguide.com/2010/07/23/pastebin-com-code-snippet-shortcode-plugin/
 */

// add shortcode [pastebin id="sdfsdfds"] ##
add_shortcode('pastebin', 'q_shortcode_pastebin');

if ( ! function_exists('q_shortcode_pastebin') ) 
{
    function q_shortcode_pastebin( $args, $content = null ) 
    {

        global $q_options;

        $plugin_namespace = '';
        $plugin_namespace = "pastebin";

        $id = $args["id"];
        if (empty($id)) return;
        $html  = "<div class=\"$plugin_namespace r-content\">";
        $html .= "<script src=\"http://pastebin.com/embed_js.php?i=";
        $html .= $id;
        $html .= "\"></script></div>";

        // return html ##
        return $html;

    }
    
}

