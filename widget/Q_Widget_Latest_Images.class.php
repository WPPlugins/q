<?php

/**
 * Widget - Latest Images
 *
 * @since 0.1
 */

if ( ! class_exists( 'Q_Widget_Latest_Images' ) ) {
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Latest_Images");'));
    
    class Q_Widget_Latest_Images extends WP_Widget {
        
        /**
         * Register widget with WordPress.
         */
        public function __construct() 
        {
            
            parent::__construct(
                'q_widget_latest_images', // Base ID
                __( 'Q - Latest Images', 'q-framework' ), // Name
                array( 'description' => __( 'Show the latest images uploaded to your site', 'q-framework' ), ) // Args
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

            $q_latest_images_title = apply_filters( 'widget_title', $instance['q_latest_images_title'] );
            $q_latest_images_count = apply_filters( 'widget_title', $instance['q_latest_images_count'] );

            if ( empty( $instance['q_latest_images_count'] ) || !$q_latest_images_count = absint( $instance['q_latest_images_count'] ) ) {

                $q_latest_images_count = 6;

            }

            echo $before_widget;

            if ( ! empty( $q_latest_images_title ) ) {
                echo $before_title . $q_latest_images_title . $after_title;
            }

            q_latest_images( true, 'new-photos', '', '', $q_latest_images_count );

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
            $instance['q_latest_images_title'] = strip_tags( $new_instance['q_latest_images_title'] );

            $new_instance_number = absint( $new_instance['q_latest_images_count'] );

            if ( empty( $new_instance_number ) || $new_instance_number > 24 || $new_instance_number < 1 ){

                $new_instance_number = 6;

            }

            $instance['q_latest_images_count'] = $new_instance_number;
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

                /*
                if ( isset( $instance[ 'q_latest_images_title' ] ) ) {
                    $q_latest_images_title = $instance[ 'q_latest_images_title' ];
                }
                else {
                    $q_latest_images_title = __( 'Latest Images', 'q-textdomain' );
                }
                if ( isset( $instance[ 'q_latest_images_count' ] ) ) {
                    $q_latest_images_count = $instance[ 'q_latest_images_count' ];
                }
                else {
                    $q_latest_images_count = absint(6);
                }
                 */

                $q_latest_images_title = isset($instance['q_latest_images_title']) ? esc_attr($instance['q_latest_images_title']) : __( 'Latest Images', 'q-textdomain' );
                $q_latest_images_count = isset($instance['q_latest_images_count']) ? absint($instance['q_latest_images_count']) : 6;

                ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'q_latest_images_title' ); ?>"><?php _e( 'Title:' ); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id( 'q_latest_images_title' ); ?>" name="<?php echo $this->get_field_name( 'q_latest_images_title' ); ?>" type="text" value="<?php echo esc_attr( $q_latest_images_title ); ?>" />
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'q_latest_images_count' ); ?>"><?php _e( 'Number to display ( Between 1 - 24 ):' ); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id( 'q_latest_images_count' ); ?>" name="<?php echo $this->get_field_name( 'q_latest_images_count' ); ?>" type="text" value="<?php echo esc_attr( $q_latest_images_count ); ?>" size="3" />
                </p>
                <?php 
            }

    }
    
}

