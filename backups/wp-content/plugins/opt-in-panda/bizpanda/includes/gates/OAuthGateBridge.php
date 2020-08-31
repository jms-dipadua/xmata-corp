<?php

namespace bizpanda\includes\gates;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\Gate;
use bizpanda\includes\gates\GateException;

/**
 * The bridge implementing methods for OAuth flow.
 */
abstract class OAuthGateBridge extends GateBridge
{

    /**
     * Client ID.
     * @var string
     */
    protected $clientId = null;

    /**
     * Client Secret.
     * @var string
     */
    protected $clientSecret = null;

    /**
     * Callback URL.
     * @var string
     */
    protected $callbackUrl = null;

    /**
     * Permissions to authorize.
     * @var string[]
     */
    protected $permissions = null;

    /**
     * Access token.
     * @var string
     */
    protected $accessToken = null;

    /**
     * Access token secret.
     * @var null
     */
    protected $accessTokenSecret = null;

    /**
     * OAuthGateBridge constructor.
     * @param array $options
     */
    public function __construct( $options = [] )
    {
        $this->clientId = $options['clientId'];
        $this->clientSecret = $options['clientSecret'];
        $this->callbackUrl = $options['callbackUrl'];
        $this->permissions = $options['permissions'];

        if ( isset( $options['accessToken']) ) $this->accessToken = $options['accessToken'];
        if ( isset( $options['accessTokenSecret']) ) $this->accessTokenSecret = $options['accessTokenSecret'];
    }

    /**
     * Gets a redirect URL to authorize a user.
     * @param array $stateParams Extra data for the state param if applied.
     * @return string
     */
    abstract function getAuthorizeURL( $stateParams = [] );

    /**
     * Get an access token using the code received on callback.
     * @param $code
     * @return mixed
     */
    abstract function getAccessToken( $code );

    /**
     * Gets a user info after authorization.
     * @return mixed[] A response with the user data from API.
     */
    abstract function getUserInfo();

    // ---------------------------------------------------------------------------
    // Helper methods
    // ---------------------------------------------------------------------------

    /**
     * Builds the state argument for the authorization URL.
     * @param $stateArgs mixed[] Arguments that have to be included into the state argument.
     * @return string|null
     */
    protected function buildStateParam( $stateArgs ) {
        if ( empty( $stateArgs ) ) return null;

        $state = [];

        foreach($stateArgs as $extraName => $extraValue ) {
            $state[] = $extraName . '=' . $extraValue;
        }

        return implode('&', $state);
    }

    /**
     * Returns UNIX-timestamp.
     * @return int
     */
    protected function generateTimestamp () {
        return time();
    }

    /**
     * Returns a nonce.
     * @return int
     */
    protected function generateNonce() {
        $mt = microtime();
        $rand = mt_rand();

        return md5($mt . $rand);
    }
}


