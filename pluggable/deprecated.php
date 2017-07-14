<?php

/**
 * Depreacted Functions
 * 
 * @since       1.5.1
 */


// -- from Theme.php -------------------

if ( ! function_exists( 'q_hero' ) ) 
{
/**
 * hero image name generator ##
 * check for id, page, taxonomy or posts type specific image
 * if no matches, return a default image
 * 
 * @param string $default       - image to return if nothing matched - pass full filename, but not path
 * @param boolean $featured     - use featured image
 * @param string $path          - path to hero images from child theme
 * @param string $class         - class selector to add to wrapper ul element
 * @param boolean $echo         - return or echo result
 * @return string               - HTML content
 * 
 * @since 0.1
 */
    function q_hero( $default = 'trees.jpg', $featured = 'true', $path = 'heros/', $class = 'hero', $echo = true ) {

        if ( !is_front_page() ) {
        #if ( $q_options->hero === TRUE ) { // check defined settings ##

            global $post, $thePostID, $q_browser;

            // id available? ##
            $thePostID = ( $post ) ?  $post->ID : $thePostID ; 

            // clean up passed data ##
            $class = wp_kses_data($class);
            $path = wp_kses_data($path);
            $hero_default = wp_kses_data($default);
            $hero_has_featured = false;

            // declare empty variable ##
            $hero_src = '';

            // open html element to contain hero image ##
            #$hero = '<div class="'.$class.'" ';

            // check for featured image ##
            if ( !is_search() && !is_404() && !is_archive() ) {
                if ( $featured == 'true' && has_post_thumbnail( $thePostID ) ) :

                    // Get the Thumbnail URL
                    #echo 'FEATURED<br/>';
                    $post_thumbnail_id = get_post_thumbnail_id( $thePostID );
                    $hero_featured_src = wp_get_attachment_image_src( $post_thumbnail_id, 'hero' ); // get img src ##
                    #pr($hero_featured_src); // test it ##
                    if ( $hero_featured_src ) { $hero_featured = $hero_featured_src[0]; } // take first array item - src ##
                    $hero_has_featured = true; // set variable to true ##

                endif ; // check for default_thumbnail ##
            }

            // by post type ##
            $posttype = get_post_type( $post );

            if ( $featured && $hero_has_featured ) {
                $hero_src = $hero_featured;

            } elseif ( $posttype == 'page' ) { // page stuff - by parent ##
                $daddy = q_ancestors_from_id( $thePostID );
                $hero_src = strtolower( get_the_title( $daddy ));
                $hero_src = str_replace( " ", "-", $hero_src );
                $hero_src .= '.jpg';

            } elseif ( is_search() || is_404() ) { // generic pages ##
                $hero_src = 'search.jpg';

            } elseif ( $posttype != 'page' || $posttype != 'post' ) { // CPT stuff ##
                $hero_src = $posttype.'jpg';

            } elseif ( $posttype == 'post' ) { // blog stuff ##
                $hero_src = 'post.jpg';

            }

            #echo 'HERO: '.$hero_src;

            // settings ##
            #$hero_path = q_get_option("path_child").$path; // theme directory / images
            #$hero_uri = q_get_option("uri_child").$path; // theme directory / images

            // does that image really exist? ##
            if ( !$hero_has_featured ) {
                #if ( !file_exists ( $hero_path.$hero_src ) ) { // not found - use default ##
                if ( file_exists ( q_get_option("path_child").'library/'.$path.$hero_src ) ) { // variable hero found in child theme ##
                    $hero_src = $hero_uri.$hero_src; 
                } else { // parent / child fallback ##
                    $hero_src = q_locate_template( $path.$hero_default, false );
                }
            }

            #echo 'HERO: '.$hero_src;

            // < ie 9 way ##
            #if ( $q_browser['type'] == 'ie8' || $q_browser['type'] == 'ie7' || $q_browser['type'] == 'ie6' ) {

                // compile string ##
                $hero = '<div class="'.$class.'"><img src="'.$hero_src.'" class="'.$class.'_img" alt="HERO"></div><!-- .hero -->';

            // modern CSS way ##
            #} else { 

                // compile string ##
                #$hero = '<div class="'.$class.'" style="background-image: url('.$hero_src.');"></div><!-- .hero -->';

            #}

            // apply filters
            $hero = apply_filters( 'q_hero', $hero );

            // echo or return string ##
            if ( $echo === true ) { 
                echo $hero;
            } else {
                return $hero;
            }

        } // not front page ##

    }
}


