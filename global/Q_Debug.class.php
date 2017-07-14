<?php

/**
 * Q Debugging Class
 * 
 * @since       1.0
 * @Author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Debug' ) ) 
{
    
    if ( isset( $_GET["q_debug"] ) ) 
    {
    
        add_action( 'init', array ( 'Q_Debug', 'init' ), 1 );
    
    }
        
    // Q_Debug Class
    class Q_Debug 
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
        
        
        public function __construct()
        {
            
            if ( !defined('SAVEQUERIES') && isset( $_GET['q_debug'] ) && $_GET['q_debug'] == 'sql' ) {
                define('SAVEQUERIES', true);
            }
            
            if ( isset( $_GET['q_debug'] ) && $_GET['q_debug'] == 'http' ) {
                add_filter('http_request_args', 'dump_http', 0, 2);
            }
            
            if ( is_admin() ) { // make sure this is only loaded up in the admin ##
            
                add_action('admin_footer', create_function('$in', 'return add_stop($in, "Display"); '), 10000000);
                
            } else {
                
                add_action('init', create_function('$in', 'return add_stop($in, "Load"); '), 10000000);
                
                add_action('template_redirect', create_function('$in', 'return add_stop($in, "Query"); '), -10000000);
                
                add_action('wp_footer', create_function('$in', 'return add_stop($in, "Display"); '), 10000000);
                
                add_action( 'wp_footer', array( $this, 'q_debug_page_request' ), 1000 );
                
                add_action('wp_print_scripts', 'init_dump');
    
                add_action('init', 'dump_phpinfo');
                
            }
            
            // @todo - review there other actions and filters ##
            #add_action( 'init', 'q_debug_rewrite_rules', 0 );
            #add_action("init", "q_mem_init", 0);
            #add_action("wp_footer", "q_mem_foot", 2000);
            #add_action( 'wp_footer', 'performance', 1001 );
            #add_action('init', 'list_hooked_functions' );

        }
        
        /**
        * dump()
        *
        * @param mixed $in
        * @return mixed $in
        */
        public function dump( $in = null ) {
            
            echo '
        <!-- ';
            foreach ( func_get_args() as $var ) {
                if ( isset($_GET['debug']) ) { echo "\r\n"; }
                if ( is_string($var) ) {
                    echo "$var";
                } else {
                    var_dump($var);
                }
            }
            echo '-->';
            
            return $in;
            
        }
        
        
        /**
        * add_stop()
        *
        * @param mixed $in
        * @param string $where
        * @return mixed $in
        **/
       function add_stop($in = null, $where = null) {
           
           global $sem_stops;
           global $wp_object_cache;
           $queries = get_num_queries();
           $milliseconds = timer_stop() * 1000;
           $out =  "$queries queries - {$milliseconds}ms";
           if ( function_exists('memory_get_usage') ) {
               $memory = number_format(memory_get_usage() / ( 1024 * 1024 ), 1);
               $out .= " - {$memory}MB";
           }
           $out .= " $wp_object_cache->cache_hits cache hits / " . ( $wp_object_cache->cache_hits + $wp_object_cache->cache_misses ).' -->
       <!-- ';
           if ( $where ) {
               $sem_stops[$where] = $out;
           } else {
               dump($out);
           }
           return $in;
           
       }
       
       
       

        /**
         * dump_stops()
         *
         * @param mixed $in
         * @return mixed $in
         **/
        function dump_stops($in = null) {
            if ( $_POST )
                return $in;
            global $sem_stops;
            global $wp_object_cache;
            $stops = '';
            foreach ( $sem_stops as $where => $stop )
                $stops .= "$where: $stop";
                dump("" . trim($stops) . "");
            if ( defined('SAVEQUERIES') && isset($_GET['debug']) && $_GET['debug'] == 'sql' ) {
                global $wpdb;
                foreach ( $wpdb->queries as $key => $data ) {
                    $query = 'SQL: '.rtrim($data[0]);
                    $duration = 'Time: '.number_format($data[1] * 1000, 1) . 'ms';
                    $loc = trim($data[2]);
                    $loc = preg_replace("/(require|include)(_once)?,\s*/ix", '', $loc);
                    $loc = "Initializer: " . preg_replace("/,\s*/", ",\r\n", $loc) . "\r\n";
                    $break = true;
                    dump( $query, $duration, $loc );
                }
            }
            if ( isset($_GET['debug']) && $_GET['debug'] == 'cache' )
                dump($wp_object_cache->cache);
            if ( isset($_GET['debug']) && $_GET['debug'] == 'cron' ) {
                $crons = get_option('cron');
                foreach ( $crons as $time => $_crons ) {
                    if ( !is_array($_crons) )
                        continue;
                    foreach ( $_crons as $event => $_cron ) {
                        foreach ( $_cron as $details ) {
                            $date = date('Y-m-d H:m:i', $time);
                            $schedule = isset($details['schedule']) ? "({$details['schedule']})" : '';
                            if ( $details['args'] )
                                dump("$date: $event $schedule", $details['args']);
                            else
                                dump("$date: $event $schedule");
                        }
                    }
                }
            }
            return $in;
        } # dump_stops()


        /**
         * init_dump()
         *
         * @return void
         **/

        function init_dump() {
            global $hook_suffix;
            if ( !is_admin() || empty($hook_suffix) ) {
                add_action('wp_footer', 'dump_stops', 10000000);
                add_action('admin_footer', 'dump_stops', 10000000);
            } else {
                add_action('wp_footer', 'dump_stops', 10000000);
                add_action("admin_footer-$hook_suffix", 'dump_stops', 10000000);
            }
        } # init_dump()



        /**
         * dump_phpinfo()
         *
         * @return void
         **/

        function dump_phpinfo() {
            if ( isset($_GET['debug']) && $_GET['debug'] == 'phpinfo' ) {
                phpinfo();
                die;
            }
        } # dump_phpinfo()


        /**
         * dump_http()
         *
         * @param array $args
         * @param string $url
         * @return array $args
         **/

        function dump_http($args, $url) {
            dump(preg_replace("|/[0-9a-f]{32}/?$|", '', $url));
            return $args;
        } # dump_http()


        /**
         * dump_trace()
         *
         * @return void
         **/

        function dump_trace() {
            $backtrace = debug_backtrace();
            foreach ( $backtrace as $trace ) {
                dump(
                    'File/Line: ' . $trace['file'] . ', ' . $trace['line'],
                    'Function / Class: ' . $trace['function'] . ', ' . $trace['class']
                );
            }
        } # dump_trace()



        function q_mem_init() {
          $GLOBALS["memused"] = "<!--memory usage after wordpress init: " . memory_get_usage() . " Bytes-->\r\n";
        }

        function q_mem_foot() {
          echo $GLOBALS["memused"];
          echo "<!--memory usage after wordpress is done: " . memory_get_usage() . " Bytes-->\r\n";
          #echo "<!--"; print_r($GLOBALS); echo "-->";
        }


        function performance( $visible = false ) {

            $stat = sprintf(  '%d queries in %.3f seconds, using %.2fMB memory',
                get_num_queries(),
                timer_stop( 0, 3 ),
                memory_get_peak_usage() / 1024 / 1024
                );

            echo $visible ? $stat : "<!-- {$stat} -->\r\n" ;
        }



        // http://www.dev4press.com/2012/tutorials/wordpress/practical/debug-wordpress-rewrite-rules-matching/
        function q_debug_rewrite_rules() {
          global $wp_rewrite;
          echo '
        <!--<pre>';
          if (!empty($wp_rewrite->rules)) {
            echo '<h5>Rewrite Rules</h5>';
            echo '<table><thead><tr>';
            echo '<td>Rule</td><td>Rewrite</td>';
            echo '</tr></thead><tbody>';
            foreach ($wp_rewrite->rules as $name => $value) {
              echo '<tr><td>'.$name.'</td><td>'.$value.'</td></tr>';
            }
            echo '</tbody></table>';
          } else {
            echo 'No rules defined.';
          }
          echo '</pre>-->';
        }


        function q_debug_page_request() {
            global $wp, $template;
            define("FTF_EOL", "");
            echo '
        <!-- Request: ';
            echo empty($wp->request) ? "None" : esc_html($wp->request);
            echo ' -->'.FTF_EOL;
            echo '                
        <!-- Matched Rewrite Rule: ';
            echo empty($wp->matched_rule) ? "None" : esc_html($wp->matched_rule);
            echo ' -->'.FTF_EOL;
            echo '                
        <!-- Matched Rewrite Query: ';
            echo empty($wp->matched_query) ? "None" : esc_html($wp->matched_query);
            echo ' -->'.FTF_EOL;
            echo '                
        <!-- Loaded Template: ';
            echo basename($template);
            echo ' -->'.FTF_EOL;
        }


        function list_hooked_functions($tag=false){
         global $wp_filter;
         if ($tag) {
          $hook[$tag]=$wp_filter[$tag];
          if (!is_array($hook[$tag])) {
            trigger_error("Nothing found for '$tag' hook", E_USER_WARNING);
          return;
          }
         }
         else {
          $hook=$wp_filter;
            ksort($hook);
         }
         echo '<pre>';
         foreach($hook as $tag => $priority){
          echo "<br /><strong>$tag</strong><br />";
          ksort($priority);
          foreach($priority as $priority => $function){
          echo $priority;
          foreach($function as $name => $properties) echo "\t$name<br />";
          }
         }
         echo '</pre>';
         return;
        }
       
        
    }

}




