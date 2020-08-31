<?php 

class OPanda_ActivecampaignSubscriptionService extends OPanda_Subscription {
    
    public function request( $action, $method = 'GET', $data = array() ) {
        
        $apiKey = get_option('opanda_activecampaign_apikey', false);
        if( empty( $apiKey )) throw new OPanda_SubscriptionException ('The API Key not set.');
        
        $apiUrl= get_option('opanda_activecampaign_apiurl', false);
        if( empty( $apiUrl )) throw new OPanda_SubscriptionException ('The API Url not set.');   
        
        $isPost = $method == 'POST';

        $caller = $isPost ? 'wp_remote_post' : 'wp_remote_get';
        
        $postData = array();
        
        $getData = array(
            'api_key' => $apiKey,
            'api_action' => $action,
            'api_output' => 'json'
        );
        
        $query = '';
        $strPostData = '';
            
        if ( $isPost ) {
            $postData = array_merge($postData, $data);
        } else {
            $getData = array_merge($getData, $data);
        }
        
        foreach( $getData as $key => $value ) $query .= $key . '=' . urlencode($value) . '&';
        $query = rtrim($query, '& ');
        
        foreach( $postData as $key => $value ) $strPostData .= $key . '=' . urlencode($value) . '&';
        $strPostData = rtrim($strPostData, '& ');
        
        $apiUrl = rtrim($apiUrl, '/ ');
        $url = $apiUrl . '/admin/api.php?' . $query;
            
        $args = array();
        if ( !empty( $strPostData ) ) $args['body'] = $strPostData;

        $result = $caller($url, $args);

        if (is_wp_error( $result )) {
            throw new OPanda_SubscriptionException( sprintf( __( 'Unexpected error occurred during connection to ActiveCampaign. %s', 'optinpanda' ), $result->get_error_message() ) );
        }

        if ( empty( $result['body'] ) ) return array();

        $data = json_decode($result['body'], true);
        
        if ($data === FALSE) {
            throw new OPanda_SubscriptionException( sprintf( __( 'Unexpected error occurred during connection to SendInBlue. %s', 'optinpanda' ), $result['body'] ) );  
        }
        
        if ( $data['result_code'] === 0 && strpos( $data['result_message'], 'Nothing is returned') === false && strpos( $data['result_message'], 'ritornato niente') === false ) {
            throw new OPanda_SubscriptionException( $data['result_message'] );  
        }  
        
        return $data;
    }

    /**
     * Returns lists available to subscribe.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getLists() {
        
        $response = $this->request('list_list', 'GET', array('ids' => 'all'));
        
        $lists = array();
        
        foreach( $response as $key => $item ) {
            if (!is_numeric($key)) continue;
            
            $lists[] = array(
                'title' => $item['name'],
                'value' => $item['id']
            );
        }

        return array(
            'items' => $lists
        ); 
    }

    /**
     * Subscribes the person.
     */
    public function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified ) {

        $email = $identityData['email'];
        $fields = $identityData;
        
        $firstName = '';
        $lastName = '';
        
        if ( !empty( $identityData['name'] ) ) $firstName = $identityData['name'];
        if ( !empty( $identityData['family'] ) ) $lastName = $identityData['family'];
        if ( empty( $firstName ) && !empty( $identityData['displayName'] ) )$firstName = $identityData['displayName'];

        $fields = $this->refine( $fields );
        unset( $fields['email'] );
        
        $response = $this->request('contact_view_email', 'GET', array('email' => $email));
        $exists = isset( $response['id'] );
        
        $data = array();
        
        $data['email'] = $email;
        $data['ip4'] = $_SERVER['REMOTE_ADDR'];
        
        if ( !empty( $firstName ) ) $data['first_name'] = $firstName;
        if ( !empty( $lastName ) ) $data['last_name'] = $lastName;
        
        $data['status[' . $listId . ']'] = 1;
        $data['instantresponders[' . $listId . ']'] = 1;
        
        foreach( $fields as $fieldKey => $fieldValue ) {
            $data[ urlencode( 'field[%' . $fieldKey . '%,0]' ) ] = $fieldValue;
        }
        
        // already exits
        
        if ( $exists ) {
            
            $lists = explode('-', $response['listslist']);

            if ( !in_array('' . $listId, $lists) ) {
                
                $data['id'] = $response['id'];

                $lists[] = $listId;
                foreach($lists as $listId) {
                    $data['p[' . $listId . ']'] = $listId;
                }
                
                $response = $this->request('contact_edit', 'POST', $data);
            }
            
            return array('status' => 'subscribed');
        }

        $data['p[' . $listId . ']'] = $listId;

        $response = $this->request('contact_add', 'POST', $data);
        return array('status' => 'subscribed');
    }
    
    /**
     * Checks if the user subscribed.
     */  
    public function check( $identityData, $listId, $contextData ) { 
        
        return array('status' => 'subscribed');
    }
         
    /**
     * Returns custom fields.
     */
    public function getCustomFields( $listId ) {
        
        $response = $this->request('list_field_view', 'GET', array('ids' => 'all'));

        if ( empty( $response ) ) return array();

        $customFields = array();
        
        $mappingRules = array(
            'radio' => 'dropdown',        
            'radio' => 'dropdown',
            'listbox' => 'dropdown',
            'textarea' => array('text', 'checkbox', 'hidden'),
            'text' => array('text', 'checkbox', 'hidden')
        );

        foreach( $response as $index => $item ) {
            if ( !is_numeric( $index) ) continue;
            $fieldType = $item['type'];
                    
            $pluginFieldType = isset( $mappingRules[$fieldType] ) 
                    ? $mappingRules[$fieldType] 
                    : strtolower( $fieldType );

            $can = array(
                'changeType' => true,
                'changeReq' => true,
                'changeDropdown' => false,
                'changeMask' => true
            );
            
            $fieldOptions = array();
            
            if ( 'dropdown' === $pluginFieldType ) {
                
                foreach ( $item['options'] as $choice ) {
                    $fieldOptions['choices'][] = $choice['value'];
                }
            }
            
            $fieldOptions['req'] = intval( $item['isrequired'] ) == 1;

            $customFields[] = array(
                
                'fieldOptions' => $fieldOptions,
                
                'mapOptions' => array(
                    'req' => intval( $item['isrequired'] ) == 1,
                    'id' => $item['perstag'],
                    'name' => $item['perstag'],
                    'title' => sprintf('%s [%s]', $item['title'], $item['perstag'] ),
                    'labelTitle' => $item['title'],
                    'mapTo' => is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                    'service' => $item
                ),
                
                'premissions' => array(
                    
                    'can' => $can,
                    'notices' => array(
                        'changeReq' => __('You can change this checkbox in your ActiveCampaign account.', 'bizpanda'),
                        'changeDropdown' => __('Please visit your ActiveCampaign account to modify the choices.', 'bizpanda')
                    ), 
                )
            );
        }

        return $customFields;
    }
    
    public function getNameFieldIds() {
        return array( 'FIRSTNAME' => 'name', 'LASTNAME' => 'family' );
    }
}
