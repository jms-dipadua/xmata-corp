<?php

add_action('wp_ajax_opanda_connect', 'opanda_connect');
add_action('wp_ajax_nopriv_opanda_connect', 'opanda_connect');

/**
 * Handles requests from the jQuery version of the locker plugin.
 */
function opanda_connect() {    

    // shows errors in the development mode
    if ( defined('ONP_DEVELOPING_MODE') && ONP_DEVELOPING_MODE ) {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
    } else {
        error_reporting(-1);
    }

    $handlerName = isset( $_REQUEST['opandaHandler'] ) ? $_REQUEST['opandaHandler'] : null;
    $requestType = isset( $_REQUEST['opandaRequestType'] ) ? $_REQUEST['opandaRequestType'] : null;

    $socialHandlers = ['facebook', 'twitter', 'google', 'linkedin', 'fblike', 'fbshare'];
    $actionHandlers = ['subscription', 'signup', 'lead'];

    $isSocialHandler = in_array( $handlerName, $socialHandlers );

    $allowed = array_merge($socialHandlers, $actionHandlers);

    if ( empty( $handlerName ) || !in_array( $handlerName, $allowed ) ) {
        header( 'Status: 403 Forbidden' );
        header( 'HTTP/1.1 403 Forbidden' );
        exit;
    }

    if ( 'fblike' === $handlerName ) {
        include OPANDA_BIZPANDA_DIR . "/includes/gates/facebook/LikeDialog.html";
        exit;
    } elseif (  'fbshare' === $handlerName ) {
        include OPANDA_BIZPANDA_DIR . "/includes/gates/facebook/ShareDialog.html";
        exit;
    }

    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/Gate.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/GateBridge.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/ActionGate.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/OAuthGate.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/OAuthGateBridge.php";

    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/context/IContextReader.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/context/IContextReaderWriter.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/context/ConfigService.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/context/RequestService.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/context/SessionService.php";

    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/exceptions/GateExceptionPriority.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/exceptions/GateException.php";
    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/exceptions/GateBridgeException.php";

    if ( 'linkedin' === $handlerName ) {
        require_once OPANDA_BIZPANDA_DIR . "/includes/gates/$handlerName/LinkedinScope.php";
    }

    if ( $isSocialHandler ) {
        require_once OPANDA_BIZPANDA_DIR . "/includes/gates/$handlerName/" . ucwords( $handlerName ) . "Bridge.php";
    }

    require_once OPANDA_BIZPANDA_DIR . "/includes/gates/$handlerName/" . ucwords( $handlerName ) . "Gate.php";

    $handlerClass = 'bizpanda\\includes\\gates\\' . $handlerName . '\\' . ucwords( $handlerName ) . 'Gate';

    try {

        if ( $isSocialHandler ) {

            if ( empty( $requestType ) ) {
                include OPANDA_BIZPANDA_DIR . "/includes/gates/$handlerName/OAuthDialog.html";
                exit;
            }
        }

        @header('Content-Type: application/json');

        $gate = new $handlerClass();
        $result = $gate->handleRequest();

        $result['success'] = true;
        echo json_encode( $result );

    } catch ( Exception $exception ) {

        if (
            $exception instanceof bizpanda\includes\gates\exceptions\GateBridgeException ||
            $exception instanceof bizpanda\includes\gates\exceptions\GateException ) {

            $result = [
                'success' => false,
                'code' => $exception->getExceptionCode(),
                'error' => $exception->getExceptionVisibleMessage(),
                'details' => $exception->getExceptionDetails()
            ];

        } else {

            $result = [
                'success' => false,
                'error' => __('Something weird happened. We will fix it soon. Please try again later.', 'bizpanda'),
                'details' => [
                    'clarification' => $exception->getMessage()
                ]
            ];
        }

        if ( defined('ONP_DEVELOPING_MODE') && ONP_DEVELOPING_MODE ) {
            $result['trace'] = $exception->getTraceAsString();
        }

        echo json_encode( $result );
    }

    exit;
}

/**
 * Returns the handler options.
 * @param $handlerName string
 * @return array
 */
