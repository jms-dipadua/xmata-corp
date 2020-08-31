<?php
namespace bizpanda\includes\gates\facebook;

use bizpanda\includes\gates\Gate;
use bizpanda\includes\gates\exceptions\GateException;
use bizpanda\includes\gates\OAuthGate;
use bizpanda\includes\gates\OAuthGateBridge;
use Yii;

/**
 * The class to proxy the request to the Facebook API.
 */
class FacebookGate extends OAuthGate {

    /**
     * FacebookGate constructor.
     * @param null $session
     * @param null $config
     * @param null $request
     */
    public function __construct($session = null, $config = null, $request = null)
    {
        parent::__construct($session, $config, $request);

        $this->name = 'facebook';
        $this->allowedPermissionScopes = ['userinfo'];
    }

    /**
     * @inheritDoc
     */
    public function createBridge( $authorized = false ) {

        $config = $this->getOptionsForBridge( $authorized );
        return new FacebookBridge( $config );
    }

    /**
     * @inheritDoc
     */
    public function getOptionsForBridge( $authorized = false ) {

        $options = $this->config->get( $this->name );

        if ( !isset( $options['clientId'] ) || !isset( $options['clientSecret'] ) ) {
            throw GateException::create(GateException::APP_NOT_CONFIGURED, 'App Id or App Secret are not set.' );
        }

        $options['permissions'] = $this->getFacebookPermissions( $this->permissionScopes );

        if ( $authorized ) {
            $options['accessToken'] = $this->getVisitorValue($this->name . '_access_token', null );

            if ( empty( $options['accessToken'] ) ) {
                throw GateException::create(GateException::AUTH_FLOW_BROKEN, 'The access token is not set.' );
            }
        }

        return $options;
    }

    /**
     * Converts app scopes to Facebook permissions.
     * @param array $permissionScopes A set of scopes to convert to Facebook permissions.
     * @return string
     */
    public function getFacebookPermissions( $permissionScopes ) {

        $permissions = [];
        foreach( $permissionScopes as $scope ) {

            if ( 'userinfo' === $scope ) {

                $permissions[] = 'public_profile';
                $permissions[] = 'email';
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
            'source' => 'facebook',
            'email' => isset( $result['email'] ) ? $result['email'] : false,
            'displayName' => false,
            'name' => isset( $result['first_name'] ) ? $result['first_name'] : false,
            'family' => isset( $result['last_name'] ) ? $result['last_name'] : false,
            'gender' => isset( $result['gender'] ) ? $result['gender'] : false,
            'social' => true,
            'facebookId' =>  isset( $result['id'] ) ? $result['id'] : false,
            'facebookUrl' => isset( $result['link'] ) ? $result['link'] : false,
            'image' => false
        ];

        $identity['displayName'] = $this->buildDisplayName( $identity );

        if ( !empty( $identity['facebookId'] ) ) {
            $identity['image'] = 'https://graph.facebook.com/' . $identity['facebookId'] .  '/picture?type=large';
        }

        return $this->prepareResult([
            'identity' => $identity,
        ]);
    }
}


