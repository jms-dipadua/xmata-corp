<?php
namespace bizpanda\includes\gates\linkedin;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\OAuthGateBridge;

/**
 * The class to proxy the request to the LinkedIn API.
 */
class LinkedinBridge extends OAuthGateBridge {

    /**
     * Returns an URL to authorize and grant permissions.
     * @param array $stateArgs An extra data to add into the state argument. They will be passed back on callback.
     * @return string An URL to authorize.
     */
    public function getAuthorizeURL($stateArgs = [] ) {
        $endpointUrl = ' https://www.linkedin.com/oauth/v2/authorization';

        $args = [
            'scope' => $this->permissions,
            'response_type' => 'code',
            'redirect_uri' => $this->callbackUrl,
            'client_id' => $this->clientId,
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
        $endpointUrl = 'https://www.linkedin.com/oauth/v2/accessToken';

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

        return [
            'profile' => $this->getUserProfile(),
            'email' => $this->getUserEmail()
        ];
    }

    /**
     * Gets user profile.
     */
    function getUserProfile() {
        $endpointUrl = 'https://api.linkedin.com/v2/';
        $url = $endpointUrl . 'me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))';

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken
        ];

        $result = $this->requestJson($url, 'GET', [
            'headers' => $headers
        ]);

        $data = [
            'id' => isset( $result['id'] ) ? $result['id'] : false,
            'firstName' => false,
            'lastName' => false,
            'image' => false
        ];

        $local = false;
        if ( isset( $result['firstName']['preferredLocale'] ) ) {
            $local = $result['firstName']['preferredLocale']['language'] . '_' . $result['firstName']['preferredLocale']['country'];
        }

        if ( !empty( $local ) && isset( $result['firstName']['localized'][$local] ) ) {
            $data['firstName'] = $result['firstName']['localized'][$local];
        }

        if ( !empty( $local ) && isset( $result['firstName']['localized'][$local] ) ) {
            $data['lastName'] = $result['lastName']['localized'][$local];
        }

        if ( isset( $result['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] ) ) {
            $data['image'] = $result['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'];
        }

        return $data;
    }

    /**
     * Gets user info.
     */
    function getUserEmail() {
        $endpointUrl = 'https://api.linkedin.com/v2/';
        $url = $endpointUrl . 'emailAddress?q=members&projection=(elements*(handle~))';

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken
        ];

        $result = $this->requestJson($url, 'GET', [
            'headers' => $headers
        ]);

        if ( isset( $result['elements'][0]['handle~']['emailAddress'] ) ) {
            return $result['elements'][0]['handle~']['emailAddress'];
        }

        return null;
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
                throw $this->createException( GateBridgeException::NOT_AUTHENTICATED, 'Please make sure that LinkedIn Client ID and Client Secret are set correctly.' );
            }

            throw $this->createException( GateBridgeException::ERROR_RESPONSE, $json['error_description']);
        } else if ( isset( $json['error'] ) && isset( $json['error']['message'] ) ) {
            throw $this->createException( GateBridgeException::ERROR_RESPONSE,  $json['error']['message']);
        }

        return $json;
    }
}


