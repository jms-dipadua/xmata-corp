<?php 

class OPanda_SendGridSubscriptionService extends OPanda_Subscription {

    protected $isLegacy = false;

    public function __construct( $data = array() ) {
        parent::__construct( $data );

        $apiVersion = get_option('opanda_sendgrid_api_version', 'auto');

        if ( 'auto' === $apiVersion ) {
            $this->isLegacy = get_option('opanda_sendgrid_is_legacy', null);

            if ( $this->isLegacy === null ) $this->isLegacy = $this->testLegacy();
        } else {

            if ( 'legacy-v3' == $apiVersion ) {
                $this->isLegacy = true;
            } else {
                $this->isLegacy = false;
            }
        }
    }

    /**
     * Checks whether we can use legacy API.
     */
    protected function testLegacy() {

        $sg = $this->getInstance();

        $response = $sg->contactdb()->lists()->get();
        $code = $response->statusCode();

        // access forbidden
        if ( 403 == $code ) {
            $this->isLegacy = false;
        } else {
            $this->isLegacy = true;
        }

        update_option('opanda_sendgrid_is_legacy', $this->isLegacy ? 1 : 0);
        return $this->isLegacy;
    }

    public function getInstance( $options = [] ) {

        require_once 'libs/client.php';

        $apiKey = get_option('opanda_sendgrid_apikey');

        $headers = array(
            'Authorization: Bearer ' . $apiKey
        );

        return new Opanda_Sendgrid_Client('https://api.sendgrid.com', $headers, '/v3');
    }

    /**
     * Returns lists available to subscribe.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getLists() {
        if ( $this->isLegacy ) return $this->getLists_Legacy();

        $sg = $this->getInstance();
        $response = $sg->marketing()->lists()->get(null, ['page_size' => 50]);

        $data = $this->handleResponse( $response );

        $lists = array();
        foreach( $data->result as $item ) {
            $lists[] = array(
                'title' => $item->name,
                'value' => $item->id
            );
        }
        
        return array(
            'items' => $lists
        ); 
    }
    
    /**
     * Sends an email.
     */
    public function send( $to, $subject, $body ) {
        
        $sg = $this->getInstance();

        $response = $sg->mail()->send()->post(array(
            'personalizations' => array(
                array(
                    'to' => array(
                        array('email' => $to)
                    ),
                    'subject' => $subject
                )
            ),
            'from' => array(
                'email' => get_option('opanda_sender_email', get_bloginfo('admin_email')),
                'name' => get_option('opanda_sender_name', get_bloginfo('name'))
            ),
            'content' => array(
                array(
                    'type' => 'text/html',
                    'value' => $body
                )
            )
        ));
        
        $this->handleResponse( $response, 202 );
    }

