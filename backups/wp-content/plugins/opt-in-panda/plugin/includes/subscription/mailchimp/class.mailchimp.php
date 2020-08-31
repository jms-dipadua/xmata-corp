<?php 

class OPanda_MailchimpSubscriptionService extends OPanda_Subscription {
    
    public function initMailChimpLibs( $version = 2 ) {
        
        $this->apiKey = get_option('opanda_mailchimp_apikey');

        if ( $version == 2 ) {
            require_once 'libs/mailchimp.php';    
            return new OPanda_MailChimp( $this->apiKey );  
        } 
        
        if ( $version == 3 ) {
            require_once 'libs/mailchimpV3.php';    
            return new OPanda_MailChimpV3( $this->apiKey );  
        } 
    }

    /**
     * Returns lists available to subscribe.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getLists() {
        
        $MailChimp = $this->initMailChimpLibs();
        $response = $MailChimp->call('lists/list', array('limit' => 99));

        if ( !$response ) {
            throw new OPanda_SubscriptionException( __( 'The API Key is incorrect.', 'optinpanda' ) );   
        }
        
        if ( !empty( $response['error'] ) ) {
            throw new OPanda_SubscriptionException( $response['error'] );   
        }
        
        $lists = array();
        foreach( $response['data'] as $value ) {
            $lists[] = array(
                'title' => $value['name'],
                'value' => $value['id']
            );
        }
        
        return array(
            'items' => $lists
        ); 
    }
    
    /**
     * Returns groups available to subscribe for the given list id.
     * 
     * @since 1.5.5
     * @return mixed[]
     */
    public function getGroups( $listId ) {

        $MailChimp = $this->initMailChimpLibs(3);
        $response = $MailChimp->get("lists/$listId/interest-categories?count=50");

        $return = array();
        foreach( $response["categories"] as $group ) {

                $groupId = $group["id"];
                $interests = $MailChimp->get("lists/$listId/interest-categories/$groupId/interests");

                $groupItem = array(
                    'id' => $groupId,
                    'title' => $group["title"],
                    'items' => array()
                );

                foreach( $interests["interests"] as $groupValue ) {
                    $groupItem['items'][] = array(
                        $groupValue['id'], $groupValue['name']
                    );
                }

                $return[] = $groupItem;
        }
        
        return $return;
    } 