function opanda_get_handler_options( $handlerName ) {

    switch ( $handlerName ) {

        case 'facebook':

            $callbackUrl = !( defined('ONP_DEVELOPING_MODE') && ONP_DEVELOPING_MODE )
                ? opanda_local_proxy_url(['opandaHandler' => $handlerName])
                : 'https://gate.sociallocker.app/fauth-wp-dev';

            return [
                'clientId' => get_option('opanda_facebook_app_id'),
                'clientSecret' => get_option('opanda_facebook_secret'),
                'callbackUrl' => $callbackUrl
            ];

        case 'twitter':

            $callbackUrl = !( defined('ONP_DEVELOPING_MODE') && ONP_DEVELOPING_MODE )
                ? opanda_local_proxy_url(['opandaHandler' => $handlerName])
                : 'https://gate.sociallocker.app/tauth-wp-dev';

            $params = [
                'read' => [
                    'clientId' => get_option('opanda_twitter_social_app_consumer_key'),
                    'clientSecret' => get_option('opanda_twitter_social_app_consumer_secret')
                ],

                'write' => [
                    'clientId' => get_option('opanda_twitter_signin_app_consumer_key'),
                    'clientSecret' => get_option('opanda_twitter_signin_app_consumer_secret'),
                ],

                'callbackUrl' => $callbackUrl
            ];

            if ( empty( $params['read']['clientId'] ) ) {
                $params['read'] = $params['write'];
            }

            return $params;

        case 'google':

            $callbackUrl = !( defined('ONP_DEVELOPING_MODE') && ONP_DEVELOPING_MODE )
                ? opanda_local_proxy_url(['opandaHandler' => $handlerName])
                : 'https://gate.sociallocker.app/gauth-wp-dev';

            return [
                'clientId' => get_option('opanda_google_client_id'),
                'clientSecret' => get_option('opanda_google_client_secret'),
                'callbackUrl' => $callbackUrl
            ];

        case 'linkedin':

            $callbackUrl = !( defined('ONP_DEVELOPING_MODE') && ONP_DEVELOPING_MODE )
                ? opanda_local_proxy_url(['opandaHandler' => $handlerName])
                : 'https://gate.sociallocker.app/lauth-wp-dev';

            return [
                'clientId' => get_option('opanda_linkedin_client_id'),
                'clientSecret' => get_option('opanda_linkedin_client_secret'),
                'callbackUrl' => $callbackUrl
            ];

        case 'subscription':

            return array(
                'service' => get_option('opanda_subscription_service', 'database')
            );

        case 'signup':

            return array();
    }
}

/**
 * Returns the lists available for the current subscription service.
 * 
 * @since 1.0.0
 * @return void
 */
function opanda_get_subscrtiption_lists() {

    require OPANDA_BIZPANDA_DIR.'/admin/includes/subscriptions.php';    
    
    try {
        
        $service = OPanda_SubscriptionServices::getCurrentService();

        $lists = $service->getLists();
        echo json_encode($lists); 
        
    } catch (Exception $ex) {
        echo json_encode( array('error' => 'Unable to get the lists: ' . $ex->getMessage() ) ); 
    }

    exit;
}

add_action( 'wp_ajax_opanda_get_subscrtiption_lists', 'opanda_get_subscrtiption_lists' );

/**
 * Returns the lists available for the current subscription service.
 * 
 * @since 1.0.0
 * @return void
 */
function opanda_get_custom_fields() {

    require OPANDA_BIZPANDA_DIR.'/admin/includes/subscriptions.php';    
    
    try {
        
        $listId = isset( $_POST['opanda_list_id'] ) ? $_POST['opanda_list_id'] : null;
        $service = OPanda_SubscriptionServices::getCurrentService();

        $fields = $service->getCustomFields( $listId );
        echo json_encode($fields); 
        
    } catch (Exception $ex) {
        echo json_encode( array('error' => $ex->getMessage() ) ); 
    }

    exit;
}

add_action( 'wp_ajax_opanda_get_custom_fields', 'opanda_get_custom_fields' );