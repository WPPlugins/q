<?php

/**
 * PHP Helper functions for Q Framework
 *
 * @since 0.1
 */


/*
 * convert an array to an object ##
 * 
 * @param array $array
 * @return object
 * @since 0.1
 * 
 */
function q_array_to_object($array) {
    if(!is_array($array)) {
        return $array;
    }
    
    $object = new stdClass();
    if (is_array($array) && count($array) > 0) {
      foreach ($array as $name=>$value) {
         $name = strtolower(trim($name));
         if (!empty($name)) {
            $object->$name = q_array_to_object($value);
         }
      }
      return $object; 
    }
    else {
      return false;
    }
}


/**
 * flatten an Array
 * 
 * @since 1.0.1
 */
if ( !function_exists( 'q_flatten_array' ) ) {
function q_flatten_array($array) {
    $result = array();
    foreach($array as $key=>$value) {
        if(is_array($value)) {
            $result = $result + q_flatten_array($value, $key . '.');
        }
        else {
            $result[$key] = $value;
        }
    }
    return $result;
}}

/**
 * Return a random key from an array
 * 
 * @since 0.7
 */
if ( !function_exists( 'q_array_random' ) ) {
function q_array_random($arr, $num = 1) {
    
    if ( !is_array($arr) ) { 
        #echo 'not array'; 
        return;
    }
    
    shuffle($arr);
    
    $r = array();
    for ($i = 0; $i < $num; $i++) {
        $r[] = $arr[$i];
    }
    return $num == 1 ? $r[0] : $r;
    
}}

/**
 * check for a valid internet connection
 * 
 * @return boolean 
 */
if ( !function_exists( 'is_connected' ) ) {
function is_connected(){
    $connected = @fsockopen( "www.google.com", "80" ); // domain and port
    if ($connected){
        fclose($connected);
        return true; //action when connected
    }else{
        return false; //action in connection failure
    }
    #return $is_conn;
}}


/**
 * validate URL
 * 
 * @since 0.4
 */
if ( !function_exists( 'is_url' ) ) {
function is_url( $url ){
    
    if ( !$url ) return;
    
    return preg_replace("
        #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
        "'<a href=\"$1\" target=\"_blank\">$3</a>$4'",
        $url
    );
    
}}
    
/*
 * Human Time Difference including weeks and months
 * 
 * @source http://pastebin.com/7zffa3Wn
 * @since 0.3
 */
if ( !function_exists( 'q_human_time_diff' ) ) {
function q_human_time_diff( $from, $to = '' ) {
    $chunks = array(
        array( 60 * 60 * 24 * 365 , '%s year', '%s years' ),
        array( 60 * 60 * 24 * 30 , '%s month', '%s months' ),
        array( 60 * 60 * 24 * 7, '%s week', '%s weeks' ),
        array( 60 * 60 * 24 , '%s day', '%s days' ),
        array( 60 * 60 , '%s hour', '%s hours' ),
        array( 60 , '%s minute', '%s minutes' ),
        array( 1 , '%s second', '%s seconds' ),
    );

    if ( empty( $to ) )
                $to = time();

    $diff = (int) abs( $to - $from );


    for ( $i = 0, $j = count( $chunks ); $i < $j; $i++)
    {
        $seconds = $chunks[$i][0];
        $name1 = $chunks[$i][1];
        $name2 = $chunks[$i][2];

        if ( ( $count = floor( $diff / $seconds ) ) != 0)
            break;
    }

    $since = sprintf( _n( $name1, $name2, $count ), $count );

    $i++;

    if ( $i < $j )
    {
        $seconds_p2 = $chunks[$i][0];
        $name1 = $chunks[$i][1];
        $name2 = $chunks[$i][2];

        if ( ( $count = floor( ( $diff - ( $seconds * $count ) ) / $seconds_p2 ) ) != 0 )
        {
            if( is_rtl() )
                $since = sprintf( _n( $name1, $name2, $count ), $count ) ." ". $since;
            else
                $since = $since. " " . sprintf( _n( $name1, $name2, $count ), $count );
        }
    }

    return $since;
}}


/**
 * Strip Tags
 * 
 * @todo        is tihs really required ??
 */
if ( !function_exists( 'q_strip_tags' ) ) {
function q_strip_tags( $text, $tags = '' ) {
  
    // replace php and comments tags so they do not get stripped  
    $text = preg_replace("@<\?@", "#?#", $text);
    $text = preg_replace("@<!--@", "#!--#", $text);

    // strip tags normally
    $text = strip_tags($text, $tags);

    // return php and comments tags to their origial form
    $text = preg_replace("@#\?#@", "<?", $text);
    $text = preg_replace("@#!--#@", "<!--", $text);

    return $text;
	
}}