    /**
     * Subscribes the person.
     */
    public function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified ) {

        $MailChimp = $this->initMailChimpLibs(3);

        $vars = $this->refine( $identityData );

        $email = strtolower( $identityData['email'] );
        $hash = md5( $email );

        if ( empty( $vars['FNAME'] ) && !empty( $identityData['name'] ) ) $vars['FNAME'] = $identityData['name'];
        if ( empty( $vars['LNAME'] ) && !empty( $identityData['family'] ) ) $vars['LNAME'] = $identityData['family'];
        if ( empty( $vars['FNAME'] ) && !empty( $identityData['displayName'] ) ) $vars['FNAME'] = $identityData['displayName'];

        $sendWelcomeMessage = (bool)get_option('opanda_mailchimp_welcome', true);

        $interests = array();
        $itemId = isset( $contextData['itemId'] ) ? intval( $contextData['itemId'] ) : 0;
        if ( $itemId ) {
            $groups = get_post_meta($itemId , "opanda_mailchimp_groups", true);
            $groups = json_decode( $groups );
            if ( $groups ) {
                foreach ( $groups as $group ) {
                    $interests[$group] = true;
                }
            }
        }

        // checks if a subscriber exists

        $response = $MailChimp->get("lists/$listId/members/$hash", array('fields' => 'status,email_address'));

        $exists = false;
        $needToReSubscribe = false;

        if ( isset( $response['email_address'] ) ) {
            $exists = true;

            if ( in_array( $response['status'], ['unsubscribed', 'cleaned', 'pending'] ) ) {
                $needToReSubscribe = true;
            }
        }

        $data = [
            'email_address'     => $email,
            'merge_fields'      => $vars,
            'interests'         => $interests,
        ];

        if ( empty($interests) ) unset( $data['interests'] );

        // exists but has the correct status, then only updates the data

        if ( $exists && !$needToReSubscribe ) {
            $response = $MailChimp->patch("lists/$listId/members/$hash", $data);

            if( isset($response['title'] )) {
                throw new OPanda_SubscriptionException ( '[subscribe]: ' . $response['title'] );
            }

            return array('status' => 'subscribed');
        }

        // exists but was removed or unsubscribed, then sets the pending status
        // or does not exist

        $data['status'] = ( $verified ? true : !$doubleOptin ) ? 'subscribed' : 'pending';
        $data['send_welcome'] = $verified ? $sendWelcomeMessage : (!$doubleOptin ? $sendWelcomeMessage : false);

        if ( $needToReSubscribe ) {
            $response = $MailChimp->patch("lists/$listId/members/$hash", $data);
        } else {
            $response = $MailChimp->post("lists/$listId/members", $data);
        }

        // if an error occurred
        if ( isset( $response['title'] ) && isset( $response['status'] ) ) {

            // if it is not an error 'Member Exists'
            if ( strpos( 'Member Exists', $response['title'] ) === false ) {
                throw new OPanda_SubscriptionException ( '[subscribe]: ' . $response['title'] . '. ' . ( isset( $response['detail'] ) ? $response['detail'] : '' ) );
            }
        }

        return array('status' => (!$verified && $doubleOptin) ? 'pending' : 'subscribed');
    }
    
    /**
     * Checks if the user subscribed.
     */  
    public function check( $identityData, $listId, $contextData ) { 
        
        $MailChimp = $this->initMailChimpLibs();
        $response = $MailChimp->call('/lists/member-info', array( 
                       'id' => $listId,
                       'emails' => array( 
                           array('email' => $identityData['email'])           
                       )
                    ));

        if( !sizeof($response) || !isset($response['data'][0]['status']) ) {
            print_r($response);
            throw new OPanda_SubscriptionException('[check]: Unexpected error occurred.');
        }
         
        return array('status' => $response['data'][0]['status']);
    }
    
    /**
     * Prepares values enters by the user to save.
     */
    public function prepareFieldValueToSave( $mapOptions, $value ) {
        if ( empty( $value ) ) return $value;
        
        $fieldType = $mapOptions['service']['field_type'];

        if ( $fieldType == 'birthday' ) {
            
            $dateformat = strtolower( $mapOptions['service']['dateformat'] );
            $parts = explode('/', $value);
            
            if ( $dateformat === 'dd/mm' ) {
                return $parts[1] . '/' . $parts[0];
            } else {
                return $parts[0] . '/' . $parts[1];
            }
            
        } elseif ( $fieldType == 'phone' ) {

            $phoneformat = strtolower( $mapOptions['service']['phoneformat'] );
            if ( $phoneformat === 'us' ) {
                
                if ( preg_match('/\((\d\d\d)\)\s(\d\d\d)\-(\d\d\d\d)/', $value, $matches ) ) {
                    return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                } else {
                    return $value;
                }
                
            } else {
                return $value;
            }
            
        }

        return $value;
    }
     
    /**
     * Returns custom fields.
     */
    public function getCustomFields( $listId ) {
        
        $MailChimp = $this->initMailChimpLibs();
        $response = $MailChimp->call('lists/merge-vars', array(
            "id" => array( $listId )
        ));

        if( isset($response['error_count']) && $response['error_count'] > 0 )
            throw new OPanda_SubscriptionException ( sprintf( __( 'Error: %s. Please try to refresh this page or update your <a href="%s" target="_blank">subscription settings</a>.' ), $response['errors'][0]['error'], opanda_get_settings_url('social') ) );  
        
        if ( !isset($response['data'][0]['merge_vars']) ) return array();
        
        $customFields = array();
        $mappingRules = array(
            'radio' => 'dropdown',
            'text' => array('text', 'checkbox', 'hidden'),
            'number' => array('integer', 'checkbox')
        );

        foreach( $response['data'][0]['merge_vars'] as $mergeVars ) {
            $fieldType = $mergeVars['field_type'];
                    
            $pluginFieldType = isset( $mappingRules[$fieldType] ) 
                    ? $mappingRules[$fieldType] 
                    : strtolower( $fieldType );
            
            if ( in_array($pluginFieldType, array('email'))) continue;            
            
            $can = array(
                'changeType' => true,
                'changeReq' => false,
                'changeDropdown' => false,
                'changeMask' => true
            );
            
            $fieldOptions = array();
            if ( 'dropdown' === $pluginFieldType ) {
                
                foreach ( $mergeVars['choices'] as $choice ) {
                    $fieldOptions['choices'][] = $choice;
                }
                
            } else if ( 'birthday' === $pluginFieldType ) {
                
                $fieldOptions['mask'] = '99/99';
                $fieldOptions['maskPlaceholder'] = strtolower( $mergeVars['dateformat'] );
                $can['changeMask'] = false;
                
            } else if ( 'phone' === $pluginFieldType ) {
                
                if ( 'US' === $mergeVars['phoneformat'] ) {

                    $fieldOptions['mask'] = '(999) 999-9999';
                    $fieldOptions['maskPlaceholder'] = '(___) ___-____';
                    $can['changeMask'] = false;
                }
            }
            
            $fieldOptions['req'] = $mergeVars['req'];

            $customFields[] = array(
                
                'fieldOptions' => $fieldOptions,
                
                'mapOptions' => array(
                    'req' => $mergeVars['req'],
                    'id' => $mergeVars['tag'],
                    'name' => $mergeVars['tag'],
                    'title' => sprintf('%s [%s]', $mergeVars['name'], $mergeVars['tag'] ),
                    'labelTitle' => $mergeVars['name'],
                    'mapTo' => is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                    'service' => $mergeVars
                ),
                
                'premissions' => array(
                    
                    'can' => $can,
                    'notices' => array(
                        'changeReq' => __('You can change this checkbox in your MailChimp account.', 'bizpanda'),
                        'changeDropdown' => sprintf( __('Please visit your MailChimp account to modify the choices. <a href="%s" target="_blank">Learn more</a>.', 'bizpanda'), "http://kb.mailchimp.com/merge-tags/using/getting-started-with-merge-tags#List-merge-tags" )
                    ), 
                )
            );
        }

        return $customFields;
    }
    
    public function getNameFieldIds() {
        return array( 'FNAME' => 'name', 'LNAME' => 'family' );
    }
}