if ( ! function_exists( 'q_latest_images' ) ) 
{
/**
 * function to show latest images
 * options can be passed to use a specific tag
 * searches all post types
 * returns html
 * heavily based on - http://wordpress.org/extend/plugins/yd-recent-images/
*/
   function q_latest_images( $attach_only = true, $image_size = '', $use_tag = false, $meta_field = '', $number = 12, $echo = true ) {

       // check transient cache ##
       #$latest_images = get_transient( 'q_latest_images' );
       global $q_transients;
       $latest_images = $q_transients->get( 'q_latest_images' );
       $latest_images = false;
       if ( false === $latest_images || '' === $latest_images ) { // nothing in transients ##

           global $wpdb;
           global $post;

           $andfrom = '';

           if( $attach_only ) {
               $andwhere = " AND at.post_parent != '' ";
               if( $use_tag 
                   && 
                   $mytag = get_post_meta( $post->ID, $meta_field, true ) 
               ) {
                   $andfrom = "
                       , $wpdb->posts AS po
                       , $wpdb->term_relationships AS tr
                       , $wpdb->term_taxonomy AS tt
                       , $wpdb->terms AS te 
                   ";
                   $andwhere .= "
                       AND po.ID = at.post_parent
                       AND tr.object_id = po.ID
                       AND tt.term_taxonomy_id = tr.term_taxonomy_id
                       AND te.term_id = tt.term_id
                       AND te.name = '$mytag' 
                   ";	
               }
           } else {
               $andwhere = '';
           }

           // get the IDs of the latest attachments
           $query = "
               SELECT 
                       at.ID, at.post_title, at.post_parent
               FROM $wpdb->posts AS at
               $andfrom
               WHERE 
                       at.post_type = 'attachment'
                       $andwhere 
               AND
                       at.post_mime_type LIKE 'image/%'
               ORDER BY at.post_date DESC
               LIMIT $number
           ";

           // run sql ##
           $latest_attachments = $wpdb->get_results( $query, ARRAY_A );

           // build latest_img string to return ##
           $latest_images = '';
           $story = '';

           $latest_images .= '<ul class="latest-images">';
           foreach( $latest_attachments as $attachment ) {

               $image = wp_get_attachment_image_src( $attachment['ID'], $image_size );
               #$data = wp_get_attachment_metadata( $attachment['ID'] );
               if( $attachment['post_parent'] ) { // attached image ##

                   $link = get_permalink( $attachment['post_parent'] );
                   $title = get_the_title( $attachment['post_parent'] );
                   $story = get_post_meta( $attachment['ID'], '_wp_attachment_image_alt', true);
                   if ( $story ) { $story = $story.' in '; }
                   #$story = $attachment['post_parent'];

               } else { // unattached image ##

                   $link = '?s=' . preg_replace( '/\-/', '+', sanitize_title( $attachment['post_title'] ) );
                   $title = $attachment['post_title'];

               }

               $latest_images .= '<li>';
               $latest_images .= '    <a href="'.$link.'" title="'.$story.$title.'">';
               $latest_images .= '        <img class="image" alt="NEW" src="'.$image[0].'" style="width:'.$image[1].'px; height:'.$image[2].'px;" />';
               $latest_images .= '    </a>';
               $latest_images .= '</li>';
           }

           // close UL ##
           $latest_images .= '</ul>';

           // apply filters ##
           $latest_images = apply_filters( 'q_latest_images', $latest_images );

           // set transient cache ## 
           #set_transient( 'q_latest_images', $latest_images, 60*60*12 );
           $q_transients->set( 'q_latest_images', $latest_images );

       }

       // echo or return string ##
       if ( $echo === true ) { 
           echo $latest_images;
       } else {
           return $latest_images;
       }

   }
}