    /**
     * Subscribes the person.
     */
    public function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified ) {
        if ( $this->isLegacy ) return $this->subscribe_Legacy( $identityData, $listId, $doubleOptin, $contextData, $verified );

        $vars = $this->refine( $identityData, true );

        if ( isset( $contextData['itemId']) ) {
            $vars = $this->extractCustomFields ( $identityData, $contextData['itemId'] );
        }

        if ( empty( $vars['first_name'] ) && !empty( $identityData['name'] ) ) $vars['first_name'] = $identityData['name'];
        if ( empty( $vars['last_name'] ) && !empty( $identityData['family'] ) ) $vars['last_name'] = $identityData['family'];
        if ( empty( $vars['first_name'] ) && !empty( $identityData['displayName'] ) ) $vars['first_name'] = $identityData['displayName'];
        
        $sg = $this->getInstance();

        /*
        Code to check a job ID returned after updating:

        $jobId = '6169d213-9b27-4c21-9a85-2d76f05f5110';
        $response = $sg->marketing()->contacts()->imports()->{$jobId}()->get();

        print_r( $this->handleResponse( $response ));
        exit;
        */

        $response = $sg->marketing()->contacts()->put([
            'list_ids' => [ $listId ],
            'contacts' => [ $vars ]
        ]);

        $this->handleResponse( $response );
        return array('status' => 'subscribed');
    }

    /**
     * Extracts custom fields from the identity data and adds them to under a key 'custom_fields'.
     */
    protected function extractCustomFields( $identityData, $lockedId ) {

        $fields = opanda_get_item_option( $lockedId, 'fields' );
        if ( empty( $fields ) ) return [];

        $fields = json_decode( $fields, true );
        if ( empty( $fields ) ) return [];

        $customFields = [];
        $identity = [];

        foreach( $identityData as $name => $value ) {
            $found = false;

            // can be removed in the next versions
            if ( 'First Name' === $name ) $name = 'first_name';
            if ( 'Last Name' === $name ) $name = 'last_name';

            foreach( $fields as $options ) {

                // real custom fields has to have the property _metadata
                // so this way we skip virtual custom fields like 'first_name', 'last_name' etc.
                if ( !isset( $options['mapOptions']['service']['_metadata'] ) ) continue;

                if ( isset( $options['mapOptions']['name'] ) && $name == $options['mapOptions']['name'] ) {

                    $fieldId = isset( $options['mapOptions']['service']['id'] ) ? $options['mapOptions']['service']['id'] : false;
                    if ( empty( $fieldId ) ) continue;

                    $customFields[$fieldId] = $value;
                    $found = true;
                    break;
                }
            }

            if ( !$found ) {
                $identity[$name] = $value;
            }
        }

        if ( !empty( $customFields ) ) {
            $identity['custom_fields'] = $customFields;
        }

        return $identity;
    }

    /**
     * Checks if the user subscribed. Not used.
     */
    public function check( $identityData, $listId, $contextData ) {
        throw new Exception('Not implemented.');
    }
    
    /**
     * Prepares values enters by the user to save.
     */
    public function prepareFieldValueToSave( $mapOptions, $value ) {
        if ( $this->isLegacy ) return $this->prepareFieldValueToSave_Legacy( $mapOptions, $value );

        if ( empty( $value ) ) return $value;
        
        $fieldType = $mapOptions['service']['field_type'];

        if ( strtolower( $fieldType ) == 'date' ) {

            // date format for SendGrid:
            // https://github.com/sendgrid/sendgrid-nodejs/issues/953#issuecomment-543799228

            $timestamp = strtotime($value);
            date_default_timezone_set('UTC');
            return date("Y-m-d\TH:i:s\Z", $timestamp);
        }

        if ( strtolower( $fieldType ) == 'number' ) {
            return (int)$value;
        }

        return $value;
    }

    /**
     * Returns custom fields.
     */
    public function getCustomFields( $listId ) {
        if ( $this->isLegacy ) return $this->getCustomFields_Legacy( $listId );

        $sg = $this->getInstance();
        $response = $sg->marketing()->field_definitions()->get();
        $data = $this->handleResponse( $response );

        array_unshift($data->custom_fields, (object)array('id' => 'last_name', 'name' => 'last_name', 'field_type' => 'text'));
        array_unshift($data->custom_fields, (object)array('id' => 'first_name', 'name' => 'first_name', 'field_type' => 'text'));

        $customFields = array();
        $mappingRules = array(
            'Text' => array('text', 'checkbox', 'hidden'),
            'Number' => array('integer', 'checkbox')
        );

        foreach( $data->custom_fields as $customFieldItem ) {
            $fieldType = $customFieldItem->field_type;
                    
            $pluginFieldType = isset( $mappingRules[$fieldType] ) 
                    ? $mappingRules[$fieldType] 
                    : strtolower( $fieldType );
            
            if ( in_array($pluginFieldType, array('email'))) continue;            
            
            $can = array(
                'changeType' => true,
                'changeReq' => true,
                'changeDropdown' => false,
                'changeMask' => true
            );
            
            $fieldOptions = array();            
            $fieldOptions['req'] = false;

            $customFields[] = array(
                
                'fieldOptions' => $fieldOptions,
                
                'mapOptions' => array(
                    'req' => false,
                    'id' => $customFieldItem->name,
                    'name' => $customFieldItem->name,
                    'title' => $customFieldItem->name,
                    'labelTitle' => $customFieldItem->name,
                    'mapTo' => is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                    'service' => (array)$customFieldItem
                ),
                
                'premissions' => array(
                    
                    'can' => $can,
                    'notices' => array()
                )
            );
        }

        return $customFields;
    }

    /**
     * Maps field names.
     * @return array
     */
    public function getNameFieldIds() {
        return array( 'first_name' =>  'name', 'last_name' => 'family' );
    }

    /**
     * Handles a request result.
     * @param $response
     * @return mixed
     * @throws OPanda_SubscriptionException
     */
    protected function handleResponse( $response, $validCode = null ) {
        
        $code = $response->statusCode();
        $bodyJson = $response->body();

        $body = json_decode($bodyJson);

        if ( $code == 401 ) {
            throw new OPanda_SubscriptionException( __('Access denied. Please make sure that you granted Full Access for your SendGrid API Key.', 'optinpanda') );
        }

        if ( isset( $body->errors[0]->message ) ) {
            throw new OPanda_SubscriptionException( $body->errors[0]->message );
        }

        // sending email return 202 code an empty body,
        // we need to handle it as a successful result
        if ( $validCode && $code == $validCode ) return $body;

        if ( empty( $body ) ) {
            throw new OPanda_SubscriptionException( __('Unexpected HTTP error. Code: ', 'optinpanda' ) . $code );
        }

        return $body;
    }

    // ------------------------------------------------
    // Legacy API
    // ------------------------------------------------

    /**
     * Returns lists available to subscribe.
     * Will be removed after a while.
     *
     * @return mixed[]
     * @throws OPanda_SubscriptionException
     * @since 1.0.0
     */
    public function getLists_Legacy() {

        $sg = $this->getInstance();
        $response = $sg->contactdb()->lists()->get();
        $data = $this->handleResponse( $response );

        $lists = array();
        foreach( $data->lists as $item ) {
            $lists[] = array(
                'title' => $item->name,
                'value' => $item->id
            );
        }

        return array(
            'items' => $lists
        );
    }

    /**
     * Subscribes the person.
     * Will be removed after a while.
     */
    public function subscribe_Legacy($identityData, $listId, $doubleOptin, $contextData, $verified ) {

        $vars = $this->refine( $identityData, true );
        $email = $identityData['email'];

        if ( empty( $vars['first_name'] ) && !empty( $identityData['name'] ) ) $vars['first_name'] = $identityData['name'];
        if ( empty( $vars['last_name'] ) && !empty( $identityData['family'] ) ) $vars['last_name'] = $identityData['family'];
        if ( empty( $vars['first_name'] ) && !empty( $identityData['displayName'] ) ) $vars['first_name'] = $identityData['displayName'];

        $sg = $this->getInstance();
        $response = $sg->contactdb()->recipients()->search()->get(null, array('email' => $email));
        $data = $this->handleResponse( $response );

        // if already exists

        if ( !empty( $data->recipients ) ) {

            $subscriberId = isset( $data->recipients[0]->id )
                ? $data->recipients[0]->id
                : 0;

            // adding to a list

            if ( $subscriberId ) {
                $response = $sg->contactdb()->lists()->_($listId)->recipients()->_($subscriberId)->post();
                $data = $this->handleResponse( $response, 201 );
            }

            return array('status' => 'subscribed');
        }

        // adding a new contact

        $response = $sg->contactdb()->recipients()->post(array($vars));
        $data = $this->handleResponse( $response, 201 );

        $subscriberId = isset( $data->persisted_recipients[0] )
            ? $data->persisted_recipients[0]
            : 0;

        if ( !$subscriberId ) {
            throw new OPanda_SubscriptionException( __( 'Unable to add a new user. Please contact OnePress support.','optinpanda') );
        }

        // adding to a list

        $response = $sg->contactdb()->lists()->_($listId)->recipients()->_($subscriberId)->post();
        $this->handleResponse( $response, 201 );

        return array('status' => 'subscribed');
    }

    /**
     * Prepares values enters by the user to save.
     * Will be removed after a while.
     */
    public function prepareFieldValueToSave_Legacy($mapOptions, $value ) {
        if ( empty( $value ) ) return $value;

        $fieldType = $mapOptions['service']['type'];

        if ( $fieldType == 'date' ) {
            return strtotime($value);
        }

        return $value;
    }

    /**
     * Returns custom fields for legacy API.
     * Will be removed after a while.
     */
    public function getCustomFields_Legacy( $listId ) {

        $sg = $this->getInstance();
        $response = $sg->contactdb()->custom_fields()->get();
        $data = $this->handleResponse( $response );

        array_unshift($data->custom_fields, (object)array('id' => 'last_name', 'name' => 'last_name', 'type' => 'text'));
        array_unshift($data->custom_fields, (object)array('id' => 'first_name', 'name' => 'first_name', 'type' => 'text'));

        $customFields = array();
        $mappingRules = array(
            'text' => array('text', 'checkbox', 'hidden'),
            'number' => array('integer', 'checkbox')
        );

        foreach( $data->custom_fields as $customFieldItem ) {
            $fieldType = $customFieldItem->type;

            $pluginFieldType = isset( $mappingRules[$fieldType] )
                ? $mappingRules[$fieldType]
                : strtolower( $fieldType );

            if ( in_array($pluginFieldType, array('email'))) continue;

            $can = array(
                'changeType' => true,
                'changeReq' => true,
                'changeDropdown' => false,
                'changeMask' => true
            );

            $fieldOptions = array();
            $fieldOptions['req'] = false;

            $customFields[] = array(

                'fieldOptions' => $fieldOptions,

                'mapOptions' => array(
                    'req' => false,
                    'id' => $customFieldItem->name,
                    'name' => $customFieldItem->name,
                    'title' => $customFieldItem->name,
                    'labelTitle' => $customFieldItem->name,
                    'mapTo' => is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                    'service' => (array)$customFieldItem
                ),

                'premissions' => array(

                    'can' => $can,
                    'notices' => array()
                )
            );
        }

        return $customFields;
    }
}
