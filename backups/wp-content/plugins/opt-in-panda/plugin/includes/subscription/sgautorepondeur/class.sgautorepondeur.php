<?php 

class OPanda_SGAutorepondeurSubscriptionService extends OPanda_Subscription {

    public function init( $options ) {
        parent::init( $options );
    }
    
    /**
     * Makes a request to Acumbamail.
     * 
     * @since 1.0.9 
     */
    public function request( $method, $args = array(), $requestMethod = 'GET' ) {

        $apiKey = get_option('opanda_sg_apikey', false);
        $memberId = trim( get_option('opanda_sg_memberid', false) );

        if ( empty( $apiKey ) )
            throw new OPanda_SubscriptionException( __( 'The Sendy API Key is not specified.', 'optinpanda' ) );
        
        if ( empty( $sendyUrl ) )
            throw new OPanda_SubscriptionException( __( 'The Sendy Installation is not specified.', 'optinpanda' ) );
        
        $sendyUrl = trim($sendyUrl, '/');
        if ( false === strpos($sendyUrl, 'http://') ) $sendyUrl = 'http://' . $sendyUrl;
        
        $url = $sendyUrl . $method;
        $args['api_key'] = $apiKey;       

        $result = wp_remote_post( $url, array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Content-Length' => strlen( $data )
            ),
            'body' => $args
        ));

        if (is_wp_error( $result )) {
            throw new OPanda_SubscriptionException( sprintf( __( 'Unexpected error occurred during connection to Sendy: %s', 'optinpanda' ), $result->get_error_message() ) );
        }

        $code = isset( $result['response']['code'] ) ? intval ( $result['response']['code'] ) : 0;
        if ( 200 !== $code ) {
            throw new OPanda_SubscriptionException( sprintf( __( 'Unexpected error occurred during connection to Sendy: %s', 'optinpanda' ), $result['response']['message'] ) );   
        }
        
        if ( empty( $result['body'] ) ) return false;
        return $result['body'];
    }
    
    /**
     * Returns lists available to subscribe.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getLists() {
        return array();
    }

    /**
     * Subscribes the person.
     */
    public function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified ) {
        
        $vars = $this->refine( $identityData );
        $email = $identityData['email'];

        $apiKey = get_option('opanda_sg_apikey', false);
        $memberId = trim( get_option('opanda_sg_memberid', false) );

        if ( empty( $apiKey ) )
            throw new OPanda_SubscriptionException( __( 'The Activation Code of SG Autorepondeur is not specified.', 'optinpanda' ) );
        
        if ( empty( $memberId ) )
            throw new OPanda_SubscriptionException( __( 'The Member Id of SG Autorepondeur is not specified.', 'optinpanda' ) );
        
        if ( empty( $listId ) )
            throw new OPanda_SubscriptionException( __( 'The List Id of SG Autorepondeur is not specified.', 'optinpanda' ) );

	// FROM THIS LINE, DO NOT CHANGE ANYTHING
	$values = array(
		 'membreid'=> $memberId
		,'codeactivationclient' => $apiKey
		,'inscription_normale' => "non"
		,'listeid'=> $listId
		,'email'=> $email
		,'nom'=> null
		,'prenom'=> null
		,'civilite'=>null
		,'adresse'=>null
		,'codepostal'=>null
		,'ville'=>null
		,'pays'=>null
		,'siteweb'=>null
		,'telephone'=>null
		,'parrain'=>null
		,'fax'=>null
		,'msn'=>null
		,'skype'=>null
		,'pseudo'=>null
		,'sexe'=>null
		,'journaissance'=>null
		,'moisnaissance'=>null
		,'anneenaissance'=>null
		,'ip'=> OPanda_Leads::getIP()
		,'identite'=>null
		,'champs_1'=>null
		,'champs_2'=>null
		,'champs_3'=>null
		,'champs_4'=>null
		,'champs_5'=>null
		,'champs_6'=>null
		,'champs_7'=>null
		,'champs_8'=>null
		,'champs_9'=>null
		,'champs_10'=>null
		,'champs_11'=>null
		,'champs_12'=>null
		,'champs_13'=>null
		,'champs_14'=>null
		,'champs_15'=>null
		,'champs_16'=>null);
        
        $values = array_merge($values, $identityData);

        if ( !empty( $identityData['name'] ) ) $values['nom'] = $identityData['name'];
        if ( !empty( $identityData['family'] ) ) $values['prenom'] = $identityData['family'];
        if ( empty( $values['nom'] ) && !empty( $identityData['displayName'] ) ) $values['nom'] = $identityData['displayName'];
        
        $parts = explode('@', $email);
        
        if ( empty( $values['nom'] ) ) $values['nom'] = $parts[0];
        if ( empty( $values['prenom'] ) ) $values['prenom'] = $parts[0]; 

        $result = wp_remote_post( 'http://sg-autorepondeur.com/inscr_decrypt.php', array(
            'timeout' => 10,
            'body' => $values
        ));
        
        if (is_wp_error( $result )) {
            throw new OPanda_SubscriptionException( sprintf( __( 'Unexpected error occurred during connection to SG Autorepondeur: %s', 'optinpanda' ), $result->get_error_message() ) );
        }
        
	$response = trim($result['body']);

        if ( $response == 'informationmanquante' ) {
            throw new OPanda_SubscriptionException( __("The data passed incorrect or list ID not found."));
        }

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

        $customFields = array();

        $fields = array(
            array('name' => 'civilite', 'title' => 'Civilite'),
            array('name' => 'adresse', 'title' => 'Adresse'),
            array('name' => 'codepostal', 'title' => 'Code Postal'),
            array('name' => 'ville', 'title' => 'Ville'),
            array('name' => 'pays', 'title' => 'Pays'),
            array('name' => 'siteweb', 'title' => 'Siteweb'),
            array('name' => 'telephone', 'title' => 'Téléphone'),
            array('name' => 'parrain', 'title' => 'Parrain'),
            array('name' => 'fax', 'title' => 'Fax'),
            array('name' => 'msn', 'title' => 'MSN'),
            array('name' => 'skype', 'title' => 'Skype'),
            array('name' => 'pseudo', 'title' => 'Pseudo'),
            array('name' => 'sexe', 'title' => 'Sexe'),
            array('name' => 'journaissance', 'title' => 'Jour Naissance'),
            array('name' => 'moisnaissance', 'title' => 'Mois Naissance'),
            array('name' => 'anneenaissance', 'title' => 'Anne Naissance'),
            array('name' => 'identite', 'title' => 'Pièce d\'identité'),
            array('name' => 'champs_1', 'title' => 'Champ personnalisé 1'),
            array('name' => 'champs_2', 'title' => 'Champ personnalisé 2'),
            array('name' => 'champs_3', 'title' => 'Champ personnalisé 3'),
            array('name' => 'champs_4', 'title' => 'Champ personnalisé 4'),
            array('name' => 'champs_5', 'title' => 'Champ personnalisé 5'),
            array('name' => 'champs_6', 'title' => 'Champ personnalisé 6'),
            array('name' => 'champs_7', 'title' => 'Champ personnalisé 7'),
            array('name' => 'champs_8', 'title' => 'Champ personnalisé 8'),
            array('name' => 'champs_9', 'title' => 'Champ personnalisé 9'),
            array('name' => 'champs_10', 'title' => 'Champ personnalisé 10'),
            array('name' => 'champs_11', 'title' => 'Champ personnalisé 11'),
            array('name' => 'champs_12', 'title' => 'Champ personnalisé 12'),
            array('name' => 'champs_13', 'title' => 'Champ personnalisé 13'),
            array('name' => 'champs_14', 'title' => 'Champ personnalisé 14'),
            array('name' => 'champs_15', 'title' => 'Champ personnalisé 15'),
            array('name' => 'champs_16', 'title' => 'Champ personnalisé 16')
        );

        foreach( $fields as $field ) {
            $fieldType = array('text', 'dropdown', 'integer', 'hidden', 'date', 'checkbox', 'url');

            $can = array(
                'changeType' => true,
                'changeReq' => true,
                'changeDropdown' => true,
                'changeMask' => true
            );
            
            $fieldOptions = array();
            $fieldOptions['req'] = false;

            $customFields[] = array(
                
                'fieldOptions' => $fieldOptions,
                
                'mapOptions' => array(
                    'req' => false,
                    'id' => $field['name'],
                    'name' => $field['name'],
                    'title' => $field['title'],
                    'labelTitle' => $field['title'],
                    'mapTo' => $fieldType,
                    'service' => $field
                ),
                
                'premissions' => array(
                    'can' => $can
                )
            );
        }

        return $customFields;
    }
}