if ( ! function_exists( 'q_list_children' ) ) 
{
/**
 * List children of current page
 */
    function q_list_children( $term = 'list-children', $taxonomy = 'page_tag' ) {

        // global
        global $post, $children;;

        // check for term ##
        if ( has_term( $term, $taxonomy, $post->ID ) ) { // ok ##

            // check transient cache ##
            global $q_transients;
            $children = $q_transients->get( 'q_list_children_'.$post->ID );
            #$children = get_transient( 'q_list_children_'.$post->ID );

            if ( false === $children || '' === $children ) { // nothing in transients ##

                // Determine parent page ID
                $parent_id = $post->ID;

                // Build WP_Query() argument array
                $page_args = array(
                    'post_parent'       => $parent_id,
                    'post_type'         => 'page',
                    'order'             => 'ASC',
                    'orderby'           => 'menu_order',
                    'nopaging'          => true
                );

                // Get child pages as a WP_Query() object
                #pr($page_args);
                $children = new WP_Query( $page_args );

                $q_transients->set( 'q_list_children_'.$post->ID, $children );
                #set_transient( 'q_list_children_'.$post->ID, $children, 60*60*24 );

            }

            #$return = '<ul id="list">';
            q_get_template_part("templates/page-loop.php");    
            #$return .= '</ul>';

            wp_reset_query();

            // filter string ##
            #$return = apply_filters( 'q_list_children', $return );

            // echo or return string ##
            #if ( $echo === true ) { 
            #    echo $return;
            #} else {
            #   return $return;
            #}

        }

    }
}


if ( ! function_exists( 'q_logo' ) ) 
{
/**
 * Auto include logo image in theme header 
 */
    function q_logo( $logo = 'logo.png', $path = 'images/', $class = 'logo_img', $echo = true ) {

        // browser detection ##
        global $q_browser;

        // clean up passed data ##
        $class = wp_kses_data($class);
        $path = wp_kses_data($path);
        $logo = wp_kses_data($logo);

        // settings ##
        $logo_src = q_locate_template( $path.$logo, false ); // parent / child check ##

        // default return - empty element ##
        $logo = '<div class="logo_img_empty">&nbsp;</div>';

        // does that image really exist? ##
        if ( $logo_src ) { // logo found theme ##

            // < ie 9 way ##
            if ( $q_browser['type'] == 'ie8' || $q_browser['type'] == 'ie7' || $q_browser['type'] == 'ie6' ) {

                // compile string ##
                $logo = '<div class="'.$class.' logo_img_class"><img src="'.$logo_src.'"  class="'.$class.'_ie" /></div>';

            } else { // modern way ##

                // compile string ##
                $logo = '<div class="'.$class.' logo_img_class" style="background-image: url('.$logo_src.');"></div>';

            }

        }

        // test it ##
        #echo 'LOGO: '.$logo_src;

        // apply filters
        $logo = apply_filters( 'q_logo', $logo );

        // echo or return string ##
        if ( $echo === true ) { 
            echo $logo;
        } else {
            return $logo;
        }

    }
}


