<?php

/**
 * Widget - MailChimp SignUp ##
 *
 * @package WordPress
 * @subpackage 4Trees
 * @since 0.1
 * 
 */

if ( ! class_exists( 'Q_Widget_MailChimp' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_MailChimp");'));

    class Q_Widget_MailChimp extends WP_Widget 
    {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
            parent::__construct(
                'q_widget_mailchimp', // Base ID
                __('Q - MailChimp Signup','q-textdomain'), // Name
                array( 'description' => __( 'Offer a MailChimp newsletter signup for your visitors - plugin required', 'q-textdomain' ), ) // Args
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
            $before_widget = str_replace('class="', 'class="mailchimp ', $before_widget);
            
            echo $before_widget;
            
            if ( !empty( $title ) )

                // title ##
                echo $before_title; ?><?php _e( $title, 'q-textdomain' ); ?><?php echo $after_title;
                
                if ( q_plugin_is_active( 'mailchimp/mailchimp.php' ) ) {

                    // include mailchimp function ##
                    echo mailchimpSF_signup_form();

                } else {
                    
                    q_plugin_warning( "MailChimp", "MailChimp List Subscribe Form", "mailchimp/mailchimp.php" );
                    
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
            }
            else {
                $title = __( 'Newsletter Signup', 'q-textdomain' );
            }
            ?>
            <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <?php 
	}

    } // class q_Latest_Images

}