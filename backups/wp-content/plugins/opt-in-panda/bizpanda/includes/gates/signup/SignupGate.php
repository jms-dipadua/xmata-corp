<?php

namespace bizpanda\includes\gates\signup;

use bizpanda\includes\gates\ActionGate;
use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\exceptions\GateException;

/**
 * Sign-in Up Handler
 */
class SignupGate extends ActionGate {

    /**
     * Handles the proxy request.
     */
    public function handleRequest() {

        // - context data
        
        $contextData = $this->getRequestParam('contextData');
        $contextData = $this->normalizeValues( $contextData );
        
        // - identity data

        $identityData = $this->getRequestParam('identityData');
        $identityData = $this->normalizeValues( $identityData );

        // - service data

        $serviceData = $this->getRequestParam('serviceData');
        $serviceData = $this->normalizeValues( $serviceData );

        // prepares data received from custom fields to be transferred to the mailing service
        
        $identityData = $this->prepareDataToSave( null, null, $identityData );
        
        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/leads.php';
        \OPanda_Leads::add( $identityData, $contextData );

        $email = $identityData['email'];
        if ( empty( $email ) ) {
            throw GateException::create(GateException::INVALID_INBOUND_REQUEST, 'The email is not specified.');
        }

        // validate email in order to sign in user

        $visitorId = isset( $serviceData['visitorId'] ) ? trim ( $serviceData['visitorId'] ) : false;
        $proxy = isset( $serviceData['proxy'] ) ? trim ( $serviceData['proxy'] ) : false;
        $source = isset( $identityData['source'] ) ? trim ( $identityData['source'] ) : false;

        $canSignin = false;
        $isNewUser = !email_exists( $email );

        // if it's a new user

        if ( $isNewUser ) {

            $username = $this->generateUsername( $email );
            $random_password = wp_generate_password( $length = 12, false );

            $userId = wp_create_user( $username, $random_password, $email );
            
            if ( $userId ) {
                if ( isset( $identityData['name'] ) ) update_user_meta( $userId, 'first_name', $identityData['name'] );
                if ( isset( $identityData['family'] ) ) update_user_meta( $userId, 'last_name', $identityData['family'] );

                // saves an user ID received from the social network
                // facebookId, twitterId, googleId, linkedinIt etc.

                if ( !empty( $source ) ) {

                    $fieldName = $source . 'Id';
                    if ( isset( $identityData[$fieldName] ) ) {
                        update_user_meta( $userId, "opanda_" . $source . "_id", $identityData[$fieldName] );
                    }
                }
            }

            wp_new_user_notification( $userId, null );
            
            do_action('opanda_registered', $identityData, $contextData );
            $canSignin = true;

        // if a user with the given email exists, we need to verify it before sign-in

        } else {

            $user = get_user_by( 'email', $email );
            $userId = $user->ID;

            if ( !empty( $source ) ) {

                // step 1: verify that the email is received from the proxy

                if ( !$this->verifyEmail( $proxy, $source, $visitorId, $email ) ) {
                    throw GateException::create(GateException::INVALID_INBOUND_REQUEST, __('Unable to verify the email.', 'bizpanda'));
                }

                // step 2: checks if the user was registered via the sign-in locker early

                $fieldName = $source . 'Id';
                if ( isset( $identityData[$fieldName] ) ) {

                    $savedId = get_user_meta($userId, $source . "_id", true);
                    if ( !empty( $savedId ) && $savedId == $identityData[$fieldName] ) {
                        $canSignin = true;
                    }
                }
            }
        }
    
        // safe, the email is verified above

        if ( $canSignin && !is_user_logged_in() ) {
            wp_set_auth_cookie( $userId, true );
        }
    }

    /**
     * Generates a username.
     * @param $email
     * @return bool|mixed|string
     */
    protected function generateUsername( $email ) {
        
        $parts = explode ('@', $email);
        if ( count( $parts ) < 2 ) return false;
        
        $username = $parts[0];
        if ( !username_exists( $username ) ) return $username;
        
        $index = 0;
        
        while(true) {
           $index++;
           $username = $parts[0] . $index;
           
           if ( !username_exists( $username ) ) return $username;
        }
    }
}