if ( ! function_exists( 'q_breadcrumb' ) ) 
{
/*
 * breadcrumb ##
 * @selector    = selector to apply to UL wrapper ##
 * @sep         = html seperator
 * @home
 * @before string HTML Text to add before breadcrumb ##
 */
    function q_breadcrumb( $selector = 'breadcrumb', $sep = '&bull;', $home, $before = '', $echo = true ) {

        if ( !is_front_page() ) { // hide on home page ##

            // get global post object ##
            global $post;

            #pr($post);
            $post_type = 'post'; // default ##
            if ( $post ) {
                $post_type = $post->post_type ? $post->post_type : get_post_type( $post ) ;
            } else {
                $post = get_queried_object();
            }
            $post_label = $post_type; // set to post type ##
            $post_type_object = get_post_type_object( $post_type );

            if ( isset ( $post->cat_name ) ) { // category / tax ##

                $parent = absint( $post->category_parent );
                $post_label = $post->cat_name;

            } elseif ( isset( $post->post_parent ) ) {

                $parent = absint( $post->post_parent );

            }

            // options ##
            $home               = wp_kses_data( $home ? $home : get_bloginfo('name') );
            $sep                = wp_kses_data($sep);  // clean passed seperator ##
            $sep                = '<span class="seperator">'.$sep.'</span>';  // prepare seperator ##
            $blog_page          = get_option( 'page_for_posts' );

            // before action ##
            do_action( 'q_breadcrumb_before' );

            // open breadcrumb ul ##
            $q_breadcrumb = '<ul class="'.$selector.' r-full-pad">';

            // add something before the breadcrumb ? ##
            if ( $before ) {  
                $q_breadcrumb .= '<li class="before">'.wp_kses_data($before).'</li>';
            }

            // add home link ##
            $q_breadcrumb .= '<li><a href="'.get_option('home').'">'.$home.'</a></li>';

            if ( is_single() || is_category() || is_archive() ) { // single post || CPT, archive or category ##

                if ( $post_type !== 'post' ) { // CPT ##

                    $q_breadcrumb .= '<li>'.$sep.'<a href="'.get_option('home').'/'.$post_type_object->rewrite["slug"].'">'.$post_type_object->labels->singular_name.'</a></li>';

                } else { // post ##

                    $q_breadcrumb .= '<li>'.$sep.'<a href="'.get_permalink($blog_page).'">'.get_the_title($blog_page).'</a></li>';

                }

                if ( is_category() ){ // add category title - no link ##

                    $q_breadcrumb .= '<li>'.$sep.single_cat_title( '', false ).'</li>';        

                } elseif ( is_archive() ){

                    if ( is_day() ) {

                        $q_breadcrumb .= '<li>'.$sep.sprintf( __( '%s', 'q-textdomain' ), get_the_date() ).'</li>';

                    } elseif ( is_month() ) {

                        $q_breadcrumb .= '<li>'.$sep.sprintf( __( 'Archive %s', 'q-textdomain' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'q-textdomain' ) ) ).'</li>';

                    } elseif ( is_year() ) {

                        $q_breadcrumb .= '<li>'.$sep.sprintf( __( '%s', 'q-textdomain' ), get_the_date( _x( 'Y', 'yearly archives date format', 'q-textdomain' ) ) ).'</li>';

                    } else {

                        if ( $post_type !== 'post' ) { // CPT ##

                            $q_breadcrumb .= '<li>'.$sep.single_cat_title( '', false ).'</li>';     

                        } else { // post archive ##

                            $q_breadcrumb .= '<li>'.$sep.__( 'Archive', 'q-textdomain').'</li>';

                        }

                    }

                }

            }

            if ( is_single() ) { // single post, archive or category - without href ##

                $q_breadcrumb .= '<li>'.$sep.get_the_title().'</li>';     

            }

            if ( is_page() && !is_front_page() ) { // viewing a page that is not the front page ##

                $get_post_ancestors = get_post_ancestors( $post );
                $ancestors = array_reverse( $get_post_ancestors );

                foreach( $ancestors as $page ) { // loop all page ancestors ##

                    $q_breadcrumb .= '<li>'.$sep.'<a href="'.get_permalink($page).'">'.get_the_title($page).'</a></li>';

                }

                // add current page without a href ##
                $q_breadcrumb .= '<li>'.$sep.get_the_title().'</li>';

            }

            if ( is_search() ) { // search page ##

                $q_breadcrumb .= '<li>'.$sep.__("Search Results", "q-textdomain").'</li>';

            } elseif (is_404() ) { // 404 ##

                $q_breadcrumb .= '<li>'.$sep.__("404", "q-textdomain").'</li>';

            }

            if ( is_home() ) {
                if ( $blog_page ) { 
                    $home = get_page( $blog_page );
                    #setup_postdata( $home ); // set-up required post data ##
                    $q_breadcrumb .= '<li>'.$sep.get_the_title( $home->ID ).'</li>';
                    #rewind_posts(); // rewind post object ##
                }
            }

            // add closing ul ##
            $q_breadcrumb .= '</ul><!-- eof ul.breadcrumb -->';

            // filter string ##
            $q_breadcrumb = apply_filters( 'q_breadcrumb', $q_breadcrumb );

            // after action ##
            do_action( 'q_breadcrumb_after' );

            // echo or return string ##
            if ( $echo === true ) { 
                echo $q_breadcrumb;
            } else {
                return $q_breadcrumb;
            }

        } // hide on home page ##

    }
} // breadcrumbs ##