/**
 * rip all tags
 * http://php.net/manual/en/function.strip-tags.php
 */
if ( !function_exists( 'q_rip_tags' ) ) {
function q_rip_tags($string) { 
    
    // ----- remove HTML TAGs ----- 
    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
    
    // ----- remove control characters ----- 
    $string = str_replace("\r", '', $string);    // --- replace with empty space
    $string = str_replace("\n", ' ', $string);   // --- replace with space
    $string = str_replace("\t", ' ', $string);   // --- replace with space
    
    // ----- remove multiple spaces ----- 
    $string = trim(preg_replace('/ {2,}/', ' ', $string));
    
    return $string; 

}}


/**
 * chop string function
 * 
 * @since       0.1
 * @pluggable   true
 */
if ( !function_exists( 'q_chop' ) ) {
function q_chop ( $content, $length = 0 ) {
    if ( $length > 0 ) { // trim required, perhaps ##
        if ( strlen( $content ) > $length ) { // long so chop ##
        	return substr( $content , 0, $length ).'...';
        } else { // no chop ##
        	return $content;
        }
    } else { // send as is ##
        return $content;
    }
}}


/**
 * Load Google Web Fonts
 *
 * Description:	A PHP script for loading Google Webfonts' css files in an orderly manner
 * Version:			0.8
 * Author:			Maarten Zilverberg (www.mzilverberg.com)
 * Examples:                    https://github.com/mzilverberg/LoadGoogleWebfonts
 * 
 * Licensed under the GPL license:
 * http://www.gnu.org/licenses/gpl.html
 * 
 * Last but not least:
 * if you like this script, I would appreciate it if you took the time to share it
*/
if ( !function_exists( 'q_load_google_web_fonts' ) )
{
    function q_load_google_web_fonts( $fonts, $use_fallback = true, $debug = false ) {
    
	// if debugging, use &lt; and $gt; notation for output as plain text
	// otherwise, use < and > for output as html
	$debug ? $x = array('&lt;', '&gt;') : $x = array('<', '>');
	// create a new font array
	$array = array();
	// create a new fallback array for storing possible fallback urls
	$fallback_urls = array();
	// determine how many fonts are requested by checking if the array key ['name'] exists
	// if it exists, push that single font into the $array variable
	// otherwise, just copy the $fonts variable to $array
	isset($fonts['name']) ? array_push($array, $fonts) : $array = $fonts;
	// request the link for each font
	foreach ($array as $font) {
            
            // set the basic url
            $base_url = 'https://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $font['name']) . ':';
            $url = $base_url;
            // create a new array for storing the font weights
            $weights = array();
            // if the font weights are passed as a string (from which all spaces will be removed), insert each value into the $weights array
            // otherwise, just copy the weights passed
            if(isset($font['weight'])) {
                gettype($font['weight']) == 'string' ? $weights = explode(',', str_replace(' ', '', $font['weight'])) : $weights = $font['weight'];
            // if font weights aren't defined, default to 400 (normal weight)
            } else {
                $weights = array('400');
            }
            // add each weight to $url and remove the last comma from the url string
            foreach($weights as $weight) { 
                $url .= $weight . ',';
                // if the fallback notation is necessary, add a single weight url to the fallback array
                if($use_fallback && count($weights) != 1) array_push($fallback_urls, "$base_url$weight");
            }
            $url = substr_replace($url, '', -1);
            // normal html output
            echo $x[0] . 'link href="' . $url . '" rel="stylesheet" type="text/css" /' . $x[1] . "\n";
                
	}
	// add combined conditional comment for each font weight if necessary 
	if ( $use_fallback && !empty( $fallback_urls ) ) {
            // begin conditional comment
            echo $x[0] . '!--[if lte IE 8]' . $x[1] . "\n";
            // add each fallback url within the conditional comment
            foreach($fallback_urls as $fallback_url) {
                echo '  ' . $x[0] . 'link href="' . $fallback_url . '" rel="stylesheet" type="text/css" /' . $x[1] . "\n";
            }
            // end conditional comment
            echo $x[0] . '![endif]--' . $x[1] . "\n";
	}
    }  
}


/**
         * Pretty print_r / var_dump
         * 
         * @since       0.1
         * @param       Mixed       $var        PHP variable name to dump
         * @param       string      $title      Optional title for the dump
         * @return      String      HTML output
         */
if ( ! function_exists ( "pr" ) )
{
    function pr( $var = null, $title = null ) 
    { 

        // sanity check ##
        if ( is_null ( $var ) ) { return false; }

        // add a title to the dump ? ##
        if ( $title ) $title = '<h2>'.$title.'</h2>';

        // print it out ##
        print '<pre class="var_dump">'; echo $title; var_dump($var); print '</pre>'; 

    }
}