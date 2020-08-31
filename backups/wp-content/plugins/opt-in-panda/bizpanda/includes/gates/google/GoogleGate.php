<?php
namespace bizpanda\includes\gates\google;

use bizpanda\includes\gates\Gate;
use bizpanda\includes\gates\exceptions\GateException;
use bizpanda\includes\gates\OAuthGate;
use Yii;

/**
 * The class to proxy the request to the Google API.
 */
class GoogleGate extends OAuthGate {

    /**
     * GoogleGate constructor.
     * @param null $session
     * @param null $config
     * @param null $request
     */
    public function __construct($session = null, $config = null, $request = null)
    {
        parent::__construct($session, $config, $request);

        $this->name = 'google';

        $this->allowedRequestTypes = array_merge(
            $this->allowedRequestTypes,
            ['subscribe_to_youtube']
        );

        $this->allowedPermissionScopes = ['userinfo', 'youtube'];
    }

    /**
     * @inheritDoc
     */
    public function createBridge( $authorized = false ) {

        $config = $this->getOptionsForBridge( $authorized );
        return new GoogleBridge( $config );
    }

    /**
     * @inheritDoc
     */
    public function getOptionsForBridge( $authorized = false ) {

        $options = $this->config->get( $this->name );

        if ( !isset( $options['clientId'] ) || !isset( $options['clientSecret'] ) ) {
            throw GateException::create(GateException::APP_NOT_CONFIGURED, 'Client Id or Client Secret are not set.' );
        }

        $options['permissions'] = $this->getGooglePermissions( $this->permissionScopes );

        if ( $authorized ) {
            $options['accessToken'] = $this->getVisitorValue($this->name . '_access_token', null );

            if ( empty( $options['accessToken'] ) ) {
                throw GateException::create(GateException::AUTH_FLOW_BROKEN, 'The access token is not set.' );
            }
        }

        return $options;
    }

    /**
     * Converts app scopes to the google scopes.
     * @param array $scopes
     * @return string
     */
    public function getGooglePermissions( $scopes = [] ) {

        $googleScopes = [];
        foreach( $scopes as $scope ) {

            if ( 'userinfo' === $scope ) {

                $googleScopes[] = 'https://www.googleapis.com/auth/userinfo.profile';
                $googleScopes[] = 'https://www.googleapis.com/auth/userinfo.email';

            } else if ( 'youtube' === $scope ) {
                $googleScopes[] = 'https://www.googleapis.com/auth/youtube';
            }
        }

        return implode(' ', $googleScopes);
    }

    /**
     * Returns user profile info.
     * @return mixed
     */
    public function doGetUserInfo() {
        $bridge = $this->createBridge( true );

        $result = $bridge->getUserInfo();

        $identity = [
            'source' => 'google',
            'email' => isset( $result['email'] ) ? $result['email'] : false,
            'displayName' => isset( $result['name'] ) ? $result['name'] : false,
            'name' => isset( $result['given_name'] ) ? $result['given_name'] : false,
            'family' => isset( $result['family_name'] ) ? $result['family_name'] : false,
            'social' => true,
            'googleId' =>  isset( $result['id'] ) ? $result['id'] : false,
            'googleUrl' => false,
            'image' => isset( $result['picture'] ) ? $result['picture'] : false
        ];

        return $this->prepareResult([
            'identity' => $identity,
        ]);
    }

    /**
     * Performs subscription to YouTube channel.
     * @throws GateException
     */
    public function doSubscribeToYouTube() {

        $channelId = $this->getRequestParam('channelId');
        if ( empty( $channelId ) ) throw new GateException('Invalid request [channelId].');

        $bridge = $this->createBridge( true );

        $result = $bridge->subscribeToYoutube( $channelId );

        return $this->prepareResult([
            'response' => $result,
        ]);
    }
}