if ( ! function_exists( 'q_authors' ) ) 
{
/**
 * list authors function ##
 * pass in array of allowed roles by title ##
 */
    function q_authors( $allowed = array ( 'editor', 'author' ) ) {

        global $wpdb;
        $authors = $wpdb->get_results("SELECT ID, user_nicename from $wpdb->users ORDER BY display_name");

        $q_authors = ''; // define empty variable ##

        // loop authors ##
        foreach ( $authors as $author ) {

            // get user role by id ##
            $role = q_get_roles_by_user_id( $author->ID );

            // check first role assigned to user against allowed values ##
            if ( in_array( $role[0], $allowed ) ) {    

            // user avatar ##
            $avatar = '';

            #$img_path = get_bloginfo("template_url").'/images/holder/'; // path to holder ##
            $holder_path = 'images/holder/';
            $author_src = '<img src="'.q_locate_template( $holder_path.'author.png', false ).'" />'; // set holding src variable ##
            $avatar = get_avatar($author->ID); // try to get current avatar ##
            if ( $avatar ) {
                $author_src = $avatar; // update src to user avatar ##
            }

            // build string ##
            $q_authors .=  '<li>'; // open <li> ##
            $q_authors .=      '<div class="author">';
            $q_authors .=          '<div class="use_wrap whole">';
            $q_authors .=              $author_src; // avatar image ##
            $q_authors .=              '<h2>';
            $q_authors .=                  '<a href="'.get_bloginfo('url').'/author/'.the_author_meta('user_nicename', $author->ID).'" title="'.the_author_meta('display_name', $author->ID).'" class="use">';
            $q_authors .=                      the_author_meta('display_name', $author->ID);
            $q_authors .=                  '</a>';
            $q_authors .=              '</h2>';
            $q_authors .=              '<div class="content">';

        // If a user has filled out their description, show a bio on their entries.
        $desc = ''; // empty ##
        if ( get_the_author_meta( 'description', $author->ID ) ) { 
        $desc = q_chop( get_the_author_meta( 'description' ), 120 );

            $q_authors .=                  '<div class="description">'.$desc.'</div>';

        }

            $q_authors .=              '</div>';
            $q_authors .=          '</div>';

        if ( get_the_author_meta('user_url', $author->ID) ) {

            $q_authors .=          '<div class="web">';
            $q_authors .=              '<a href="'.the_author_meta('user_url', $author->ID).'" title="'.__("Open in new window", "q-textdomain").'" target="_blank">';
            $q_authors .=                  the_author_meta('user_url', $author->ID);
            $q_authors .=              '</a>';
            $q_authors .=          '</div>';
        } // author URL ##
            $q_authors .=      '</div>';
            $q_authors .=  '</li>';

            }
        }

        // apply filters ##
        $q_authors = apply_filters( 'q_authors', $q_authors );

        // return ##
        return $q_authors;

    }
}




// -- from Wordpress.php -------------------


/** 
 * add custom field function 
 * 
 * @since 0.1
 */
