<?php

/**
 * Widget - Follow
 *
 * @since       0.1
 * @todo        allow follow items to be turned on and links added
 */

if ( ! class_exists( 'Q_Widget_Follow' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Follow");'));

    class Q_Widget_Follow extends WP_Widget 
    {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct(  ) {
            parent::__construct(
                'q_widget_follow', // Base ID
                'Q - Follow', // Name
                array( 'description' => __( 'Offer fast-loading links to your social network sites', 'q-textdomain' ), ) // Args
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
            $follow_widget = apply_filters( 'widget_follow', $instance['follow'] );
            #pr($follow_widget);
            
            // add "follow" class to sidepanel ##
            $before_widget = str_replace('class="', 'class="follow ', $before_widget);
            #pr( $before_widget );
            echo $before_widget;
            
            #if ( !empty( $title ) ) {

                // title ##
                echo $before_title; ?><?php _e( $title, 'q-textdomain' ); ?><?php echo $after_title;

                // open UL element ##
                echo '<ul class="fade">';
                
                // default settings ##
                $follow = array (
                    "facebook"  => array( 'img' => "facebook", 'desc' => "Fan on Facebook", 'url' => "http://www.facebook.com/" ),
                    "twitter"   => array( 'img' => "twitter", 'desc' => "Follow on Twitter", 'url' => "http://twitter.com/" ),
                    "flickr"    => array( 'img' => "flickr", 'desc' => "Photos on Flickr", 'url' => "http://www.flickr.com/photos/" ),
                    "youtube"   => array( 'img' => "youtube", 'desc' => "Videos on YouTube", 'url' => "http://www.youtube.com/user/" ),
                    "meetup"    => array( 'img' => "meetup", 'desc' => "Meetup with Us", 'url' => "http://www.meetup.com/" ),
                    "pinterest" => array( 'img' => "pinterest", 'desc' => "Pin on Pinterest", 'url' => "http://pinterest.com/" ),
                    "instagram" => array( 'img' => "instagram", 'desc' => "Photos on Instagram", 'url' => "http://instagram.com/" ),
                    "google"    => array( 'img' => "google", 'desc' => "Google+", 'url' => "http://plus.google.com/" ),
                    "contact"   => array( 'img' => "contact", 'desc' => "Get in Contact", 'url' => get_option("home") ),
                    "rss"       => array( 'img' => "rss", 'desc' => "RSS Feed", 'url' => get_bloginfo('rss2_url') ),
                );
                
                // loop follow items ##
                foreach ( $follow as $item ) {
                    
                    if ( array_key_exists( $item["img"], $follow_widget ) ) {
                        
                        #pr( $item );
                        
                        // image - search child and parent ##
                        $img = q_locate_template( 'images/q/social_'.$item["img"].'.png', false );
                        
                        // url ##
                        unset( $url_key, $url );
                        $url_key = $item["img"];
                        $url = $follow_widget[$url_key];
                        #echo 'url: '.$url.'<br />';
                        
                        if ( $url ) {
                        
                            $pos = strpos( $url, 'http://' );
                            $url = ( $pos ? $url : 'http://'.$url );

                            // target ##
                            $target = '_blank';
                            if ( $item['img'] == "contact" ) { // internal page ##
                                $target = "";
                            }
                        
?>
                <li class="<?php echo $item['img']; ?>">
                    <a href="<?php echo $url; ?>" title="<?php _e( $item["desc"], 'q-textdomain' ); ?>" target="<?php echo $target; ?>">
                        <img src="<?php echo $img; ?>" alt="<?php echo $item["img"]; ?>" />
                    </a>
                </li>
<?php   

                        } // url set ##
                    
                    } // item saved ##
                    
                }

                // open UL element ##
                echo '</ul>';
            
            #} // title found ##
                
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
            $instance['follow'] = array(
                "facebook"  => strip_tags( is_url( $new_instance['facebook'] ) ),
                "twitter"   => strip_tags( is_url( $new_instance['twitter'] ) ),
                "flickr"    => strip_tags( is_url( $new_instance['flickr'] ) ),
                "youtube"   => strip_tags( is_url( $new_instance['youtube'] ) ),
                "meetup"    => strip_tags( is_url( $new_instance['meetup'] ) ),
                "pinterest" => strip_tags( is_url( $new_instance['pinterest'] ) ),
                "instagram" => strip_tags( is_url( $new_instance['instagram'] ) ),
                "google"    => strip_tags( is_url( $new_instance['google'] ) ),
                "contact"   => strip_tags( is_url( $new_instance['contact'] ) ),
                "rss"       => strip_tags( is_url( $new_instance['rss'] ) )
            );
            
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
            
            $title =        ( isset( $instance['title'] ) ? $instance['title'] : __( 'Follow Us', 'q-textdomain' ) );
            $facebook =     ( isset( $instance['follow']['facebook'] ) ? $instance['follow']['facebook'] : '' );
            $twitter =      ( isset( $instance['follow']['twitter'] ) ? $instance['follow']['twitter'] : '' );
            $flickr =       ( isset( $instance['follow']['flickr'] ) ? $instance['follow']['flickr'] : '' );
            $youtube =      ( isset( $instance['follow']['youtube'] ) ? $instance['follow']['youtube'] : '' );
            $meetup =       ( isset( $instance['follow']['meetup'] ) ? $instance['follow']['meetup'] : '' );
            $pinterest =    ( isset( $instance['follow']['pinterest'] ) ? $instance['follow']['pinterest'] : '' );
            $instagram =    ( isset( $instance['follow']['instagram'] ) ? $instance['follow']['instagram'] : '' );
            $google =       ( isset( $instance['follow']['google'] ) ? $instance['follow']['google'] : '' );
            $contact =      ( isset( $instance['follow']['contact'] ) ? $instance['follow']['contact'] : '' );
            $rss =          ( isset( $instance['follow']['rss'] ) ? $instance['follow']['rss'] : '' );
            
?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo esc_attr( $facebook ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo esc_attr( $twitter ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('flickr'); ?>"><?php _e('Flickr'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('flickr'); ?>" name="<?php echo $this->get_field_name('flickr'); ?>" type="text" value="<?php echo esc_attr( $flickr ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e('YouTube'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" type="text" value="<?php echo esc_attr( $youtube ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('meetup'); ?>"><?php _e('Meetup'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('meetup'); ?>" name="<?php echo $this->get_field_name('meetup'); ?>" type="text" value="<?php echo esc_attr( $meetup ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('pinterest'); ?>"><?php _e('Pinterest'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('pinterest'); ?>" name="<?php echo $this->get_field_name('pinterest'); ?>" type="text" value="<?php echo esc_attr( $pinterest ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('instagram'); ?>"><?php _e('Instagram'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('instagram'); ?>" name="<?php echo $this->get_field_name('instagram'); ?>" type="text" value="<?php echo esc_attr( $instagram ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('google'); ?>"><?php _e('Google+'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('google'); ?>" name="<?php echo $this->get_field_name('google'); ?>" type="text" value="<?php echo esc_attr( $google ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('contact'); ?>"><?php _e('Contact'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('contact'); ?>" name="<?php echo $this->get_field_name('contact'); ?>" type="text" value="<?php echo esc_attr( $contact ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('rss'); ?>"><?php _e('RSS'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" type="text" value="<?php echo esc_attr( $rss ); ?>" />
            </p>
<?php 

	}

    }

}