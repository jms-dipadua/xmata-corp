<?php

namespace bizpanda\includes\gates;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\exceptions\GateException;
use bizpanda\includes\gates\twitter\TwitterGate;

/**
 * Action Gate.
 * Provides common methods for Action flow.
 */
abstract class ActionGate extends Gate {

    /**
     * Process names of the identity data.
     * @param $service \OPanda_Subscription
     * @param $itemId
     * @param $identityData
     * @return
     */
    public function prepareDataToSave( $service, $itemId, $identityData ) {

        // move the values from the custom fields like FNAME, LNAME

        if ( !empty( $service ) ) {

            $formType = get_post_meta( $itemId, 'opanda_form_type', true );
            $strFieldsJson = get_post_meta( $itemId, 'opanda_fields', true );

            if ( 'custom-form' == $formType && !empty( $strFieldsJson ) ) {

                $fieldsData = json_decode( $strFieldsJson, true );
                $ids = $service->getNameFieldIds();

                $newIdentityData = $identityData;

                foreach( $identityData as $itemId => $itemValue ) {

                    foreach($fieldsData as $fieldData) {

                        if ( !isset( $fieldData['mapOptions']['id'] ) ) continue;
                        if ( $fieldData['fieldOptions']['id'] !== $itemId ) continue;

                        $mapId = $fieldData['mapOptions']['id'];

                        if ( in_array( $fieldData['mapOptions']['mapTo'], array( 'separator', 'html', 'label' ) ) ) {
                            unset($newIdentityData[$itemId]);
                            continue;
                        }

                        foreach( $ids as $nameFieldId => $nameFieldType ) {
                            if ( $mapId !== $nameFieldId ) continue;
                            $newIdentityData[$nameFieldType] = $itemValue;
                            unset($newIdentityData[$itemId]);
                        }
                    }
                }

                $identityData = $newIdentityData;
            }
        }

        // splits the full name into 2 parts

        if ( isset( $identityData['fullname'] ) ) {

            $fullname = trim( $identityData['fullname'] );
            unset( $identityData['fullname'] );

            $parts = explode(' ', $fullname);
            $nameParts = array();

            foreach( $parts as $part ) {
                if ( trim($part) == '' ) continue;
                $nameParts[] = $part;
            }

            if ( count($nameParts) == 1 ) {
                $identityData['name'] = $nameParts[0];
            } else if ( count($nameParts) > 1) {
                $identityData['name'] = $nameParts[0];
                $identityData['displayName'] = implode(' ', $nameParts);
                unset( $nameParts[0] );
                $identityData['family'] = implode(' ', $nameParts);
            }
        }

        return $identityData;
    }

    /**
     * Replaces keys of identity data of the view 'cf3' with the ids of custom fields in the mailing services.
     * @param $service \OPanda_Subscription
     * @param $itemId
     * @param $identityData
     * @return array
     */
    public function mapToServiceIds( $service, $itemId, $identityData ) {

        $formType = get_post_meta( $itemId, 'opanda_form_type', true );
        $strFieldsJson = get_post_meta( $itemId, 'opanda_fields', true );

        if ( 'custom-form' !== $formType || empty( $strFieldsJson ) ) {

            $data = array();
            if ( isset( $identityData['email'] ) ) $data['email'] = $identityData['email'];
            if ( isset( $identityData['name'] ) ) $data['name'] = $identityData['name'];
            if ( isset( $identityData['family'] ) ) $data['family'] = $identityData['family'];
            return $data;
        }

        $fieldsData = json_decode( $strFieldsJson, true );

        $data = array();
        foreach( $identityData as $itemId => $itemValue ) {

            if ( in_array( $itemId, array('email', 'fullname', 'name', 'family', 'displayName') ) ) {
                $data[$itemId] = $itemValue;
                continue;
            }

            foreach($fieldsData as $fieldData) {

                if ( $fieldData['fieldOptions']['id'] === $itemId ) {
                    $mapId = $fieldData['mapOptions']['id'];
                    $data[$mapId] = $service->prepareFieldValueToSave( $fieldData['mapOptions'], $itemValue );
                }
            }
        }

        return $data;
    }

    /**
     * Replaces keys of identity data of the view 'cf3' with the labels the user enteres in the locker settings.
     * @param $service
     * @param $itemId
     * @param $identityData
     * @return array
     */
    public function mapToCustomLabels( $service, $itemId, $identityData ) {

        $formType = get_post_meta( $itemId, 'opanda_form_type', true );
        $strFieldsJson = get_post_meta( $itemId, 'opanda_fields', true );

        if ( 'custom-form' !== $formType || empty( $strFieldsJson ) ) return $identityData;

        $fieldsData = json_decode( $strFieldsJson, true );

        $data = array();
        foreach( $identityData as $itemId => $itemValue ) {

            if ( in_array( $itemId, array('email', 'fullname', 'name', 'family', 'displayName') ) ) {
                $data[$itemId] = $itemValue;
                continue;
            }

            foreach($fieldsData as $fieldData) {

                if ( $fieldData['fieldOptions']['id'] !== $itemId ) continue;
                $label = $fieldData['serviceOptions']['label'];

                if ( empty( $label ) ) continue 2;
                $data['{' . $label . '}'] = $itemValue;
                continue 2;
            }

            $data[$itemId] = $itemValue;
        }

        return $data;
    }


    /**
     * Verifies if the email is actually was received from the social proxy.
     * @param $proxy string An URL that was used to make proxy requests.
     * @param $source string A source name from where the data received (facebook, twitter etc.)
     * @param $visitorId string A unique visitor ID used by proxy to identify requests.
     * @param $email string An email to verify.
     * @return bool True if the email is verified.
     * @throws GateBridgeException
     */
    protected function verifyEmail( $proxy, $source, $visitorId, $email ) {
        if ( empty( $proxy ) || empty( $visitorId ) || empty( $email ) ) return false;

        $remoteProxy = opanda_remote_social_proxy_url();
        $localProxy = opanda_local_proxy_url();

        // if the email was received from the remote proxy

        if ( strpos( $proxy, $remoteProxy ) !== false ) {

            $url = opanda_remote_social_proxy_url() . '/verify';

            $response = wp_remote_post( $url, [

                'body' => [
                    'email' => $email,
                    'visitorId' => $visitorId,
                    'source' => $source
                ]
            ]);

            $content = isset( $response['body'] ) ? $response['body'] : '';
            $json = json_decode( $content, true );

            if ( !$json ) {

                throw GateBridgeException::create( GateBridgeException::UNEXPECTED_RESPONSE, [
                    'response' => $response
                ]);
            }

            if ( isset( $json['error'] ) ) {

                throw GateBridgeException::create( GateBridgeException::ERROR_RESPONSE, [
                    'clarification' => $json['error'],
                    'response' => $response
                ]);
            }

            if ( isset( $json['success'] ) &&  $json['success'] && $json['email'] === $email ) {
                return true;
            }

            return false;
        }

        // if the email was received from the local proxy

        if ( strpos( $proxy, $localProxy ) !== false ) {

            // as visitor ID is passed in the array of the service data (not via the parameter visitorId),
            // we need to re-init sessions to access the correct session data

            $currentSessionId = $this->session->sessionId;
            $this->session->sessionId = $visitorId;

            $verifiedEmail = $this->getVisitorValue($source . '_email');

            $this->session->sessionId = $currentSessionId;

            if ( $email === $verifiedEmail ) return true;
            return false;
        }

        return false;
    }
}