if ( !function_exists( 'q_field_func' ) ) {
add_shortcode('field', 'q_field_func');
function q_field_func( $atts ) {
   global $post;
   $name = $atts['name'];
   if ( empty ( $name ) ) return;
   return get_post_meta ( $post->ID, $name, true );
}}


/**
 * get top level ancestor ##
 * 
 * @since 0.1
 */
if ( !function_exists( 'q_ancestors_from_id' ) ) {
function q_ancestors_from_id ( $id ) {
    $current = get_post($id);
    if(!$current->post_parent){
        return $current->ID;
    } else {
        return q_ancestors_from_id($current->post_parent);
    }
}}


/**
 * return page slug ##
 * 
 * @added for GH
 * @source http://www.tcbarrett.com/2011/09/wordpress-the_slug-get-post-slug-function/
 * @since 0.7
 */
if ( !function_exists('q_the_slug') ) {
function q_the_slug($echo=true){
    $slug = basename(get_permalink());
    do_action('before_slug', $slug);
    $slug = apply_filters('slug_filter', $slug);
    if( $echo ) echo $slug;
    do_action('after_slug', $slug);
    return $slug;
}}


/**
 * flatten arg list ##
 * 
 * @source http://wordpress.org/extend/plugins/next-page/
 * @since 0.3
 */
if ( !function_exists('q_flatten_page_list') ) {
function q_flatten_page_list($exclude = '') {
   $args = 'sort_column=menu_order&sort_order=asc';
   $pagelist = get_pages($args);
   $mypages = array();
   if (!empty($exclude)) {
       $excludes = split(',', $exclude);
       foreach ($pagelist as $thispage) {
           if (!in_array($thispage->ID, $excludes)) {
               $mypages[] += $thispage->ID;
           }
       }
   }
   else {
       foreach ($pagelist as $thispage) {
           $mypages[] += $thispage->ID;
       }
   }
   return $mypages;
}}



if ( ! function_exists( 'q_has_shortcode' ) ) 
{
/* 
 * check the current post for the existence of a short code
 * 
 * @added for GH
 * @since 0.7
 */
    function q_has_shortcode($shortcode = '') {
            
        // if no short code was provided, return false
        if (!$shortcode) {
            return false;
	}
        
        // load the post
        global $post;
	
        // content to scan ##
        $content = isset( $post->post_content ) ? $post->post_content : '' ;
        
	// check the post content for the short code
	if ( stripos( $content, '[' . $shortcode) !== false ) {
            
            // we have found the short code
            #echo 'shortcode ['.$shortcode.'] found';
            return true;
            
        }
        
    }
    
}



if ( !function_exists('q_next_page_link') ) 
{
/**
 * next page link
 * 
 * @source http://wordpress.org/extend/plugins/next-page/
 * @since 0.3
 */
    function q_next_page_link( $after_link = '&raquo;', $linktext = '%title%', $exclude = '', $echo = true ) {

        global $post;

        $pagelist = q_flatten_page_list($exclude);
        $current = array_search($post->ID, $pagelist);
        if (array_key_exists($current+1, $pagelist) ) {
            $nextID = $pagelist[$current+1];
        }
        if ( isset($nextID) ) {

            $after_link = stripslashes( $after_link );
            $linkurl = get_permalink( $nextID );
            $title = get_the_title( $nextID );
            if (strpos($linktext, '%title%') !== false) {
                $linktext = str_replace('%title%', $title, $linktext);
            }
            $link = '<a href="' . $linkurl . '" title="' . $title . '">'.$linktext.' '.$after_link.'</a>';

            if ( $echo === false ) {
                return $link;
            } 
            echo $link;

        }

    }
}



