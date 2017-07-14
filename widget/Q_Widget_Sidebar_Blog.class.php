<?php

/**
 * Widget - Blog Sidebar ##
 *
 * @package WordPress
 * @subpackage 4Trees
 * @since 0.1
 * 
 */

if ( ! class_exists( 'Q_Widget_SideBar_Blog' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_SideBar_Blog");'));

    class Q_Widget_SideBar_Blog extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
            parent::__construct(
                'q_widget_sidebar_blog', // Base ID
                __('Q - Blog Navigation','q-textdomain'), // Name
                array( 'description' => __( 'Blog Navigation - category, tag & archive links', 'q-textdomain' ), ) // Args
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
            $title = apply_filters( 'widget_title', $instance['title'] );
            
            // add "follow" class to sidepanel ##
            $before_widget = str_replace('class="', 'class="blog ', $before_widget);
            
            echo $before_widget;
            
            if ( ! empty( $title ) ) {
                
                q_get_template_part( "templates/sidebar-blog.php" );
                
            }

            echo $after_widget;
            
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
            if ( isset( $instance[ 'title' ] ) ) {
                $title = $instance[ 'title' ];
            } else {
                $title = __( 'Blog Navigation', 'q-textdomain' );
            }
?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
<?php 
	}

    }

}