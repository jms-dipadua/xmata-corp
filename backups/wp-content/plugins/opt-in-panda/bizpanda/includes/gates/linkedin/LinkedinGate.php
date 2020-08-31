<?php
namespace bizpanda\includes\gates\linkedin;

use bizpanda\includes\gates\Gate;
use bizpanda\includes\gates\exceptions\GateException;
use bizpanda\includes\gates\OAuthGate;
use Yii;

/**
 * The class to proxy the request to the LinkedIn API.
 */
class LinkedinGate extends OAuthGate {

    /**
     * LinkedinGate constructor.
     * @param null $session
     * @param null $config
     * @param null $request
     */
    public function __construct($session = null, $config = null, $request = null)
    {
        parent::__construct($session, $config, $request);

        $this->name = 'linkedin';
        $this->allowedPermissionScopes = ['userinfo'];
    }

    /**
     * @inheritDoc
     */
    public function createBridge( $authorized = false ) {

        $config = $this->getOptionsForBridge( $authorized );
        return new LinkedinBridge( $config );
    }

    /**
     * @inheritDoc
     */
    public function getOptionsForBridge( $authorized = false ) {

        $options = $this->config->get( $this->name );

        if ( !isset( $options['clientId'] ) || !isset( $options['clientSecret'] ) ) {
            throw GateException::create(GateException::APP_NOT_CONFIGURED, 'Client Id or Client Secret are not set.' );
        }

        $options['permissions'] = $this->getLinkedInPermissions( $this->permissionScopes );

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
    public function getLinkedInPermissions( $scopes = [] ) {

        $permissions = [];
        foreach( $scopes as $scope ) {

            if ( 'userinfo' === $scope ) {

                $permissions[] = LinkedinScope::READ_LITE_PROFILE;
                $permissions[] = LinkedinScope::READ_EMAIL_ADDRESS;
            }
        }

        return implode(' ', $permissions);
    }

    /**
     * Returns user profile info.
     * @return mixed
     */
    public function doGetUserInfo() {
        $bridge = $this->createBridge( true );

        $result = $bridge->getUserInfo();

        $identity = [
            'linkedinId' =>  isset( $result['id'] ) ? $result['id'] : false,
            'source' => 'linkedin',
            'email' => isset( $result['email'] ) ? $result['email'] : false,
            'displayName' => false,
            'name' => isset( $result['profile']['firstName'] ) ? $result['profile']['firstName'] : false,
            'family' => isset( $result['profile']['lastName'] ) ? $result['profile']['lastName'] : false,
            'social' => true,
            'image' => isset( $result['profile']['image'] ) ? $result['profile']['image'] : false
        ];

        $identity['displayName'] = $this->buildDisplayName( $identity );

        return $this->prepareResult([
            'identity' => $identity,
        ]);
    }
}


