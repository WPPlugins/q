<?php

/**
 * Widget - Hero Image ##
 *
 * @package WordPress
 * @subpackage 4Trees
 * @since 0.1
 * 
 */

if ( ! class_exists( 'Q_Widget_Hero' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Hero");'));

    class Q_Widget_Hero extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
            parent::__construct(
                'q_widget_hero', // Base ID
                __( 'Q - Hero Image', 'ftframework' ), // Name
                array( 'description' => __( 'Add a top banner image to each page', 'ftframework' ), ) // Args
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
            
            extract( $args );
            $q_hero_default_img = apply_filters( 'widget_title', $instance['q_hero_default_img'] );
            #$q_hero_use_featured = apply_filters( 'widget_title', $instance['q_hero_featured'] );
            #echo 'FEATURE: '.$q_hero_featured.' // '.$instance['q_hero_featured'].'<br />';
            
            // add "follow" class to sidepanel ##
            #$before_widget = str_replace('class="', 'class="blog ', $before_widget);
            #echo $before_widget;
            
            if ( !empty( $q_hero_default_img ) ) {
                
                // hero image - see "q_hero()" in pluggable/theme.php for usage ##
                if ( function_exists( 'q_hero' ) ) {
                    q_hero( $q_hero_default_img, $instance['q_hero_featured'] ); 
                }
                
            }

            #echo $after_widget;
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
            $instance['q_hero_default_img'] = strip_tags( $new_instance['q_hero_default_img'] );
            
            if ( in_array( $new_instance['q_hero_featured'], array( 'true', 'false' ) ) ) {
                $instance['q_hero_featured'] = $new_instance['q_hero_featured'];
            } else {
                $instance['q_hero_featured'] = true;
            }
            #$instance['q_hero_featured'] = strip_tags( $new_instance['q_hero_featured'] );

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
   
            $q_hero_default_img = isset($instance['q_hero_default_img']) ? esc_attr($instance['q_hero_default_img']) : __( 'trees.jpg', 'ftframework' );
            $q_hero_featured = isset($instance['q_hero_featured']) ? esc_attr($instance['q_hero_featured']) : 'true';
            #echo 'FEATURE: '.$q_hero_featured.'<br />';
            
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'q_hero_default_img' ); ?>"><?php _e( 'Default Image ( theme/library/heros/ ):' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'q_hero_default_img' ); ?>" name="<?php echo $this->get_field_name( 'q_hero_default_img' ); ?>" type="text" value="<?php echo esc_attr( $q_hero_default_img ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'q_hero_featured' ); ?>"><?php _e( 'Use featured image?' ); ?></label> 
                <select name="<?php echo $this->get_field_name('q_hero_featured'); ?>" id="<?php echo $this->get_field_id('q_hero_featured'); ?>" class="widefat">
                    <option value="true"<?php selected( $instance['q_hero_featured'], 'true' ); ?>><?php _e('Yes'); ?></option>
                    <option value="false"<?php selected( $instance['q_hero_featured'], 'false' ); ?>><?php _e('No'); ?></option>
                </select>                
            </p>
            <?php 
	}

    } // class hero ##

}
