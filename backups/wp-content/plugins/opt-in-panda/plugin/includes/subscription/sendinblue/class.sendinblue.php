<?php 

class OPanda_SendinblueSubscriptionService extends OPanda_Subscription {
    
    public function request( $endPoint, $method = 'GET', $data = array() ) {
        
        $apiKey = get_option('opanda_sendinblue_apikey', false);
        if( empty( $apiKey )) throw new OPanda_SubscriptionException ('The API Key not set.');
        
        $isPost = $method == 'POST';
        $url = 'https://api.sendinblue.com/v2.0/' . $endPoint;

        $caller = $isPost ? 'wp_remote_post' : 'wp_remote_get';
        
        $args = array(
            'headers' => array(
                'api-key' => $apiKey,
                'Content-Type' => 'application/json'
            )
        );
        
        if ( !empty( $data) ) $args['body'] = json_encode($data);

        $result = $caller($url, $args);

        if (is_wp_error( $result )) {
            throw new OPanda_SubscriptionException( sprintf( __( 'Unexpected error occurred during connection to SendInBlue. %s', 'optinpanda' ), $result->get_error_message() ) );
        }

        if ( empty( $result['body'] ) ) return array();

        $data = json_decode($result['body']);
        if ($data === FALSE) {
            throw new OPanda_SubscriptionException( sprintf( __( 'Unexpected error occurred during connection to SendInBlue. %s', 'optinpanda' ), $result['body'] ) );  
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
        
        $result = $this->request('list', 'GET', array(
            'page' => 1,
            'page_limit' => 100
        ));

        if ( $result->code !== 'success' ) throw new OPanda_SubscriptionException( $result->message );

        $lists = array();
        
        if ( isset( $result->data->lists) ) {
            
            foreach( $result->data->lists as $value ) {
                $lists[] = array(
                    'title' => $value->name,
                    'value' => $value->id
                );
            }
        } elseif ( is_array( $result->data ) ) {
            
            foreach( $result->data as $value ) {
                $lists[] = array(
                    'title' => $value->name,
                    'value' => $value->id
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

        $email = $identityData['email'];
        $result = $this->request("user/$email", "GET");

        // user exists already
        
        if ( $result->code == 'success' ) {
            
            if ( !isset( $result->listid ) || empty( $result->listid ) ) $lists = array();
            else $lists = $result->listid;
                
            if ( !in_array($listId, $lists) ) $lists[] = $listId;

        // user doesn't exist yet
            
        } else {
            
            $lists[] = $listId;
            
        }

        unset($identityData['email']);
        $attrs = $identityData;        

        $result = $this->request("user/createdituser", "POST", array(
            'email' => $email,
            'attributes' => $attrs,
            'listid' => $lists
        ));
        
        if ( $result->code !== 'success' ) throw new OPanda_SubscriptionException( $result->message );

        return array('status' => 'subscribed');
    }
    
    /**
     * Checks if the user subscribed.
     */  
    public function check( $identityData, $listId, $contextData ) { 
        
        $email = $identityData['email'];
        $result = $this->request("user/$email", "GET");
        
        if ( $result->code !== 'success' ) throw new OPanda_SubscriptionException( $result->message );
        
        return array('status' => 'subscribed');
    }
    
    /**
     * Returns custom fields.
     */
    public function getCustomFields( $listId ) {
        
        return array(
            'error' => sprintf( __('Sorry, the plugin doesn\'t support custom fields for SendInBlue. Please <a href="%s" target="_blank">contact us</a> if you need this feature.', 'bizpanda'), "http://support.onepress-media.com/create-ticket/" )
        );
    }
}
