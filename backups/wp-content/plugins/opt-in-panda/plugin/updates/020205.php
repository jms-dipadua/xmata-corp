<?php

/**
 * Updates options.
 * @since 5.2.0
 */
class OptinPandaUpdate020205 extends Factory325_Update {

   public function install() {

       $facebookAppId = get_option('opanda_facebook_appid');
       if ( !empty( $facebookAppId ) ) {
           update_option('opanda_facebook_app_id', $facebookAppId );
           delete_option('opanda_facebook_appid');
       }

       $twitterConsumerKey = get_option('opanda_twitter_consumer_key');
       if ( !empty( $twitterConsumerKey ) ) {
           update_option('opanda_twitter_social_app_consumer_key', $twitterConsumerKey );
           update_option('opanda_twitter_signin_app_consumer_key', $twitterConsumerKey );
           delete_option('opanda_twitter_consumer_key');
       }

       $twitterConsumerSecret = get_option('opanda_twitter_consumer_secret');
       if ( !empty( $twitterConsumerSecret ) ) {
           update_option('opanda_twitter_social_app_consumer_secret', $twitterConsumerKey );
           update_option('opanda_twitter_signin_app_consumer_secret', $twitterConsumerKey );
           delete_option('opanda_twitter_consumer_secret');
       }
    }
}