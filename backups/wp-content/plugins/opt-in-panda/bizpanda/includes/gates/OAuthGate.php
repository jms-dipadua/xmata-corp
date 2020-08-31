<?php

namespace bizpanda\includes\gates;

use bizpanda\includes\gates\exceptions\GateException;

/**
 * OAuth Gate.
 * Provides common methods for OAuth flow.
 */
abstract class OAuthGate extends Gate {

    /**
     * Current bridge ID (used to keep connection with a widget that initiates the auth flow).
     * @var string
     */
    protected $bridgeId = null;

    /**
     * Scopes of permissions to requests on authorization.
     * @var string[]
     */
    protected $permissionScopes = [];

    /**
     * A list of allowed scopes of permissions on authorization.
     * @var string[]
     */
    protected $allowedPermissionScopes = [];

    /**
     * Allowed types of requests.
     * @var string[]
     */
    protected $allowedRequestTypes = [];

    /**
     * OAuthGate constructor.
     * @param $session
     * @param $config
     * @param $request
     */
    public function __construct( $session = null, $config = null, $request = null ) {
        parent::__construct( $session, $config, $request );

        $this->bridgeId = $this->getRequestParam('bridgeId');

        $this->allowedRequestTypes = array_merge(
            $this->allowedRequestTypes,
            ['init', 'auth', 'callback', 'get_user_info']
        );
    }

    /**
     * Handles the proxy request.
     * @throws GateException
     */
    public function handleRequest() {

        // the request type is to determine which action we should to run
        $requestType = $this->getRequestParam('requestType');

        if ( empty( $this->name ) ) {
            throw GateException::create(GateException::APP_NOT_CONFIGURED, 'The gate name is missed.' );
        }

        if ( empty( $this->allowedPermissionScopes ) ) {
            throw GateException::create(GateException::APP_NOT_CONFIGURED, 'Allowed permissions are not set.' );
        }

        if ( empty( $this->allowedRequestTypes ) ) {
            throw GateException::create(GateException::APP_NOT_CONFIGURED, 'Allowed request types are not set.' );
        }

        if ( !in_array( $requestType, $this->allowedRequestTypes ) ) {
            throw GateException::create(GateException::INVALID_INBOUND_REQUEST, 'The request type [' . $requestType . '] is not allowed.' );
        }

        $this->permissionScopes = $this->getFlowScopes();

        $method = 'do' . $this->toCamelCase( $requestType, true );
        $result = $this->{$method}();

        // required for the email verification in future
        // used in Wordpress to verify if the email is actually received from the app

        if ( $requestType == 'get_user_info' && isset( $result['identity']['email'] )) {
            $this->setVisitorValue($this->name . '_email', $result['identity']['email']  );
        }

        return $result;
    }

    /**
     * Returns scopes of permissions using within the current auth flow.
     * @throws GateException
     */
    protected function getFlowScopes() {

        $scopes = $this->getRequestParam('scope');

        if ( empty( $scopes ) ) $scopes = $this->getVisitorValue($this->name . '_permission_scopes', null );
        if ( empty( $scopes ) ) throw GateException::create(GateException::SCOPE_MISSED );

        $parts = explode(',', $scopes);
        $result = [];

        foreach( $parts as $part ) {
            if ( !in_array( $part, $this->allowedPermissionScopes) ) continue;
            $result[] = $part;
        }

        return $result;
    }

    /**
     * Saves scopes of permissions for the current flow.
     * @param $scope
     */
    protected function setFlowScopes( $scope ) {

        $scope = implode(',', $scope);
        $this->setVisitorValue($this->name . '_permission_scopes', $scope );
    }

    /**
     * Adds extra mandatory variables into returned data.
     * @param array $data
     * @return array
     */
    public function prepareResult( $data = [] ) {

        // returns when sessions expires (1440 is the default value)
        $sessionLifetime = (int)ini_get('session.gc_maxlifetime');
        if ( empty( $sessionLifetime ) ) $sessionLifetime = 1440;

        $expires = time() + $sessionLifetime;

        $mandatory = [
            'visitorId' => $this->visitorId,
            'bridgeId' => $this->bridgeId,
            'expires' => $expires
        ];

        return array_merge( $mandatory, $data );
    }

    /**
     * Returns an object that is responsible for calls to API.
     * @param bool $authorized If true, creates a bridge for authorized requests.
     * @return OAuthGateBridge
     */
    public abstract function createBridge( $authorized = false );

    /**
     * Returns options needed to create a bridge.
     * @param bool $authorized If true, creates a bridge for authorized requests.
     * @return mixed[]
     */
    public abstract function getOptionsForBridge( $authorized = false );

    /**
     * Inits an OAuth request.
     */
    public function doInit() {

        $bridge = $this->createBridge();

        $authUrl = $bridge->getAuthorizeURL([
            'bridgeId' => $this->bridgeId,
            'visitorId' => $this->visitorId
        ]);

        $this->setFlowScopes( $this->permissionScopes );

        return $this->prepareResult([
            'authUrl' => $authUrl
        ]);
    }

    /**
     * The same as doInit.
     */
    public function doAuth() {
        return $this->doInit();
    }

    /**
     * Handles the callback request.
     * @throws GateException
     */
    public function doCallback() {

        $bridge = $this->createBridge();

        $code = $this->getRequestParam('code');

        if ( empty( $code ) ) {
            throw GateException::create(GateException::INVALID_INBOUND_REQUEST, 'The code parameter is not set.' );
        }

        $tokenData = $bridge->getAccessToken($code);

        $this->setVisitorValue($this->name . '_access_token', $tokenData['accessToken'] );

        return $this->prepareResult([
            'auth' => $tokenData,
        ]);
    }

    /**
     * Returns user profile info.
     * @return mixed
     */
    public abstract function doGetUserInfo();
}