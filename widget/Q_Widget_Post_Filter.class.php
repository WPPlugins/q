<?php

/**
 * Widget - Post Filter
 *
 * @since       0.1
 * @todo        CSS / JS requirements
 */

if ( ! class_exists( 'Q_Widget_Post_Filter' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Post_Filter");'));

    class Q_Widget_Post_Filter extends WP_Widget 
    {
        
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
            
            parent::__construct(
                'q_widget_post_filter', // Base ID
                __('Q - Post Filter','q-textdomain'), // Name
                array( 'description' => __( 'Filter Posts by Category', 'q-textdomain' ), ) // Args
            );
            
            // add ajax actions ##
            add_action( 'wp_ajax_post_filter', array(&$this, 'ajax_q_post_filter' ) );
            add_action( 'wp_ajax_nopriv_post_filter', array(&$this, 'ajax_q_post_filter' ) );
            add_action( 'wp_ajax_post_filter_parent', array(&$this, 'ajax_q_post_filter_parent' ) );
            add_action( 'wp_ajax_nopriv_post_filter_parent', array(&$this, 'ajax_q_post_filter_parent' ) );

	}

        
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
            
            // ajax stuff ##
            $ajax_nonce = wp_create_nonce( 'q-post-filter-nonce' );

            extract( $args );
            
            $title = apply_filters( 'title', $instance['title'] );
            $post_type = apply_filters( 'post_type', $instance['post_type'] );
            $taxonomy = apply_filters( 'taxonomy', $instance['taxonomy'] );
            
            // something missing? ##
            $post_type = $post_type ? $post_type : 'post';
            $taxonomy = $taxonomy ? $taxonomy : 'category';
            
            // add "follow" class to sidepanel ##
            $before_widget = str_replace('class="', 'class="q_post_filter ', $before_widget);
            
            echo $before_widget;
            
            // title ##
            echo $before_title; ?><?php echo $title; ?><?php echo $after_title;
            
            if ( !$post_type ) {
                
                echo 'Please enter a Post Type.';
                
            }
            
            if ( !$taxonomy ) {
                
                echo 'Please enter a Taxonomy.';
                
            }
            
            // check if $taxonomy passed and is valid ##
            if ( taxonomy_exists( $taxonomy ) ) {
                
                // is "$taxonomy" hierarchical ? ##
                $taxonomy_parent = '';
                $taxonomy_parent_class = '';
                $select_id_one = 'post_filter';
                $select_id_two = 'post_child';
                
                if ( is_taxonomy_hierarchical( $taxonomy ) ) {
                    
                    $taxonomy_parent = '0';
                    $taxonomy_parent_class = 'parent';
                    $select_id_one = 'post_filter_parent';
                    $select_id_two = 'post_filter';

                }
                
                // term args ##
                $args = array(
                    'orderby'       => 'menu_order, name', 
                    'order'         => 'ASC',
                    'hide_empty'    => false, 
                    'parent'        => $taxonomy_parent,
                    'hierarchical'  => false, 
                    //'get'           => 'all', 
                ); 
                
                $terms = get_terms( $taxonomy, $args );
                // build selects ##
                
                if ( $terms ) { // build ##
                
?>
                <div class="selector">
                    <select class="<?php echo $taxonomy_parent_class; ?>" id="<?php echo $select_id_one; ?>" data-nonce="<?php echo $ajax_nonce; ?>">
                        <option value="" class="default">Select</option>
<?php
    
        foreach ( $terms as $term ) {

            if ( $taxonomy_parent == 0 ) {

                // has_children ##
                $termchildren = get_term_children( $term->term_id, $taxonomy );

                if ( $termchildren ) {
                    
                    echo '<option value="'.$term->term_id.'" data-daddy="'.$term->parent.'" data-term_id="'.$term->term_id.'">'.$term->name.'</option>';
                    
                }

            } else {
                    
                    echo '<option value="'.$term->term_id.'" data-daddy="'.$term->parent.'" data-term_id="'.$term->term_id.'">'.$term->name.'</option>';
                    
            }

        }

?>
                    </select>
                </div>
<?php

            // add child select ##
            if ( is_taxonomy_hierarchical( $taxonomy ) ) {

?>
                <div class="selector disabled">
                    <select class="child" id="<?php echo $select_id_two; ?>" data-nonce="<?php echo $ajax_nonce; ?>" disabled>
                        <option value="" class="default">Select</option>
                    </select>
                </div>
<?php

            } // add child select ##

?>
                
                <ul class="ajax_results post_filter_results"></ul>
<?php

                } // terms built ##
                
            } else {
                
                echo 'Please enter a valid Taxonomy.';
                
            }
            
            // after widget ##
            echo $after_widget;
            
?>
<script type="text/javascript">
    
    jQuery(document).ready(function($) {
        
        // variables ##
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
        var imageurl = ajaxurl.replace('admin-ajax.php','images/');
        
        // parent ##
        $("#post_filter_parent").change(function(e) {
            
            // stop kick back ##
            e.preventDefault();
            
            // disable child selector ##
            $("div.selector").addClass("disabled");
            $("#post_filter").attr('disabled', true);
            $('#post_filter').find('option').not('.default').remove(); // remove all options from select ##
            
            //console.log( $('#post_filter').data('nonce') );
            ajax_nonce = $(this).data("nonce");
            ajax_term_id = $("#post_filter_parent option:selected").val();
            
            if ( ajax_term_id <= 0 ) {
                
                // empty target element ##
                $('.post_filter_results').slideUp().html('');
                return;
                
            }
            
            $.ajax({
                type: "POST",
                dataType: "json",
                url: ajaxurl,
                data: {
                   action: "post_filter_parent",
                   taxonomy: '<?php echo $taxonomy; ?>',
                   //post_type: '<?php echo $post_type; ?>',
                   term_id: ajax_term_id,
                   _ajax_nonce: ajax_nonce
                },
                beforeSend: function () {
                  
                    // loading ##
                    $(".widget_q_widget_post_filter h2.widgettitle").after('<img id="inc_waiting" class="ajax_results_loader" src="'+imageurl+'wpspin_light.gif" />');
                    
                    // empty target element ##
                    $('.post_filter_results').slideUp().html('');
                    
                },
                success: function (response) {
                    
                    _ajax_nonce = response.new_nonce;
                    
                    $('#inc_waiting').remove();
                    $('.error').slideUp().remove();
                    
                    if (response) {
                        
                        if ( response.status === true ) {
                        
                            // no posts returned ##
                            if ( response.posts.length === 0 ) {

                                $(".post_filter_results").append("<li class=\'error\'>No posts found!</li>").slideDown();

                            } else {

                                //console.log(response);
                                $.each(response.posts, function(i, item) {
                                    
                                    // <option value="'.$child_term->term_id.'" data-daddy="'.$child_term->parent.'">'.$child_term->name.'</option>
                                    $("#post_filter").append("<option value=\'"+response.posts[i]["term_id"]+"\' data-daddy=\'"+response.posts[i]["parent"]+"\' >"+response.posts[i]["name"]+"</option>");

                                });

                                // enable child selector ##
                                $("div.selector").removeClass("disabled");
                                $("#post_filter").attr('disabled', false);
                                
                            }
                            
                        } else {
                            
                            $(".post_filter_results").append("<li class=\'error'>"+response.message+"</li>").slideDown();
                            
                        }
                        
                    } else {

                        //console.log('failed');
                        $(".post_filter_results").append("<li class=\'error\'>No posts found!</li>").slideDown();

                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    
                    $('#inc_waiting').remove();
                    
                }
            });
            
            // focus next select ##
            jQuery("#post_filter").focus();
            
        });
        
        
        
        // submit ##
	$("#post_filter").change(function(e) {
            
            e.preventDefault();
            
            // data ##
            //console.log( $('#post_filter').data('nonce') );
            ajax_nonce = $(this).data("nonce");
            ajax_term_id = $("#post_filter option:selected").val();
            ajax_daddy = $("#post_filter option:selected").data("daddy");
            
            if ( ajax_term_id <= 0 ) {
                
                // empty target element ##
                $('.post_filter_results').slideUp().html('');
                return;
                
            }
            
            $.ajax({
                type: "POST",
                dataType: "json",
                url: ajaxurl,
                data: {
                   action: "post_filter",
                   daddy: ajax_daddy,
                   taxonomy: '<?php echo $taxonomy; ?>',
                   post_type: '<?php echo $post_type; ?>',
                   term_id: ajax_term_id,
                   _ajax_nonce: ajax_nonce
                },
                beforeSend: function () {
                  
                    // loading ##
                    $(".widget_q_widget_post_filter h2.widgettitle").after('<img id="inc_waiting" class="ajax_results_loader" src="'+imageurl+'wpspin_light.gif" />');
                    
                    // empty target element ##
                    $('.post_filter_results').slideUp().html('');
                    
                },
                success: function (response) {
                    
                    _ajax_nonce = response.new_nonce;
                    
                    $('#inc_waiting').remove();
                    $('.error').slideUp().remove();
                    
                    if (response) {
                        
                        if ( response.status === true ) {
                        
                            // no posts returned ##
                            if ( response.posts.length === 0 ) {
                                
                                $(".post_filter_results").append("<li class=\'error\'>No posts found!</li>").slideDown();
                                //console.log("error no.1");
                                
                            } else {

                                //console.log(response);
                                $.each(response.posts, function(i, item) {

                                    $(".post_filter_results").append("<li><a href=\'"+response.posts[i]["permalink"]+"\'>"+response.posts[i]["title"]+"</a></li>");

                                });

                                // show target element ##
                                $('.post_filter_results').slideDown();
                                
                            }
                            
                        } else {
                            
                            $(".post_filter_results").append("<li class=\'error'>"+response.message+"</li>").slideDown();;
                            //console.log("error no.2");
                            
                        }
                        
                    } else {

                        //console.log('failed');
                        $(".post_filter_results").append("<li class=\'error\'>No posts found!</li>").slideDown();;
                        //console.log("error no.3");

                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    
                    $('#inc_waiting').remove();
                    //console.log("error no.4");
                    
                }
          });
        });
        
    });
    
</script>
<?php
            
	}

        
        // filter function ##
        function ajax_q_post_filter_parent() {
            
            // check to see if the submitted nonce matches with the
            // generated nonce we created earlier
            $ajaxNonce = 'q-post-filter-nonce';
            if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'q-post-filter-nonce' ) ) {
                
                // negative ##
                $response['status'] = false;
                $response['message'] = 'Opps! Something went wrong.';
                die ();

            }

            // positive ##
            $response['status'] = true;

            // get the submitted parameters
            $response['term_id'] = $_POST['term_id'];
            $response['taxonomy'] = $_POST['taxonomy'];

            // Prep JSON response & generate new, unique nonce
            $new_nonce = wp_create_nonce('q-post-filter-nonce-'. str_replace('.', '', gettimeofday(true)));
            $response['new_nonce'] = $new_nonce;
            
            // term args ##
            $args = array(
                'orderby'           => 'menu_order, name', 
                'order'             => 'ASC',
                'hide_empty'        => true, 
                'child_of'          => $response['term_id'],
            ); 

            $terms = get_terms( $response['taxonomy'], $args );
            
            $response["posts"] = array();
            
            if ( $terms ) {
                
                foreach ( $terms as $term ) {
                    
                    if ( $term->parent > 0 ) { // kids only! ##
                    
                        $response["posts"][] = array (
                            'term_id' => $term->term_id,
                            'parent' => $term->parent,
                            'name' => $term->name
                        );
                        
                    }
                    
                }
                
            }

            // generate the response
            $response = json_encode( 
                $response
            );

            // response output ##
            header( "Content-Type: application/json" );

            // do it ##
            echo $response;

            // IMPORTANT: don't forget to "exit"
            exit;

        }
        
        
        
        
        
        // child filter function ##
        function ajax_q_post_filter() {
            
            // check to see if the submitted nonce matches with the
            // generated nonce we created earlier
            $ajaxNonce = 'q-post-filter-nonce';
            if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'q-post-filter-nonce' ) ) {
                
                // positive ##
                $response['status'] = false;
                $response['message'] = 'Opps! Something went wrong.';
                die ();

            }

            // positive ##
            $response['status'] = true;

            // get the submitted parameters
            $response['daddy'] = $_POST['daddy'];
            $response['term_id'] = $_POST['term_id'];
            $response['taxonomy'] = $_POST['taxonomy'];
            $response['post_type'] = $_POST['post_type'];

            // Prep JSON response & generate new, unique nonce
            $new_nonce = wp_create_nonce('q-post-filter-nonce-'. str_replace('.', '', gettimeofday(true)));
            $response['new_nonce'] = $new_nonce;
            
            // args ##
            $args = array(

                'posts_per_page'   => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => $response['taxonomy'],
                        'field' => 'term_id',
                        'terms' => $response['term_id']
                    )
                ),  
                'orderby'          => 'post_date',
                'order'            => 'DESC',
                'post_type'        => $response['post_type'],

            );
            
            $posts = get_posts($args);
            
            $response["posts"] = array();
            
            if ( $posts ) {
                
                foreach ( $posts as $post ) {
                    
                    setup_postdata( $post );
                    
                    $response["posts"][] = array (
                        'title' => get_the_title( $post->ID ),
                        'permalink' => get_permalink( $post->ID )
                    );
                    
                }
                
                // tidy up ##
                wp_reset_postdata;
                
            }

            // generate the response
            $response = json_encode( 
                $response
            );

            // response output ##
            header( "Content-Type: application/json" );

            // do it ##
            echo $response;

            // IMPORTANT: don't forget to "exit"
            exit;

        }
        
        
        
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
            
            $instance = array();
            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['post_type'] = strip_tags( $new_instance['post_type'] );
            $instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );

            return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
            
            $title =        ( isset( $instance['title'] ) ? $instance['title'] : __( 'Post Filter', 'q-textdomain' ) );
            $post_type =        ( isset( $instance['post_type'] ) ? $instance['post_type'] : __( 'post', 'q-textdomain' ) );
            $taxonomy =        ( isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : __( 'category', 'q-textdomain' ) );
            
?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" type="text" value="<?php echo esc_attr( $post_type ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy:' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" type="text" value="<?php echo esc_attr( $taxonomy ); ?>" />
            </p>
            <?php 
	}

    }

}