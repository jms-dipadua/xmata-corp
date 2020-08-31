<?php

namespace bizpanda\includes\gates\twitter;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\OAuthGateBridge;

/**
 * The class to proxy the request to the Twitter API.
 */
class TwitterBridge extends OAuthGateBridge {

    const API_ENDPOINT = 'https://api.twitter.com/';
    const API_VERSION = '1.1';
    const API_OAUTH_VERSION = '1.0';

    /**
     * Get a request_token from Twitter
     * @param array $stateParams
     * @return mixed[]
     * @throws GateBridgeException
     */
    function getRequestToken( $stateParams = [] ) {
        $callbackUrl = $this->callbackUrl;

        if ( !empty( $stateParams ) ) {

            if ( strpos( $callbackUrl, '?' ) === false ) {
                $callbackUrl .= '?' . http_build_query( $stateParams );
            } else {
                $callbackUrl .= '&' . http_build_query( $stateParams );
            }
        }

        $response = $this->requestQuery('oauth/request_token', 'POST', [
            'oauthParams' => [
                'oauth_callback' => $callbackUrl
            ],

            // the parameter 'x_auth_access_type' doesn't work with include_email option
            // 'data' => [
            //     'x_auth_access_type' => $this->permissions
            // ],

            'expectQuery' => true
        ]);

        if ( !isset( $response['oauth_token'] ) ) {
            throw $this->createException( GateBridgeException::UNEXPECTED_RESPONSE, 'The parameter [oauth_token] is not set.' );
        }

        return $response;
    }

    /**
     * Gets a redirect URL to authorize a user.
     * @param array $stateParams Extra data for the state param if applied.
     * @return string
     * @throws GateBridgeException
     */
    function getAuthorizeURL( $stateParams = [] ) {

        $token = $this->getRequestToken( $stateParams );
        $token = $token['oauth_token'];

        return self::API_ENDPOINT . 'oauth/authorize' . "?oauth_token={$token}";
    }

    /**
    * Exchange request token and secret for an access token and
    * secret, to sign API calls.
    *
    * @returns array("oauth_token" => "the-access-token",
    *                "oauth_token_secret" => "the-access-secret",
    *                "user_id" => "9436992",
    *                "screen_name" => "abraham")
    */
    function getAccessTokenByVerifier( $token, $verifier ) {

        $host = 'oauth/access_token?oauth_token=' . $token . '&oauth_verifier=' . $verifier;
        return $this->requestQuery( $host, 'POST');
    }

    /**
     * @inheritDoc
     */
    function getAccessToken($code) {
        throw GateBridgeException::create( GateBridgeException::NOT_SUPPORTED );
    }

    /**
     * @inheritDoc
     */
    function getUserInfo(){

        return $this->get('account/verify_credentials',  [
            'skip_status' => 1,
            'include_email' => 'true'
        ]);

        return $result;
    }

    // -----------------------------------------------------------------------------
    // Helper Methods
    // -----------------------------------------------------------------------------

    /**
     * Makes GET request to the API.
     * @param $host
     * @param $data
     * @return string
     * @throws GateBridgeException
     */
    public function get($host, $data) {

        return $this->requestJson( $host, 'GET', [
            'data' => $data
        ]);
    }

    /**
     * Makes POST request to the API.
     * @param $host
     * @param $data
     * @return string
     * @throws GateBridgeException
     */
    public function post($host, $data) {

        return $this->requestJson( $host, 'POST', [
            'data' => $data
        ]);
    }

    /**
     * Makes a request to API and expects the query string as a response.
     * @param $host
     * @param $method
     * @param array $options
     * @return mixed[]
     * @throws GateBridgeException
     */
    function requestQuery($host, $method, $options = [] ) {

        $options['expectQuery'] = true;
        $data = $this->request( $host, $method, $options  );

        return $this->throwExceptionOnErrors( $data );
    }

    /**
     * Makes a request to API and expects the json as a response.
     * @param $host
     * @param $method
     * @param array $options
     * @return string
     * @throws GateBridgeException
     */
    function requestJson( $host, $method, $options = [] ) {

        $options['expectJson'] = true;
        $json = $this->request( $host, $method, $options );

        return $this->throwExceptionOnErrors( $json );
    }

