<?php
namespace bizpanda\includes\gates\twitter;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\Gate;
use bizpanda\includes\gates\exceptions\GateException;
use bizpanda\includes\gates\google\FacebookBridge;
use bizpanda\includes\gates\OAuthGate;
use Yii;

/**
 * The class to proxy the request to the Twitter API.
 */
class TwitterGate extends OAuthGate {

    public function __construct($session = null, $config = null, $request = null)
    {
        parent::__construct($session, $config, $request);

        $this->name = 'twitter';

        $this->allowedRequestTypes = array_merge(
            $this->allowedRequestTypes,
            ['follow', 'tweet', 'get_tweets', 'get_followers']
        );

        $this->allowedPermissionScopes = ['read', 'write'];
    }

    /**
     * @inheritDoc
     */
    public function createBridge( $authorized = false ) {

        $config = $this->getOptionsForBridge( $authorized );
        return new TwitterBridge( $config );
    }

    /**
     * @inheritDoc
     */
    public function getOptionsForBridge( $authorized = false ) {

        $scopes = $this->getFlowScopes();
        $scope = $scopes[0]; // read or write

        $twitterOptions = $this->config->get( $this->name );

        $options = $twitterOptions[$scope];
        $options['callbackUrl'] = $twitterOptions['callbackUrl'];

        if ( !isset( $options['clientId'] ) || !isset( $options['clientSecret'] ) ) {
            throw GateException::create(GateException::APP_NOT_CONFIGURED, 'API Key or API Secret are not set.' );
        }

        $options['permissions'] = $scope;

        if ( $authorized ) {
            $options['accessToken'] = $this->getVisitorValue($this->name . '_oauth_token', null );
            $options['accessTokenSecret'] = $this->getVisitorValue($this->name . '_oauth_secret', null );

            if ( empty( $options['accessToken'] ) ) {
                throw GateException::create(GateException::AUTH_FLOW_BROKEN, 'The access token is not set.' );
            }

            if ( empty( $options['accessTokenSecret'] ) ) {
                throw GateException::create(GateException::AUTH_FLOW_BROKEN, 'The access token secret is not set.' );
            }
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public function doCallback() {

        $token = $this->getRequestParam('oauthToken');
        $verifier = $this->getRequestParam('oauthVerifier');

        if ( empty( $token ) || empty( $verifier ) ) {
            throw GateException::create(GateException::INVALID_INBOUND_REQUEST, 'Parameters [oauthToken] or [oauthVerifier] are not passed.' );
        }

        $bridge = $this->createBridge( false );
        $accessToken = $bridge->getAccessTokenByVerifier( $token, $verifier );

        $this->setVisitorValue($this->name . '_oauth_token', $accessToken['oauth_token'] );
        $this->setVisitorValue($this->name . '_oauth_secret', $accessToken['oauth_token_secret'] );

        return $this->prepareResult([
            'userId' => $accessToken['user_id'],
            'oauthToken' => $accessToken['oauth_token'],
            'oauthTokenSecret' => $accessToken['oauth_token_secret']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function doGetUserInfo() {
        $bridge = $this->createBridge(true);

        $response = $bridge->getUserInfo();

        $identity = [
            'source' => 'twitter',
            'email' => isset( $response['email'] ) ? $response['email'] : false,
            'name' => isset( $response['name'] ) ? $response['name'] : false,
            'displayName' => isset( $response['screen_name'] ) ? $response['screen_name'] : false,
            'twitterId' => isset( $response['screen_name'] ) ? $response['screen_name'] : false,
            'twitterUrl' => isset( $response['screen_name'] ) ? ( 'https://twitter.com/' . $response['screen_name'] ) : ''
        ];

        if ( $identity['name'] ) {

            $nameParts = explode(' ',  $identity['name']);
            if ( count( $nameParts ) == 2 ) {
                $identity['name'] = $nameParts[0];
                $identity['family'] = $nameParts[1];
            }
        }

        if ( isset( $response['profile_image_url'] ) ) {
            $identity['image'] = str_replace('_normal', '', $response['profile_image_url']);
        } else {
            $identity['image'] = false;
        }

        return $this->prepareResult([
            'identity' => $identity
        ]);
    }

    /**
     * Gets the 3 last tweets.
     * @return array
     * @throws GateBridgeException
     */
    public function doGetTweets() {
        $bridge = $this->createBridge(true);

        $response = $bridge->get('statuses/user_timeline', [
            'count' => 3
        ]);

        return $this->prepareResult([
            'response' => $response
        ]);
    }

    /**
     * Finds a followers with the specified screen name.
     * @return array
     * @throws GateException
     */
    public function doGetFollowers() {
        $bridge = $this->createBridge(true);

        $screenName = $this->getRequestParam('screenName');
        if ( empty( $screenName) ) throw new GateException( "The screen name is not set." );

        $response = $bridge->get('friendships/lookup', [
            'screen_name' => $screenName
        ]);

        return $this->prepareResult([
            'response' => $response
        ]);
    }

    /**
     * Tweets the specified message.
     * @return array
     * @throws GateException
     */
    protected function doTweet() {
        $bridge = $this->createBridge(true);

        $message = $this->getRequestParam('tweetMessage');
        if ( empty( $message) ) throw new GateException( "The tweet text is not specified." );

        $response = null;

        try {

            $response = $bridge->post('statuses/update', [
                'status' => $message
            ]);

            return $this->prepareResult([
                'response' => $response
            ]);

        } catch ( GateBridgeException $exception ) {

            $details = $exception->getExceptionDetails();

            if ( strpos( $details['clarification'], '187' ) > 0 ) {

                // error 187: status is a duplicate.
                // already tweeted

                return $this->prepareResult([
                    'success' => true
                ]);

            } else {
                throw $exception;
            }
        }
    }

    /**
     * Follows the specified profile.
     * @return array
     * @throws GateException
     */
    protected function doFollow() {
        $bridge = $this->createBridge(true);

        $followTo = $this->getRequestParam('followTo');
        if ( empty( $followTo) ) throw new GateException( "The user name to follow is not specified" );

        $notifications = $this->getRequestParam('notifications', true);
        $notifications = $this->normalizeValue( $notifications );

        $response = $bridge->get('friendships/lookup', [
            'screen_name' => $followTo
        ]);

        if ( isset( $response[0]->connections ) && in_array( 'following', $response[0]->connections ) ) {

            return $this->prepareResult([
                'success' => true
            ]);
        }

        $requestData = ['screen_name' => $followTo];
        if ( !empty( $notifications ) ) $requestData['follow'] = $notifications;

        $response = $bridge->post('friendships/create', $requestData);

        return $this->prepareResult([
            'response' => $response
        ]);
    }
}


