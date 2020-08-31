<?php 

class OPanda_MailPoetSubscriptionService extends OPanda_Subscription {

    protected $_version = 0;
    
    public function __construct( $info ) {
        parent::__construct( $info );
        
        $this->_version = 0;
        if ( defined('WYSIJA') ) $this->_version = 2;
        if ( defined('MAILPOET_VERSION') ) $this->_version = 3;
    }

    /**
     * Returns lists available to subscribe.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getLists() {
        
        if ( !$this->_version ) {
            throw new OPanda_SubscriptionException( __( 'The MailPoet plugin is not found on your website.', 'optinpanda' ) ); 
        }
        
        $lists = array();
        
        if ( $this->_version == 3 ) {

            $segments = \MailPoet\Models\Segment::getSegmentsWithSubscriberCount();
            foreach( $segments as $item ) {
                $lists[] = array(
                    'title' => $item['name'],
                    'value' => $item['id']
                );
            }  
            
        } else {
            
            $model_list = WYSIJA::get('list','model');
            foreach( $model_list->getLists() as $item ) {
                $lists[] = array(
                    'title' => $item['name'],
                    'value' => $item['list_id']
                );
            }  
        }

        return array(
            'items' => $lists
        ); 
    }

    /**
     * Subscribes the person.
     */
    public function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified ) {

        if ( !$this->_version ) {
            throw new OPanda_SubscriptionException( __( 'The MailPoet plugin is not found on your website.', 'optinpanda' ) ); 
        }

        if ( $this->_version == 3 ) {
            
            $subscriber = \MailPoet\Models\Subscriber::findOne( $identityData['email'] );
            
        } else {
            
            $userModel = WYSIJA::get('user','model');
            $userListModel = WYSIJA::get('user_list','model');
            $manager = WYSIJA::get('user','helper');   

            $subscriber = $userModel->getOne(false, array('email' => $identityData['email'] ));
        
        }

        $customs = $this->refine( $identityData );

        if ( empty( $customs['firstname'] ) && !empty( $identityData['name'] ) ) $customs['firstname'] = $identityData['name'];
        if ( empty( $customs['lastname'] ) && !empty( $identityData['family'] ) ) $customs['lastname'] = $identityData['family'];
        if ( empty( $customs['firstname'] ) && !empty( $identityData['displayName'] ) ) $customs['firstname'] = $identityData['displayName'];

        // ---
        // if already subscribed
        
        if ( !empty( $subscriber ) ) {

            if ( $this->_version == 3 ) {
                $subscriberId = intval( $subscriber->id );
                
                // adding the user to the specified list if the user has not been yet added

                $lists = $subscriber->segments()->findArray();
                
                $added = false;
                foreach( $lists as $list ) {
                    if ( $list['id'] == $listId ) $added = true;
                }
                
                if ( !$added ) {
                    \MailPoet\Models\SubscriberSegment::subscribeToSegments($subscriber, array( $listId ) );
                }
                
                if ( isset( $customs['firstname'] ) ) $subscriber->first_name = trim( $customs['firstname'] );
                if ( isset( $customs['lastname'] ) ) $subscriber->last_name = trim( $customs['lastname'] );

                unset( $customs['firstname'] );
                unset( $customs['lastname'] );
                    
                $data = $subscriber->asArray();
                
                $temp = array();
                foreach( $customs as $customName => $customValue ) {
                    if (in_array( $customName, array('first_name', 'last_name', 'firstname', 'lastname', 'email') )) continue;
                    $temp['cf_' . $customName] = $customValue;
                } 

                $data = array_merge( $data, $temp );
                \MailPoet\Models\Subscriber::createOrUpdate( $data );
                
                if ( !$doubleOptin ) return array('status' =>  'subscribed');
                
                // sends the confirmation email

                $status = $subscriber->get('status');
                if ( \MailPoet\Models\Subscriber::STATUS_UNCONFIRMED === $status ) $subscriber->sendConfirmationEmail();

                return array('status' => \MailPoet\Models\Subscriber::STATUS_SUBSCRIBED  === $status ? 'subscribed' : 'pending');
                
            } else {
                $subscriberId = intval( $subscriber['user_id'] );
                
                // adding the user to the specified list if the user has not been yet added

                $lists = $userListModel->get_lists( array( $subscriberId ) );

                if ( !isset( $lists[$subscriberId] ) || !in_array( $listId, $lists[$subscriberId] ) ) {
                    $manager->addToList( $listId, array( $subscriberId ) );
                }

                if ( isset( $customs['firstname'] ) || isset( $customs['lastname'] ) ) {

                    $modelUser = WYSIJA::get('user', 'model');

                    if ( isset( $customs['firstname'] ) ) $data['firstname'] = trim( $customs['firstname'] );
                    if ( isset( $customs['lastname'] ) ) $data['lastname'] = trim( $customs['lastname'] );

                    if ( empty( $customs['firstname'] ) ) $customs['firstname'] = $subscriber['firstname'];
                    if ( empty( $customs['lastname'] ) ) $customs['lastname'] = $subscriber['lastname'];

                    $modelUser->update($customs, array( 'user_id' => $subscriberId ));
                    WJ_FieldHandler::handle_all( $customs, $subscriberId );
                } 

                if ( !$doubleOptin ) return array('status' =>  'subscribed');

                // sends the confirmation email

                $status = intval($subscriber['status'] );
                if ( 0 === $status ) $manager->sendConfirmationEmail( $subscriberId, true, array( $listId ) );

                return array('status' => 1 === $status ? 'subscribed' : 'pending');
            }
        }
        
        // ---
        // if it's a new subscriber
        
        if ( $this->_version == 3 ) {
            
            $userData = array(
                'email' => $identityData['email'],
                'status' => $verified 
                                ? \MailPoet\Models\Subscriber::STATUS_SUBSCRIBED 
                                : (!$doubleOptin ? \MailPoet\Models\Subscriber::STATUS_SUBSCRIBED : \MailPoet\Models\Subscriber::STATUS_UNCONFIRMED),
                'created_at' => time()
            );
            
            if ( !empty( $identityData['name'] ) )
                $userData['first_name'] = $identityData['name'];

            if ( !empty( $identityData['family'] ) )
                $userData['last_name'] = $identityData['family'];

            if ( empty( $identityData['name'] ) && empty( $identityData['family'] ) && !empty( $identityData['displayName'] ) )
                $userData['first_name'] = $identityData['displayName'];
            
            $temp = array();
            foreach( $customs as $customName => $customValue ) {
                if (in_array( $customName, array('first_name', 'last_name', 'firstname', 'lastname', 'email') )) continue;
                $temp['cf_' . $customName] = $customValue;
            } 

            $userData = array_merge( $userData, $temp );
            $subscriber = \MailPoet\Models\Subscriber::createOrUpdate( $userData );

            if ( !$subscriber ) {
                throw new OPanda_SubscriptionException ( '[subscribe]: Unable to add a subscriber.' ); 
            }
            
            \MailPoet\Models\SubscriberSegment::subscribeToSegments( $subscriber, array( $listId ) );
            
            if ( !$verified && $doubleOptin ) $subscriber->sendConfirmationEmail();
            return array('status' => $verified ? 'subscribed' : ( $doubleOptin ? 'pending' : 'subscribed' ));  
            
        } else {

            $ip = $manager->getIP();

            $userData = array(
                'email' => $identityData['email'],
                'status' => $verified ? 1 : (!$doubleOptin ? 1 : 0),
                'ip' => $ip,
                'created_at' => time()
            );

            if ( !empty( $identityData['name'] ) )
                $userData['firstname'] = $identityData['name'];

            if ( !empty( $identityData['family'] ) )
                $userData['lastname'] = $identityData['family'];

            if ( empty( $identityData['name'] ) && empty( $identityData['family'] ) && !empty( $identityData['displayName'] ) )
                $userData['firstname'] = $identityData['displayName'];

            $subscriberId = $userModel->insert( $userData );

            // adds custom fields
            WJ_FieldHandler::handle_all( $customs, $subscriberId );

            if ( !$subscriberId ) {
                throw new OPanda_SubscriptionException ( '[subscribe]: Unable to add a subscriber.' ); 
            }

            // adds the user the the specified list

            $manager->addToList( $listId, array( $subscriberId ) );

            // sends the confirmation email

            if ( !$verified && $doubleOptin ) $manager->sendConfirmationEmail( $subscriberId, true, array( $listId ) );
            return array('status' => $verified ? 'subscribed' : ( $doubleOptin ? 'pending' : 'subscribed' ));   

        }
    }
    
    /**
     * Checks if the user subscribed.
     */  
    public function check( $identityData, $listId, $contextData ) { 
        
        if ( $this->_version == 3 ) {
            
            $subscriber = \MailPoet\Models\Subscriber::findOne( $identityData['email'] );
            if ( empty( $subscriber ) ) {
                throw new OPanda_SubscriptionException( __( 'The operation is canceled because the website administrator deleted your email from the list.', 'optinpanda' ) ); 
            }

            $subscriberId = intval( $subscriber->id );

            // adding the user to the specified list if the user has not been yet added

            $lists = $subscriber->segments()->findArray();

            $added = false;
            foreach( $lists as $list ) {
                if ( $list['id'] == $listId ) $added = true;
            }

            if ( !$added ) {
                \MailPoet\Models\SubscriberSegment::subscribeToSegments($subscriber, array( $listId ) );
            }

            $status = $subscriber->get('status');
            return array('status' => \MailPoet\Models\Subscriber::STATUS_SUBSCRIBED  === $status ? 'subscribed' : 'pending');
        
        } else {

            $userModel = WYSIJA::get('user','model');
            $userListModel = WYSIJA::get('user_list','model');
            $manager = WYSIJA::get('user','helper');   

            $subscriber = $userModel->getOne(false, array('email' => $identityData['email'] ));
            if ( empty( $subscriber ) ) {
                throw new OPanda_SubscriptionException( __( 'The operation is canceled because the website administrator deleted your email from the list.', 'optinpanda' ) ); 
            }

            $subscriberId = intval( $subscriber['user_id'] );

            // adding the user to the specified list if the user has not been yet added

            $lists = $userListModel->get_lists( array( $subscriberId ) );
            if ( !isset( $lists[$subscriberId] ) || !in_array( $listId, $lists[$subscriberId] ) ) {
                $manager->addToList( $listId, array( $subscriberId ) );
            }

            $status = intval( $subscriber['status'] );

            if ( 1 === $status ) return array('status' => 'subscribed');
            return array('status' => 'pending');
            
        }
    }
    
    /**
     * Returns custom fields.
     */
    public function getCustomFields( $listId ) {

        if ( $this->_version == 3 ) {

            $result = \MailPoet\Models\CustomField::orderByAsc('created_at')->findMany();
            
            $mappingRules = array(
                'textarea' => 'text',
                'radio' => 'dropdown',
                'select' => 'dropdown'
            );
            
            $customFields = array();
            foreach ($result as $cf ) {
                
                $field = $cf->asArray();
                $fieldType = $field['type'];
                
                $pluginFieldType = isset( $mappingRules[$fieldType] ) 
                        ? $mappingRules[$fieldType] 
                        : strtolower( $fieldType );
                
                $fieldOptions = array(
                    'req' => isset( $field['params']['required'] ) ? ( ( $field['params']['required'] ) ? true : false ) : false
                );

                $changeMask = true;

                if ( 'text' === $pluginFieldType ) {

                    if ( isset( $field['params']['validate'] ) && !empty( $field['params']['validate'] ) ) {

                        if ( 'number' == $field['params']['validate'] ) {
                            $pluginFieldType = 'integer';
                        } elseif ( 'alphanum' == $field['params']['validate'] ) {
                            $fieldOptions['validation'] = '/^[0-9a-z]+$/i';
                        } elseif ( 'phone' == $field['params']['validate'] ) {
                            $pluginFieldType = 'phone';
                            $fieldOptions['validation'] = '/^[\s\+\-\#\(\)\d]+$/';
                        }
                    }

                } elseif ( 'date' === $pluginFieldType  ) {

                    if ( isset( $field['params']['date_type'] ) && !empty( $field['params']['date_type'] ) ) {

                        if ( 'year_month_day' == $field['params']['date_type'] ) {

                            $pluginFieldType = 'text';
                            $fieldOptions['mask'] = '99/99/9999';
                            $fieldOptions['validation'] = 'month/day/year';
                            $changeMask = false;              
                        
                        } elseif ( 'year_month' == $field['params']['date_type'] ) {

                            $pluginFieldType = 'text';
                            $fieldOptions['mask'] = '99/9999';
                            $fieldOptions['validation'] = 'month/year';
                            $changeMask = false;

                        } elseif ( 'month' == $field['params']['date_type'] ) {

                            $pluginFieldType = 'text';
                            $fieldOptions['mask'] = '99';
                            $fieldOptions['validation'] = 'month';
                            $changeMask = false;

                        } elseif ( 'year' == $field['params']['date_type'] ) {

                            $pluginFieldType = 'text';
                            $fieldOptions['mask'] = '9999';
                            $fieldOptions['validation'] = 'year';
                            $changeMask = false;

                        }
                    }

                } elseif ( 'dropdown' === $pluginFieldType  ) {

                    if ( isset( $field['params']['values'] ) && !empty( $field['params']['values'] ) ) {

                        foreach ( $field['params']['values'] as $choice ) {
                            $fieldOptions['choices'][] = $choice['value'];
                        }
                    }

                } elseif ( 'checkbox' === $pluginFieldType  ) {

                    if ( isset( $field['params']['values'] ) && !empty( $field['params']['values'] ) ) {

                        if ( !empty( $field['params']['values'][0]['value'] ) ) {
                            $fieldOptions['onValue'] = $field['params']['values'][0]['value'];
                            $fieldOptions['offValue'] = '';
                        }

                        if ( $field['params']['values'][0]['is_checked'] ) {
                            $fieldOptions['markedByDefault'] = $field['params']['values'][0]['is_checked'];
                        }
                    }

                }

                if ( in_array($pluginFieldType, array('html', 'list', 'divider'))) continue;     

                $can = array(
                    'changeType' => true,
                    'changeReq' => false,
                    'changeDropdown' => false,
                    'changeMask' => $changeMask
                );

                $customFields[] = array(

                    'fieldOptions' => $fieldOptions,

                    'mapOptions' => array(
                        'req' => isset( $field['params']['required'] ) ? ( ( $field['params']['required'] ) ? true : false ) : false,
                        'id' => $field['id'],
                        'name' => 'cf_' . $field['id'],
                        'title' => $field['name'],
                        'labelTitle' => $field['name'],
                        'mapTo' => is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                        'service' => $field
                    ),

                    'premissions' => array(

                        'can' => $can,
                        'notices' => array(
                            'changeReq' => __('You can change this checkbox in the settings of your MailPoet forms.', 'bizpanda'),
                            'changeDropdown' => __('Please visit the form editor in MailPoet to modify the choices.' )
                        ), 
                    )
                );
            }

            return $customFields;
            
        } else {
            

            $mappingRules = array(
                'input' => 'text',
                'textarea' => 'text',
                'radio' => 'dropdown',
                'select' => 'dropdown',            

            );

            $manager = WYSIJA::get('form_engine','helper');
            $result = $manager->get_custom_fields();

            $customFields = array();
            foreach ($result as $field ) {
                $fieldType = $field['column_type'];

                $pluginFieldType = isset( $mappingRules[$fieldType] ) 
                        ? $mappingRules[$fieldType] 
                        : strtolower( $fieldType );

                $fieldOptions = array(
                    'req' => isset( $field['params']['required'] ) ? ( ( $field['params']['required'] ) ? true : false ) : false
                );

                $changeMask = true;

                if ( 'text' === $pluginFieldType ) {

                    if ( isset( $field['params']['validate'] ) && !empty( $field['params']['validate'] ) ) {

                        if ( 'onlyNumberSp' == $field['params']['validate'] ) {
                            $pluginFieldType = 'integer';
                        } elseif ( 'onlyLetterSp' == $field['params']['validate'] ) {
                            $fieldOptions['validation'] = '/^[a-z]+$/i';
                        } elseif ( 'onlyLetterNumber' == $field['params']['validate'] ) {
                            $fieldOptions['validation'] = '/^[0-9a-z]+$/i';
                        } elseif ( 'phone' == $field['params']['validate'] ) {
                            $pluginFieldType = 'phone';
                            $fieldOptions['validation'] = '/^[\s\+\-\#\(\)\d]+$/';
                        }
                    }

                } elseif ( 'date' === $pluginFieldType  ) {

                    if ( isset( $field['params']['date_type'] ) && !empty( $field['params']['date_type'] ) ) {

                        if ( 'year_month' == $field['params']['date_type'] ) {

                            $pluginFieldType = 'text';
                            $fieldOptions['mask'] = '99/9999';
                            $fieldOptions['validation'] = 'month/year';
                            $changeMask = false;

                        } elseif ( 'month' == $field['params']['date_type'] ) {

                            $pluginFieldType = 'text';
                            $fieldOptions['mask'] = '99';
                            $fieldOptions['validation'] = 'month';
                            $changeMask = false;

                        } elseif ( 'year' == $field['params']['date_type'] ) {

                            $pluginFieldType = 'text';
                            $fieldOptions['mask'] = '9999';
                            $fieldOptions['validation'] = 'year';
                            $changeMask = false;

                        }
                    }

                } elseif ( 'dropdown' === $pluginFieldType  ) {

                    if ( isset( $field['params']['values'] ) && !empty( $field['params']['values'] ) ) {

                        foreach ( $field['params']['values'] as $choice ) {
                            $fieldOptions['choices'][] = $choice['value'];
                        }
                    }

                } elseif ( 'checkbox' === $pluginFieldType  ) {

                    if ( isset( $field['params']['values'] ) && !empty( $field['params']['values'] ) ) {

                        if ( !empty( $field['params']['values'][0]['value'] ) ) {
                            $fieldOptions['onValue'] = $field['params']['values'][0]['value'];
                            $fieldOptions['offValue'] = '';
                        }

                        if ( $field['params']['values'][0]['is_checked'] ) {
                            $fieldOptions['markedByDefault'] = $field['params']['values'][0]['is_checked'];
                        }
                    }

                }

                if ( in_array($pluginFieldType, array('html', 'list', 'divider'))) continue;     

                $can = array(
                    'changeType' => true,
                    'changeReq' => false,
                    'changeDropdown' => false,
                    'changeMask' => $changeMask
                );

                $customFields[] = array(

                    'fieldOptions' => $fieldOptions,

                    'mapOptions' => array(
                        'req' => isset( $field['params']['required'] ) ? ( ( $field['params']['required'] ) ? true : false ) : false,
                        'id' => $field['column_name'],
                        'name' => $field['column_name'],
                        'title' => $field['name'],
                        'labelTitle' => $field['name'],
                        'mapTo' => is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                        'service' => $field
                    ),

                    'premissions' => array(

                        'can' => $can,
                        'notices' => array(
                            'changeReq' => __('You can change this checkbox in the settings of your MailPoet forms.', 'bizpanda'),
                            'changeDropdown' => __('Please visit the form editor in MailPoet to modify the choices.' )
                        ), 
                    )
                );
            }

            return $customFields;

        }
    }
    
    public function prepareFieldValueToSave( $mapOptions, $value ) {
        if ( empty( $value ) ) return $value;
        $fieldType = $mapOptions['service']['type'];

        if ( $fieldType == 'checkbox' ) {
            
            return ( !empty( $value ) ) ? 1 : 0;
            
        } else if ( $fieldType == 'date' ) {
            
            if ( 'year_month_day' == $mapOptions['service']['params']['date_type'] ) {
                
                $parts = explode('/', $value);
                return array(
                    'year' => $parts[2],
                    'day' => $parts[1], 
                    'month' => $parts[0]
                ); 
            
            } elseif ( 'year_month' == $mapOptions['service']['params']['date_type'] ) {
                
                $parts = explode('/', $value);
                return array(
                    'year' => $parts[1],
                    'month' => $parts[0],
                    'day' => 1
                );

            } elseif ( 'month' == $mapOptions['service']['params']['date_type'] ) {

                return array(
                    'year' => 1,
                    'month' => $value,
                    'day' => 1
                );

            } elseif ( 'year' == $mapOptions['service']['params']['date_type'] ) {

                return array(
                    'year' => $value,
                    'month' => 1,
                    'day' => 1
                );

            } else {
                
                $parts = explode('-', $value); 
                return array(
                    'year' => $parts[0],
                    'month' => $parts[1],
                    'day' => $parts[2]
                );
                
            }
        }
        
        return $value;
    }
}
