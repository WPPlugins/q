<?php

/**
 * Widget - Twitter
 *
 * @since   1.0
 */


if ( ! class_exists( 'Q_Widget_Twitter' ) ) 
{
    
    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("Q_Widget_Twitter");'));

    class Q_Widget_Twitter extends WP_Widget 
    {

        /**
         * Register widget with WordPress.
         */
        public function __construct(  ) {
            parent::__construct(
                'q_widget_twitter', // Base ID
                'Q - Twitter', // Name
                array( 'description' => __( 'Show tweets from a twitter user or query.', 'q-textdomain' ), ) // Args
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

            if ( !function_exists('q_twitter') ) {
                return;
            }

            extract( $args );

            $title                      = apply_filters( 'widget_title', $instance['title'] );
            $username                   = apply_filters( 'widget_username', $instance['username'] );
            $query                      = apply_filters( 'widget_query', $instance['query'] );
            $count                      = apply_filters( 'widget_count', $instance['count'] );

            $consumer_key               = apply_filters( 'widget_consumer_key', $instance['consumer_key'] );
            $consumer_secret            = apply_filters( 'widget_consumer_secret', $instance['consumer_secret'] );
            $oauth_access_token         = apply_filters( 'widget_oauth_access_token', $instance['oauth_access_token'] );
            $oauth_access_token_secret  = apply_filters( 'widget_oauth_access_token_secret', $instance['oauth_access_token_secret'] );

            $debug                      = apply_filters( 'widget_debug', $instance['debug'] );

        if ( !$consumer_key || !$consumer_secret || !$oauth_access_token || !$oauth_access_token_secret ) {

            echo '<ul class="twitter"><li>Missing oAuth Data.</li></ul>';

        } else { // carry on ##

            if ( $username && $query ) { // carry on ##

                // defaults ##
                $count = $count ? $count : 20 ; // count ##
                $debug = $debug ? $debug : false ; // debug ##

                // grab tweets ##   
                $args = array(
                    'debug'                     => $debug, // debug returned object and errors ##
                    'consumer_key'              => $consumer_key, /* consumer key - https://dev.twitter.com/apps/ */
                    'consumer_secret'           => $consumer_secret, /* consumer secret */
                    'oauth_access_token'        => $oauth_access_token, /* generated oauth access token */
                    'oauth_access_token_secret' => $oauth_access_token_secret, /* generated oauth access token secret */
                    'mode'                      => 'search', /* sitestream, search, home_timeline, user_timeline */
                    'username'                  => $username,
                    'count'                     => $count,
                    'retweet'                   => true,
                    'follow'                    => $username,
                    'search'                    => $query, // 'from:EKuntzelman',
                    'cache'                     => true,
                    'cacheID'                   => 'q_twitter_cache' // TODO - automate ##
                );

                // instatiate Q_Twitter Class ##
                $q_twitter = New Q_Twitter();

                // grab some tweets ##
                $tweets = $q_twitter->get( $args );

                #$tweets = q_twitter( $args );

                #pr($args);

                if ( $tweets ) {

                    // add "widget" class to sidepanel ##
                    $before_widget = str_replace('class="', 'class="q_twitter ', $before_widget);
                    echo $before_widget;

                    // title ##
                    echo $before_title; ?><?php _e( $title, 'q-textdomain' ); ?><?php echo $after_title;

    ?>
                    <ul class="twitter">
    <?php

                    foreach ( $tweets as $tweet ) {

                        // format text correctly ##
                        $tweet->text = $q_twitter->add_href( $tweet->text ); // add href link ##
                        $tweet->text = $q_twitter->add_username( $tweet->text ); // add username link ##
                        $tweet->text = $q_twitter->add_hashtag( $tweet->text ); // add hashtag link ##

    ?>
                        <li>
                            <div class="avatar">
                                <a rel="nofollow" target="_blank" class="mt_avatar" href="http://twitter.com/<?php echo $tweet->user->screen_name; ?>">
                                    <img src="<?php echo $tweet->user->profile_image_url; ?>" alt="<?php echo $tweet->user->name; ?>" border="0" class="icon"/>
                                </a>
                            </div>
                            <div class="meta">
                                <div class="mt_header">
                                    <a rel="nofollow" target="_blank" class="mt_user" href="http://twitter.com/<?php echo $tweet->user->screen_name; ?>"><?php echo $tweet->user->name; ?></a>
                                    <span class="mt_screen_name">@<?php echo $tweet->user->screen_name; ?></span>
                                </div>
                                <div class="mt_text"><?php echo $tweet->text; ?></div>
                                <div class="mt_footer">
    <?php

                                    // format date correctly ##
                                    #$date = date("j M Y", strtotime($tweet->created_at)); // 1 Jul 2011
                                    $date = q_human_time_diff( strtotime($tweet->created_at) );

    ?>
                                    <div class="time"><?php _e("Tweeted"); ?>: <?php echo $date; ?> <?php _e("ago"); ?></div>
    <?php

                                // if retweet - TODO ##
                                if ( isset($tweet->entities->retweeted) && $tweet->entities->retweeted === true ) {

    ?>
                                    <span class="image_r"></span>Retweeted by <a rel="nofollow" target="_blank" class="mt_retweet" href="http://twitter.com/<?php echo $tweet->user->screen_name; ?>"><?php echo $tweet->user->name; ?></a>
    <?php

                                }
                                // end retweet ##

    ?>
                                </div>
                            </div>
                        </li>
    <?php

                    } // loop ##

    ?>
                    </ul>
    <?php 

                    echo $after_widget;

                    } // results ##

                } // ouath ok ##

            } // basics provided ##

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
            $instance['title']                      = strip_tags( $new_instance['title'] );
            $instance['username']                   = strip_tags( $new_instance['username'] );
            $instance['query']                      = strip_tags( $new_instance['query'] );
            $instance['count']                      = strip_tags( $new_instance['count'] );

            $instance['consumer_key']               = strip_tags( $new_instance['consumer_key'] );
            $instance['consumer_secret']            = strip_tags( $new_instance['consumer_secret'] );
            $instance['oauth_access_token']         = strip_tags( $new_instance['oauth_access_token'] );
            $instance['oauth_access_token_secret']  = strip_tags( $new_instance['oauth_access_token_secret'] );

            $instance['debug']                      = (bool)( $new_instance['debug'] );

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

            $title                      = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Twitter', 'q-textdomain' ) );
            $username                   = ( isset( $instance['username'] ) ? $instance['username'] : __( '_qstudio', 'q-textdomain' ) );
            $query                      = ( isset( $instance['query'] ) ? $instance['query'] : __( 'from:_qstudio', 'q-textdomain' ) );
            $count                      = ( isset( $instance['count'] ) ? $instance['count'] : __( '20', 'q-textdomain' ) );

            $consumer_key               = ( isset( $instance['consumer_key'] ) ? $instance['consumer_key'] : '' );
            $consumer_secret            = ( isset( $instance['consumer_secret'] ) ? $instance['consumer_secret'] : '' );
            $oauth_access_token         = ( isset( $instance['oauth_access_token'] ) ? $instance['oauth_access_token'] : '' );
            $oauth_access_token_secret  = ( isset( $instance['oauth_access_token_secret'] ) ? $instance['oauth_access_token_secret'] : '' );

            $debug                      = ( isset( $instance['debug'] ) ? $instance['debug'] : __( '0', 'q-textdomain' ) );

    ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Query'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('query'); ?>" name="<?php echo $this->get_field_name('query'); ?>" type="text" value="<?php echo esc_attr( $query ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Count'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('consumer_key'); ?>"><?php _e('Consumer Key'); ?>: (<a href="https://dev.twitter.com/apps/" target="_blank" title="API Signup">API</a>)</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('consumer_key'); ?>" name="<?php echo $this->get_field_name('consumer_key'); ?>" type="text" value="<?php echo esc_attr( $consumer_key ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('consumer_secret'); ?>"><?php _e('Consumer Secret'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('consumer_secret'); ?>" name="<?php echo $this->get_field_name('consumer_secret'); ?>" type="text" value="<?php echo esc_attr( $consumer_secret ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('oauth_access_token'); ?>"><?php _e('oAuth Access Token'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('oauth_access_token'); ?>" name="<?php echo $this->get_field_name('oauth_access_token'); ?>" type="text" value="<?php echo esc_attr( $oauth_access_token ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('oauth_access_token_secret'); ?>"><?php _e('oAuth Access Token Secret'); ?>:</label> 
                <input class="widefat" id="<?php echo $this->get_field_id('oauth_access_token_secret'); ?>" name="<?php echo $this->get_field_name('oauth_access_token_secret'); ?>" type="text" value="<?php echo esc_attr( $oauth_access_token_secret ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('debug'); ?>"><?php _e('Debug'); ?>:</label> 
                <input id="<?php echo $this->get_field_id('debug'); ?>" name="<?php echo $this->get_field_name('debug'); ?>" type="checkbox" <?php checked(isset($instance['debug']) ? $instance['debug'] : 0); ?> />
            </p>

    <?php 

        }

    } // class q_Latest_Images

}