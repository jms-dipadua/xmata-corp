<?php

namespace bizpanda\includes\gates\lead;

use bizpanda\includes\gates\ActionGate;
use \bizpanda\includes\gates\exceptions\GateException;

/**
 * The class to proxy the request to the Twitter API.
 */
class LeadGate extends ActionGate {

    /**
     * Handles the proxy request.
     */
    public function handleRequest() {
        
        // - context data

        $contextData = $this->getRequestParam('contextData', []);
        $contextData = $this->normalizeValues( $contextData );
        
        // - idetity data

        $identityData = $this->getRequestParam('identityData', []);
        $identityData = $this->normalizeValues( $identityData );
        
        // prepares data received from custom fields to be transferred to the mailing service
        
        $identityData = $this->prepareDataToSave( null, null, $identityData );
        
        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/leads.php';
        \OPanda_Leads::add( $identityData, $contextData );
    }
}


