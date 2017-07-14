<?php

/**
 * Widget - Breadcrumb ##
 *
 * @package WordPress
 * @subpackage 4Trees
 * @since 0.1
 * 
 */

if ( ! class_exists( 'Q_Widget_Breadcrumb' ) ) {
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Breadcrumb");'));

    class Q_Widget_Breadcrumb extends WP_Widget 
    {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
            parent::__construct(
                'q_widget_breadcrumb', // Base ID
                'Q - Breadcrumb', // Name
                array( 'description' => __( 'Breadcrumb - display clickable location trail', 'q-textdomain' ), ) // Args
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
            $q_breadcrumb_home = apply_filters( 'widget_title', $instance['q_breadcrumb_home'] );
            $q_breadcrumb_before = apply_filters( 'widget_title', $instance['q_breadcrumb_before'] );
            $q_breadcrumb_sep = apply_filters( 'widget_title', $instance['q_breadcrumb_sep'] );
            
            // add "follow" class to sidepanel ##
            #$before_widget = str_replace('class="', 'class="blog ', $before_widget);
            #echo $before_widget;
            
            if ( !empty( $q_breadcrumb_home ) ) {
                
                // hero image - see "q_hero()" in framework.php for usage ##
                #if ( !is_front_page() ) {
                    q_breadcrumb( 'breadcrumb', $q_breadcrumb_sep, __( $q_breadcrumb_home, 'q-textdomain' ), __( $q_breadcrumb_before, 'q-textdomain' ) );
                #}
                
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
            $instance['q_breadcrumb_home'] = strip_tags( $new_instance['q_breadcrumb_home'] );
            $instance['q_breadcrumb_before'] = strip_tags( $new_instance['q_breadcrumb_before'] );
            $instance['q_breadcrumb_sep'] = strip_tags( $new_instance['q_breadcrumb_sep'] );
            
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
            if ( isset( $instance[ 'q_breadcrumb_home' ] ) ) {
                $q_breadcrumb_home = $instance[ 'q_breadcrumb_home' ];
            }
            else {
                $q_breadcrumb_home = __( 'Home', 'q-textdomain' );
            }
            if ( isset( $instance[ 'q_breadcrumb_before' ] ) ) {
                $q_breadcrumb_before = $instance[ 'q_breadcrumb_before' ];
            }
            else {
                $q_breadcrumb_before = __( "You're in:", 'q-textdomain' );
            }
            if ( isset( $instance[ 'q_breadcrumb_sep' ] ) ) {
                $q_breadcrumb_sep = $instance[ 'q_breadcrumb_sep' ];
            }
            else {
                $q_breadcrumb_sep = __( "&bull;", 'q-textdomain' );
            }
            ?>
            <p>
            <label for="<?php echo $this->get_field_id( 'q_breadcrumb_home' ); ?>"><?php _e( 'Home Title' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'q_breadcrumb_home' ); ?>" name="<?php echo $this->get_field_name( 'q_breadcrumb_home' ); ?>" type="text" value="<?php echo esc_attr( $q_breadcrumb_home ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_id( 'q_breadcrumb_before' ); ?>"><?php _e( 'Text before breadcrumb:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'q_breadcrumb_before' ); ?>" name="<?php echo $this->get_field_name( 'q_breadcrumb_before' ); ?>" type="text" value="<?php echo esc_attr( $q_breadcrumb_before ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_id( 'q_breadcrumb_sep' ); ?>"><?php _e( 'Item Seperation ( HTML allowed ):' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'q_breadcrumb_sep' ); ?>" name="<?php echo $this->get_field_name( 'q_breadcrumb_sep' ); ?>" type="text" value="<?php echo esc_attr( $q_breadcrumb_sep ); ?>" />
            </p>
            <?php 
	}

    } // class q_Latest_Images

}
