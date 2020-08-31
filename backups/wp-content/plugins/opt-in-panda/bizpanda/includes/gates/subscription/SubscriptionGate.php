<?php

namespace bizpanda\includes\gates\subscription;

use bizpanda\includes\gates\ActionGate;
use bizpanda\includes\gates\exceptions\GateException;

/**
 * Subscription Gate.
 */
class SubscriptionGate extends ActionGate {

    /**
     * Handles the proxy request.
     * @throws GateException
     */
    public function handleRequest() {

        $requestType = $this->getRequestParam('requestType');
        $service = $this->getRequestParam('service');

        if( empty( $requestType ) || empty( $service ) ) {
            throw GateException::create( GateException::INVALID_INBOUND_REQUEST, 'The "requestType" or "service" are not defined.');
        }

        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/subscriptions.php';
        $service = \OPanda_SubscriptionServices::getCurrentService();

        if ( empty( $service) ) {
            throw GateException::create( GateException::APP_NOT_CONFIGURED, 'The subscription service is not set.');
        }

        // - service name

        $options = $this->config->get( 'subscription' );

        $serviceName = $options['service'];
        if ( $serviceName !== $service->name  ) {
            throw GateException::create( GateException::APP_NOT_CONFIGURED, sprintf( 'Invalid subscription service "%s".', $serviceName ));
        }
        
        // - request type
        
        $requestType = strtolower( $requestType );
        $allowed = array('check', 'subscribe');

        if ( !in_array( $requestType, $allowed ) ) {
            throw GateException::create( GateException::INVALID_INBOUND_REQUEST, sprintf( 'Invalid request. The action "%s" not found.', $requestType ));
        }
        
        // - identity data

        $identityData = $this->getRequestParam('identityData', []);
        $identityData = $this->normalizeValues( $identityData );
        
        if ( empty( $identityData['email'] )) {
            throw GateException::create( GateException::INVALID_INBOUND_REQUEST, sprintf( 'Unable to subscribe. The email is not specified.' , $requestType ));
        }

        if ( get_option('opanda_forbid_temp_emails', false) ) {
 
            $tempDomains = get_option('opanda_temp_domains', false);
            if ( !empty( $tempDomains ) ) {

                $tempDomains = explode(',', $tempDomains);
                foreach( $tempDomains as $tempDomain ) {
                    $tempDomain = trim($tempDomain);
                    if ( false !== strpos($identityData['email'], $tempDomain) ) {
                        throw new GateException([
                            'message' => get_option('opanda_res_errors_temporary_email', 'Sorry, temporary email addresses cannot be used to unlock content.')
                        ]);
                    }
                }
            }            
        }
        
        // - service data

        $serviceData = $this->getRequestParam('serviceData', []);
        $serviceData = $this->normalizeValues( $serviceData );
        
        // - context data

        $contextData = $this->getRequestParam('contextData', []);
        $contextData = $this->normalizeValues( $contextData );

        // - list id

        $listId = $this->getRequestParam('listId', null);
        if ( empty( $listId ) ) {
            throw GateException::create( GateException::INVALID_INBOUND_REQUEST, 'Unable to subscribe. The list ID is not specified.');
        }
        
        // - double opt-in

        $doubleOptin = $this->getRequestParam('doubleOptin', true);
        $doubleOptin = $this->normalizeValue( $doubleOptin );
        
        // - confirmation

        $confirm = $this->getRequestParam('confirm', true);
        $confirm = $this->normalizeValue( $confirm );
        
        // verifying user data if needed while subscribing
        // works for social subscription
        
        $verified = false; 
        $mailServiceInfo = \OPanda_SubscriptionServices::getServiceInfo();
            
        if ( 'subscribe' === $requestType ) {

            if ( $doubleOptin && in_array( 'quick', $mailServiceInfo['modes'] ) ) {

                $visitorId = isset( $serviceData['visitorId'] ) ? trim ( $serviceData['visitorId'] ) : false;
                $proxy = isset( $serviceData['proxy'] ) ? trim ( $serviceData['proxy'] ) : false;
                $source = isset( $identityData['source'] ) ? trim ( $identityData['source'] ) : false;
                $email = isset( $identityData['email'] ) ? trim ( $identityData['email'] ) : false;

                $verified = $this->verifyEmail( $proxy, $source, $visitorId, $email );
            }     
        }

        // prepares data received from custom fields to be transferred to the mailing service
        
        $itemId = intval( $contextData['itemId'] );
        
        $identityData = $this->prepareDataToSave( $service, $itemId, $identityData );
        $serviceReadyData = $this->mapToServiceIds( $service, $itemId, $identityData );
        $identityData = $this->mapToCustomLabels( $service, $itemId, $identityData );
        
        // checks if the subscription has to be procces via WP
        
        $subscribeMode = get_post_meta($itemId, 'opanda_subscribe_mode', true);
        $subscribeDelivery = get_post_meta($itemId, 'opanda_subscribe_delivery', true);
        
        $isWpSubscription = false;
        
        if ( $service->hasSingleOptIn() 
                && in_array( $subscribeMode, array('double-optin', 'quick-double-optin') ) 
                && ( $service->isTransactional() || $subscribeDelivery == 'wordpress' ) ) {
            
            $isWpSubscription = true;
        }

        // creating subscription service

        $result = array();

        try {

            if ( 'subscribe' === $requestType ) {

                if ( $isWpSubscription ) {

                    // if the use signs in via a social network and we managed to confirm that the email is real,
                    // then we can skip the confirmation process

                    if ( $verified ) {
                        \OPanda_Leads::add( $identityData, $contextData, true, true );
                        return $service->subscribe( $serviceReadyData, $listId, false, $contextData, $verified );
                    } else {
                        $result = $service->wpSubscribe( $identityData, $serviceReadyData, $contextData, $listId, $verified );
                    }

                } else {
                    $result = $service->subscribe( $serviceReadyData, $listId, $doubleOptin, $contextData, $verified );
                }

                do_action('opanda_subscribe',
                    ( $result && isset( $result['status'] ) ) ? $result['status'] : 'error',
                    $identityData, $contextData, $isWpSubscription
                );

            } elseif ( 'check' === $requestType ) {

                if ( $isWpSubscription ) {
                    $result = $service->wpCheck( $identityData, $serviceReadyData, $contextData, $listId, $verified );
                } else {
                    $result = $service->check( $serviceReadyData, $listId, $contextData );
                }

                do_action('opanda_check',
                    ( $result && isset( $result['status'] ) ) ? $result['status'] : 'error',
                    $identityData, $contextData, $isWpSubscription
                );
            }

        } catch( \OPanda_SubscriptionException $exception ) {

            throw new GateException([
                'code' => 'gate.subscription-error',
                'message' => $exception->getMessage(),
                'details' => []
            ]);

        } catch ( \Exception $exception ) {
            throw $exception;
        }

        $result = apply_filters('opanda_subscription_result', $result, $identityData);

        // calls the hook to save the lead in the database
        if ( $result && isset( $result['status'] ) ) {

            $actionData = array(
                'identity' => $identityData,
                'requestType' => $requestType,
                'service' => $options['service'],
                'list' => $listId,
                'doubleOptin' => $doubleOptin,
                'confirm' => $confirm,
                'context' => $contextData
            );

            if ( 'subscribed' === $result['status'] ) {
                do_action('opanda_subscribed', $actionData);
            } else {
                do_action('opanda_pending', $actionData);
            }
        }

        return $result;
    }
}