if ( !function_exists('q_previous_page_link') ) 
{
/**
 * previous page link
 * 
 * @source http://wordpress.org/extend/plugins/next-page/
 * @since 0.3
 */
    function q_previous_page_link( $before_link = '&laquo;', $linktext = '%title%', $exclude = '', $echo = true ) {

        global $post;

        $pagelist = q_flatten_page_list($exclude);
        $current = array_search( $post->ID, $pagelist );
        if (array_key_exists($current-1, $pagelist) ) {
            $prevID = $pagelist[$current-1];
        }
        if ( isset( $prevID ) ) {

            $before_link = stripslashes( $before_link );
            $linkurl = get_permalink($prevID);
            $title = get_the_title($prevID);
            if (strpos($linktext, '%title%') !== false) {
                $linktext = str_replace('%title%', $title, $linktext);
            }

            $link = '<a href="' . $linkurl . '" title="' . $title . '">'.$before_link.' '.$linktext.'</a>';

            if ( $echo === false ) {
                return $link;
            } 
            echo $link;

        }

    }
}




if ( ! function_exists( 'q_add_update_option' ) ) 
{
/**
 * add or update option ##
 * 
 * @since 0.2
 */
    function q_add_update_option ( $option_name, $new_value, $deprecated = ' ', $autoload = 'no' ) {
        if ( get_option( $option_name ) != $new_value ) {
            update_option( $option_name, $new_value );
        } else {
            add_option( $option_name, $new_value, $deprecated, $autoload );
        }
    }
}





if ( ! function_exists( 'q_get_page_depth' ) ) 
{
/**
 * get page depth
 * 
 * @return integer
 * @since 0.1
 */
    function q_get_page_depth( $id = '' ){

        return count( get_post_ancestors( get_post( $id )));

    }
}



/**
 * don't send an empty search to the blog page
 * 
 * @since 0.1
 */
#add_filter( 'request', 'q_request_filter' );
function q_request_filter( $query_vars ) {
    if( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
        $query_vars['s'] = " ";
    }
    return $query_vars;
}





/**
 * Remove standard image sizes so that these sizes are not
 * created during the Media Upload process
 *
 * Tested with WP 3.2.1
 *
 * Hooked to intermediate_image_sizes_advanced filter
 * See wp_generate_attachment_metadata( $attachment_id, $file ) in wp-admin/includes/image.php
 *
 * @param $sizes, array of default and added image sizes
 * @return $sizes, modified array of image sizes
 * @author http://www.wpmayor.com/code/remove-image-sizes-in-wordpress/
 */
if ( ! function_exists ( 'q_filter_image_sizes' ) )
{
    function q_filter_image_sizes( $sizes ) {

        unset( $sizes['slides']);
        unset( $sizes['slides-small']);
        unset( $sizes['home']);
        unset( $sizes['new-photos']);
        unset( $sizes['hero']);

        return $sizes;

    }
}



/**
* Get the current post type in the WordPress Admin
* 
* @since   0.1
* @link    http://themergency.com/wordpress-tip-get-post-type-in-admin/
*/
if ( !function_exists( 'q_get_current_post_type' ) )
{
    function q_get_current_post_type() 
    {

        global $post, $typenow, $current_screen;

        // we have a post so we can just get the post type from that
        if ( $post && $post->post_type ){
            return $post->post_type;
        }

        // check the global $typenow - set in admin.php
        elseif( $typenow ) {
            return $typenow;
        }

        // check the global $current_screen object - set in sceen.php
        elseif( $current_screen && $current_screen->post_type ) {
            return $current_screen->post_type;
        }

        //lastly check the post_type querystring
        elseif( isset( $_REQUEST['post_type'] ) ) {
            return sanitize_key( $_REQUEST['post_type'] );
        }

        // we do not know the post type!
        return null;

    }
    
}




/**
* disable all site feeds ##
 * 
 * @since 1.0
*/
function q_disable_feed() 
{
    
    wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
    
}

if ( ! function_exists( 'q_disable_feeds' ) ) 
{
    function q_disable_feeds() {
        add_action('do_feed', 'q_disable_feed', 1);
        add_action('do_feed_rdf', 'q_disable_feed', 1);
        add_action('do_feed_rss', 'q_disable_feed', 1);
        add_action('do_feed_rss2', 'q_disable_feed', 1);
        add_action('do_feed_atom', 'q_disable_feed', 1);
    }
}
