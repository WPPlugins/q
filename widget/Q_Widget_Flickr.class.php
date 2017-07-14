<?php

/**
 * Widget - Flickr ##
 *
 * @package WordPress
 * @subpackage Q
 * @since 1.1.0
 * 
 */

if ( ! class_exists( 'Q_Widget_Flickr' ) ) {
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Flickr");'));

    class Q_Widget_Flickr extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct(  ) {
            
            parent::__construct(
                'q_widget_flickr', // Base ID
                'Q - Flickr', // Name
                array( 'description' => __( 'Connect to Flickr API to include images.', 'q-textdomain' ), ) // Args
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
            
            $title = apply_filters( 'title', $instance['title'] );
            $api_key = apply_filters( 'api_key', $instance['api_key'] );
            $photoset_id = apply_filters( 'photoset_id', $instance['photoset_id'] );
            $per_page = apply_filters( 'per_page', $instance['per_page'] );
            $element = apply_filters( 'element', $instance['element'] );
            $size = apply_filters( 'size', $instance['size'] );
            $placement = apply_filters( 'placement', $instance['placement'] );
            
            // sort our element ##
            $class_ids = array( ".", "#" );
            $dom_element = str_replace( $class_ids, "", $element );
            
            // add widget class to sidepanel ##
            #$before_widget = str_replace('class="', 'class="q_flickr_wrapper ', $before_widget);
            echo $before_widget;
            
            #if ( !empty( $title ) ) {

                // title ##
                echo $before_title; ?><?php _e( $title, 'q-textdomain' ); ?><?php echo $after_title;
                
                // open UL element ##
                echo '<ul class="'.$dom_element.'">';

                // close UL element ##
                echo '</ul>';
            
            #} // title found ##
                
            echo $after_widget;
            
?>
<script type="text/javascript">
if ( typeof jQuery !== 'undefined' ) { jQuery(document).ready(function() { if ( typeof q_flickr !== 'undefined' ) {
    q_flickr( '<?php echo $api_key; ?>', '<?php echo $photoset_id; ?>', '<?php echo $per_page; ?>', '<?php echo $element; ?>', '<?php echo $size; ?>', '<?php echo $placement; ?>' );
}});}
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
            $instance['api_key'] = strip_tags( $new_instance['api_key'] );
            $instance['photoset_id'] = strip_tags( $new_instance['photoset_id'] );
            $instance['per_page'] = strip_tags( $new_instance['per_page'] );
            $instance['element'] = strip_tags( $new_instance['element'] );
            $instance['size'] = strip_tags( $new_instance['size'] );
            $instance['placement'] = strip_tags( $new_instance['placement'] );
            
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
            
            $title =        ( isset( $instance['title'] ) ? $instance['title'] : __( 'Flickr Pics', 'q-textdomain' ) );
            $api_key =        ( isset( $instance['api_key'] ) ? $instance['api_key'] : '' );
            $photoset_id =        ( isset( $instance['photoset_id'] ) ? $instance['photoset_id'] : '' );
            $per_page =        ( isset( $instance['per_page'] ) ? $instance['per_page'] : __( '10', 'q-textdomain' ) );
            $element =        ( isset( $instance['element'] ) ? $instance['element'] : __( '.q_flickr', 'q-textdomain' ) );
            $size =        ( isset( $instance['size'] ) ? $instance['size'] : __( 'm', 'q-textdomain' ) );
            $placement =        ( isset( $instance['placement'] ) ? $instance['placement'] : __( 'background', 'q-textdomain' ) );
            
?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('api_key'); ?>"><?php _e('API Key'); ?>: <a href="http://www.flickr.com/services/apps/create/" target="blank">help</a></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr( $api_key ); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('photoset_id'); ?>"><?php _e('Photoset ID'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('photoset_id'); ?>" name="<?php echo $this->get_field_name('photoset_id'); ?>" type="text" value="<?php echo esc_attr( $photoset_id ); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('per_page'); ?>"><?php _e('Number of Photos'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('per_page'); ?>" name="<?php echo $this->get_field_name('per_page'); ?>" type="text" value="<?php echo esc_attr( $per_page ); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('element'); ?>"><?php _e('DOM Selector'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('element'); ?>" name="<?php echo $this->get_field_name('element'); ?>" type="text" value="<?php echo esc_attr( $element ); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Image Size'); ?>: <a href="http://www.flickr.com/services/api/misc.urls.html" target="blank">help</a></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" type="text" value="<?php echo esc_attr( $size ); ?>" />
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('placement'); ?>"><?php _e('Image Placement'); ?>: ( background / inline )</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('placement'); ?>" name="<?php echo $this->get_field_name('placement'); ?>" type="text" value="<?php echo esc_attr( $placement ); ?>" />
            </p>
            
<?php 

	}

    }
    
}

