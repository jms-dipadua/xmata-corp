<?php
namespace bizpanda\includes\gates\facebook;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\GateBridge;
use bizpanda\includes\gates\OAuthGateBridge;

/**
 * Proxy for requests to the Facebook API.
 */
class FacebookBridge extends OAuthGateBridge {

    /**
     * Returns an URL to authorize and grant permissions.
     * @param array $stateArgs An extra data to add into the state argument. They will be passed back on callback.
     * @return string An URL to authorize.
     */
    public function getAuthorizeURL( $stateArgs = [] ) {
        $endpointUrl = 'https://www.facebook.com/v7.0/dialog/oauth';

        $args = [
            'scope' => $this->permissions,
            'response_type' => 'code',
            'redirect_uri' => $this->callbackUrl,
            'client_id' => $this->clientId,
            'display' => 'popup',
            'auth_type' => 'rerequest',
            'state' => $this->buildStateParam( $stateArgs )
        ];

        return $endpointUrl . '?' . http_build_query( $args );
    }

    /**
     * Gets the access token using the code received on the authorization step.
     * @param $code Code received on the authorization step.
     * @return mixed[]
     * @throws GateBridgeException
     */
    function getAccessToken( $code ) {
        $endpointUrl = 'https://graph.facebook.com/v7.0/oauth/access_token';

        $requestData = [
            'code' => $code,
            'redirect_uri' => $this->callbackUrl,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ];

        $json = $this->requestJson($endpointUrl, 'GET', [
            'data' => $requestData
        ]);

        if ( !isset( $json['access_token'] ) ) {
            throw $this->createException( GateBridgeException::UNEXPECTED_RESPONSE, 'The parameter [access_token] is not set.' );
        }

        return [
            'accessToken' => $json['access_token']
        ];
    }

    /**
     * Gets user info.
     */
    function getUserInfo() {
        $endpointUrl = 'https://graph.facebook.com/me';
        $url = $endpointUrl . '?fields=email,first_name,last_name,gender,link&access_token=' . $this->accessToken;

        return $this->requestJson($url, 'GET');
    }

    /**
     * Makes a request to Facebook API.
     * @param $url
     * @param $method
     * @param array $options
     * @return string
     * @throws GateBridgeException
     */
    function requestJson($url, $method, $options = [] ) {

        $options['expectJson'] = true;
        $json = $this->http( $url, $method, $options );

        return $this->throwExceptionOnErrors( $json );
    }

    /**
     * Throws an exception if the response contains errors.
     * @param $json
     * @return mixed[] Returns response data back.
     * @throws GateBridgeException
     */
    function throwExceptionOnErrors( $json ) {

        if ( isset( $json['error'] ) && isset( $json['error_description'] ) ) {
            throw $this->createException( GateBridgeException::ERROR_RESPONSE, $json['error_description']);
        } else if ( isset( $json['error'] ) && isset( $json['error']['message'] ) ) {
            throw $this->createException( GateBridgeException::ERROR_RESPONSE, $json['error']['message']);
        }

        return $json;
    }
}