    /**
     * A common point for any request to Twitter API.
     * @param $host
     * @param $method
     * @param array $options
     * @return mixed[]
     * @throws GateBridgeException
     */
    function request($host, $method, $options = [] ) {

        $isOAuthRequest = ( strpos( $host, 'oauth' ) === 0 );

        if ( $isOAuthRequest ) {
            $url = self::API_ENDPOINT . $host;
        } else {
            $url = self::API_ENDPOINT . self::API_VERSION . '/' . $host . '.json';
        }

        $signUrl = $url;

        $authorizationParams = [
            'oauth_consumer_key' => $this->clientId,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $this->generateTimestamp(),
            'oauth_nonce' => $this->generateNonce(),
            'oauth_version' => self::API_OAUTH_VERSION
        ];

        // if this request is authorized, add the access token

        if ( $this->accessToken ) {
            $authorizationParams['oauth_token'] = $this->accessToken;
        }

        // if the options extra parameters to pass, add it too

        if ( isset( $options['oauthParams'] )) {
            $authorizationParams = array_merge($authorizationParams, $options['oauthParams'] );
        }

        // adds all GET/POST params to the signature

        $urlParams = [];
        $query = parse_url($url, PHP_URL_QUERY );
        parse_str( $query, $urlParams );

        // filters empty params, otherwise we will get an incorrect signature

        $postParams = !empty( $options['data'] ) ? $options['data'] : [];
        $postParamsFiltered = [];
        foreach( $postParams as $postParamName => $postParam ) {
            if ( $postParam === null || $postParam === false ) continue;
            $postParamsFiltered[$postParamName] = $postParam;
        }

        $options['data'] = $postParamsFiltered;

        $signatureParams = array_merge($authorizationParams, $urlParams, $postParamsFiltered);
        $authorizationParams['oauth_signature'] = $this->signRequest($method, $url, $signatureParams);

        $headers =[
            'Authorization' => $this->generateAuthorizationHeader( $authorizationParams )
        ];

        if ( !isset( $options['headers'] ) ) $options['headers'] = [];
        $options['headers'] = array_merge( $options['headers'], $headers);

        $json = $this->http( $url, $method, $options );

        /*
        if ( $host == 'account/verify_credentials' ) {

            return [
                'url' => $url,
                'signatureParams' => $signatureParams,
                'authorizationParams' => $authorizationParams,
                'urlParams' => $urlParams,
                'postParams' => $postParams,
                'options' => $options,
                'content' => $json,
                'request' => $this->getLastRequestInfo(),
                'response' => $this->getLastResponseInfo(),
                'sign-key' => $this->generateSigningKey(),
                'access-token' => $this->accessToken,
                'access-token-secret' => $this->accessTokenSecret,
            ];
        }
*/

        return $json;
    }

    /**
     * Throws an exception if the response contains errors.
     * @param $data
     * @return mixed[] Returns response data back.
     * @throws GateBridgeException
     */
    function throwExceptionOnErrors( $data ) {

        if ( isset( $data['errors'] ) && isset( $data['errors'][0]['message'] ) ) {

            // [CODE: 32] Could not authenticate you.
            if ( 32 === $data['errors'][0]['code'] ) {
                throw $this->createException( GateBridgeException::NOT_AUTHENTICATED, 'Please make sure that Twitter API Key and Secret Key are set correctly.' );
            }

            $clarification = sprintf( '[CODE: %d] %s.' , $data['errors'][0]['code'], $data['errors'][0]['message'] );
            throw $this->createException( GateBridgeException::ERROR_RESPONSE, $clarification );
        }

        return $data;
    }

    /**
     * Signs the request.
     * @param $method
     * @param $url
     * @param $params
     * @return string
     */
    function signRequest( $method, $url, $params ) {

        ksort( $params );

        $strParams = [];
        foreach( $params as $key => $value ) {
            $strParams[] = rawurlencode( $key ) . '=' . rawurlencode( $value );
        }

        $strParams = implode('&', $strParams);

        $message = $method . '&'. rawurlencode($url) . '&' . rawurlencode($strParams);
        $key = $this->generateSigningKey();

        $encrypted = hash_hmac('sha1', $message, $key, true);
        return base64_encode($encrypted);
    }

    /**
     * Generates the signing key.
     * @return string
     */
    function generateSigningKey() {

        $key = rawurlencode( $this->clientSecret ) . '&';
        if ( $this->accessTokenSecret ) $key = $key . rawurlencode( $this->accessTokenSecret );

        return $key;
    }

    /**
     * Generates the authorization header.
     * @param array $params
     * @return string
     */
    function generateAuthorizationHeader( $params = [] ) {

        $header = 'OAuth ';

        ksort( $params );

        $strParams = [];
        foreach( $params as $key => $value ) {
            $strParams[] = rawurlencode( $key ) . '="' . rawurlencode( $value ) . '"';
        }

        $strParams = implode(', ', $strParams);
        return $header . $strParams;
    }
}
