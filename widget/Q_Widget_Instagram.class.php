<?php

/**
 * Widget - Instagram ##
 *
 * @package WordPress
 * @subpackage Q
 * @since 1.1.0
 * 
 */

if ( ! class_exists( 'Q_Widget_Instagram' ) ) {
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Instagram");'));

    class Q_Widget_Instagram extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct(  ) {
            
            parent::__construct(
                'q_widget_instagram', // Base ID
                'Q - Instagram', // Name
                array( 'description' => __( 'Connect to Instagram API to include images.', 'q-textdomain' ), ) // Args
            );
            
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
         * api_key, photoset_id, per_page, element, size
	 */
	public function widget( $args, $instance ) {
            
            extract( $args );
            
            $title = apply_filters( 'q_widget_instagram_title', $instance['title'] );
            $get = apply_filters( 'q_widget_instagram_get', $instance['get'] );
            $target = apply_filters( 'q_widget_instagram_target', $instance['target'] );
            $user_id = apply_filters( 'q_widget_instagram_user_id', $instance['user_id'] );
            $access_token = apply_filters( 'q_widget_instagram_access_token', $instance['access_token'] );
            $filter = apply_filters( 'q_widget_instagram_filter', $instance['filter'] );
            $template = $instance['template'] ? apply_filters( 'q_widget_instagram_template', $instance['template'] ) : '<li><a href="{{link}}"><img src="{{image}}" /></a></li>' ;
            $resolution = apply_filters( 'q_widget_instagram_resolution', $instance['resolution'] );
            $limit = apply_filters( 'q_widget_instagram_limit', $instance['limit'] );
            $links = apply_filters( 'q_widget_instagram_links', $instance['links'] );
            
            // sort our element ##
            $dom_element = str_replace( array( ".", "#" ), "", $target );
            
            // add widget class to sidepanel ##
            echo $before_widget;
            
            // title ##
            echo $before_title; ?><?php _e( $title, 'q-textdomain' ); ?><?php echo $after_title;

            // add element ##
            echo '<ul id="'.$dom_element.'"></ul>';
            
            // after widget ##
            echo $after_widget;
            
?>
<script type="text/javascript">
if ( typeof jQuery !== 'undefined' ) { 
    jQuery(document).ready(function() { 
        if ( typeof Instafeed !== 'undefined' ) {
            var userFeed = new Instafeed({
                get:            'user',
                target:         '<?php echo $dom_element; ?>',
                userId:         <?php echo $user_id;  ?>,
                accessToken:    '<?php echo $access_token; ?>',
                filter:         '<?php echo $filter; ?>',
                template:       '<?php echo $template; ?>',
                resolution:     '<?php echo $resolution; ?>',
                limit:          <?php echo $limit; ?>,
                links:          <?php echo $links; ?>
            });
            userFeed.run();
        } else {
            console.log("Instagram plugin undefined...");
        }
    });
}
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
            $instance['get'] = strip_tags( $new_instance['get'] );
            $instance['target'] = strip_tags( $new_instance['target'] );
            $instance['user_id'] = strip_tags( $new_instance['user_id'] );
            $instance['access_token'] = strip_tags( $new_instance['access_token'] );
            $instance['filter'] = strip_tags( $new_instance['filter'] );
            $instance['template'] = $new_instance['template'];
            $instance['resolution'] = strip_tags( $new_instance['resolution'] );
            $instance['limit'] = strip_tags( $new_instance['limit'] );
            $instance['links'] = strip_tags( $new_instance['links'] );
            
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
            
            $title =        ( isset( $instance['title'] ) ? $instance['title'] : __( 'Instagram Pics', 'q-textdomain' ) );
            $get =          ( isset( $instance['get'] ) ? $instance['get'] : 'user' );
            $target =       ( isset( $instance['target'] ) ? $instance['target'] : '#instafeed' );
            $user_id =      ( isset( $instance['user_id'] ) ? $instance['user_id'] : '' );
            $access_token = ( isset( $instance['access_token'] ) ? $instance['access_token'] : '' );
            $filter =       ( isset( $instance['filter'] ) ? $instance['filter'] : '' );
            $template =     ( isset( $instance['template'] ) ? $instance['template'] : '' );
            $resolution =   ( isset( $instance['resolution'] ) ? $instance['resolution'] : 'thumbnail' );
            $limit =        ( isset( $instance['limit'] ) ? $instance['limit'] : 6 );
            $links =        ( isset( $instance['links'] ) ? $instance['links'] : 'true' );
            
?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('get'); ?>"><?php _e('Get'); ?>: <a href="http://instafeedjs.com/#standard" target="blank">help</a></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('get'); ?>" name="<?php echo $this->get_field_name('get'); ?>" type="text" value="<?php echo esc_attr( $get ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('DOM Selector'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" type="text" value="<?php echo esc_attr( $target ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('user_id'); ?>"><?php _e('User ID'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('user_id'); ?>" name="<?php echo $this->get_field_name('user_id'); ?>" type="text" value="<?php echo esc_attr( $user_id ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('access_token'); ?>"><?php _e('Access Token'); ?>: <a href="https://instagram.com/accounts/manage_access" target="blank">help</a></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="text" value="<?php echo esc_attr( $access_token ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('JS Filter'); ?>: <a href="http://instafeedjs.com/#advanced" target="blank">help</a></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="text" value="<?php echo esc_attr( $filter ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('HTML Template'); ?>: <a href="http://instafeedjs.com/#templating" target="blank">help</a></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" type="text" value="<?php echo esc_attr( $template ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('resolution'); ?>"><?php _e('Image Size'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('resolution'); ?>" name="<?php echo $this->get_field_name('resolution'); ?>" type="text" value="<?php echo esc_attr( $resolution ); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Image Count'); ?>: ( integer )</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('links'); ?>"><?php _e('Link to Instagram'); ?>: ( true / false )</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('links'); ?>" name="<?php echo $this->get_field_name('links'); ?>" type="text" value="<?php echo esc_attr( $links ); ?>" />
            </p>
            
<?php 

	}

    }
    
}

