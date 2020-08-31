<?php
namespace bizpanda\includes\gates\google;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\OAuthGateBridge;

/**
 * The class to proxy the request to the Google API.
 */
class GoogleBridge extends OAuthGateBridge {

    /**
     * Returns an URL to authorize and grant permissions.
     * @param array $stateArgs An extra data to add into the state argument. They will be passed back on callback.
     * @return string An URL to authorize.
     */
    public function getAuthorizeURL( $stateArgs = [] ) {
        $endpointUrl = 'https://accounts.google.com/o/oauth2/v2/auth';

        $args = [
            'scope' => $this->permissions,
            'access_type' => 'online',
            'include_granted_scopes' => 'true',
            'response_type' => 'code',
            'redirect_uri' => $this->callbackUrl,
            'client_id' => $this->clientId,
            'display' => 'popup',
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
        $endpointUrl = 'https://oauth2.googleapis.com/token';

        $requestData = [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->callbackUrl,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ];

        $json = $this->requestJson($endpointUrl, 'POST', [
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
        $endpointUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $url = $endpointUrl . '?access_token=' . $this->accessToken;

        return $this->requestJson($url, 'GET');
    }

    /**
     * Subscribes to YouTube channel.
     * @param $channelId string A channel to subscribe to.
     * @return string
     * @throws GateBridgeException
     */
    function subscribeToYoutube( $channelId ) {

        $endpointUrl = 'https://www.googleapis.com/youtube/v3/subscriptions';
        $url = $endpointUrl . '?access_token=' . $this->accessToken . '&part=snippet';

        $data = json_encode([
            'snippet' => [
                'resourceId' => [
                    'kind' => 'youtube#channel',
                    'channelId' => $channelId
                ]
            ]
        ]);

        $headers = [
            'Content-Type' => 'application/json'
        ];

        return $this->requestJson($url, 'POST', [
            'data' => $data,
            'headers' => $headers
        ]);
    }

    /**
     * Makes a request to Google API.
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

            if ( 'invalid_client' === $json['error'] ) {
                throw $this->createException( GateBridgeException::NOT_AUTHENTICATED, 'Please make sure that Google Client ID and Client Secret are set correctly.' );
            }

            throw $this->createException( GateBridgeException::ERROR_RESPONSE, $json['error_description']);
        } else if ( isset( $json['error'] ) && isset( $json['error']['message'] ) ) {
            throw $this->createException( GateBridgeException::ERROR_RESPONSE,  $json['error']['message']);
        }

        return $json;
    }
}


