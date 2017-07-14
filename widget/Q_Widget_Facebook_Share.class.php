<?php

/**
 * Widget - Facebook Share
 *
 * @since 0.1
 * @todo - allow follow items to be turned on and links added
 */


if ( ! class_exists( 'Q_Widget_Facebook_Share' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Facebook_Share");'));

    class Q_Widget_Facebook_Share extends WP_Widget 
    {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct(  ) {
            parent::__construct(
                'q_widget_facebook_share', // Base ID
                'Q - Facebook Share', // Name
                array( 'description' => __( 'Add a Facebook share option', 'q-textdomain' ), ) // Args
            );
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
            
            // only on single post page ##
            if ( ! is_single() && ! is_page() ) return false;
            
            extract( $args );
            
            $title = apply_filters( 'widget_title', $instance['title'] );
            #pr( $title );
            
            // add "widget_name" class to sidepanel ##
            $before_widget = str_replace('class="', 'class="q_facebook ', $before_widget);
            
            #pr( $before_widget );
            echo $before_widget;
            
            if ( ! empty( $title ) ) {
                
                // add "widget_name" class to sidepanel ##
                $before_title = str_replace('class="', 'class="q_facebook_share ', $before_title);
                
                // title ##
                echo $before_title; ?><?php _e( $title ); ?><?php echo $after_title;

            } // title found ##
                
            echo $after_widget;
            
?>
<div id="fb-root"></div>
<script>
    
jQuery(document).ready(function($) {
    
    // FB share ##
    $(".q_facebook_share").click(function(e) {
            
        e.preventDefault();
        
        if ( typeof FB !== "undefined" ) {
            
<?php 
            
            // grab some details ##
            global $post;
            $fb_name = esc_js(get_the_title( $post->ID ));
            $fb_link = get_permalink( $post->ID );
            $fb_picture = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'square-small' );
            $fb_caption = esc_js(get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true));
            $fb_description = esc_js(q_excerpt_from_id( $post->ID ));
                
?>
            FB.ui (
                {
                    method: 'feed',
                    name: '<?php echo $fb_name; ?>',
                    link: '<?php echo $fb_link; ?>',
                    picture: '<?php echo $fb_picture[0]; ?>',
                    caption: '<?php echo $fb_caption; ?>',
                    description: '<?php echo $fb_description; ?>'
                },
                function(response) {
                    if (response && response.post_id) {
                        jQuery(".q_facebook_share").text('Shared on Facebook!');
                    } else {
                        jQuery(".q_facebook_share").text('Oops!');
                        fb_restore = setTimeout(function(){
                            jQuery(".q_facebook_share").text('Share on Facebook');
                        }, 3000);
                    }
                }
            );

        }
    
    });
          
});
    
</script>
<?php

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
            
            $title =        ( isset( $instance['title'] ) ? $instance['title'] : __( 'Share on Facebook', 'q-textdomain' ) );
            
?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
<?php 

	}

    }

}