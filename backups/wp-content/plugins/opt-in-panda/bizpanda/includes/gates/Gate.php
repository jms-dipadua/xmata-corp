<?php

namespace bizpanda\includes\gates;

use bizpanda\includes\gates\context\ConfigService;
use bizpanda\includes\gates\context\IContextReader;
use bizpanda\includes\gates\context\IContextReaderWriter;
use bizpanda\includes\gates\context\RequestService;
use bizpanda\includes\gates\context\SessionService;
use bizpanda\includes\gates\exceptions\GateException;
use yii\base\BaseObject;

/**
 * The base class for all handlers of requests to the proxy.
 */
class Gate {

    /**
     * A name of this gate (usually it's a service name: facebook, twitter etc).
     * @var array
     */
    protected $name = null;

    /**
     * Current visitor ID (used as a Session ID to store data across requests).
     * @var string
     */
    protected $visitorId = null;

    /**
     * Configuration Manager.
     * @var IContextReader
     */
    protected $config = null;

    /**
     * Request Manager.
     * @var IContextReader
     */
    protected $request = null;

    /**
     * Session Manager.
     * @var IContextReaderWriter
     */
    protected $session = null;

    /**
     * Gate constructor.
     * @param $session IContextReaderWriter
     * @param $config IContextReader
     * @param $request IContextReader
     */
    public function __construct( $session = null, $config = null, $request = null ) {

        $this->config = $config;
        $this->request = $request;
        $this->session = $session;

        if ( empty( $this->config ) ) $this->config = new ConfigService();
        if ( empty( $this->request ) ) $this->request = new RequestService();
        if ( empty( $this->session ) ) $this->session = new SessionService();

        $this->visitorId = $this->getRequestParam('visitorId');
        if ( empty( $this->visitorId ) ) $this->visitorId = $this->getGuid();

        $this->session->init( $this->visitorId );
    }

    // ----------------------------------------------------------------------
    // Helper Methods
    // ----------------------------------------------------------------------

    /**
     * Returns a param from the $_REQUEST array.
     * @param $name string A key of the param to return.
     * @param $default mixed A value to return if the param doesn't exist.
     * @return mixed A value to return.
     */
    protected function getRequestParam($name, $default = null ) {
        return $this->request->get( $name, $default );
    }

    /**
     * Saves the value to the session storage.
     * @param $name string A key to save value to the session.
     * @param $value mixed A value to save.
     */
    protected function setVisitorValue($name, $value ) {
        $this->session->set( $name, $value );
    }

    /**
     * Gets the value from the session storage.
     * @param $name string A key to get value from the session.
     * @param $default mixed A default value to return if the session doesn't contain any value.
     * @return mixed A value from the session.
     */
    protected function getVisitorValue( $name, $default = null ) {
        return $this->session->get( $name, $default );
    }

    /**
     * Converts a set of values to the respective native values.
     * @param $values mixed[] A set of values to convert.
     * @return mixed[] Normalized values.
     */
    protected function normalizeValues($values = array() ) {
        if ( empty( $values) ) return $values;
        if ( !is_array( $values ) ) $values = array( $values );

        foreach ( $values as $index => $value ) {

            $values[$index] = is_array( $value )
                ? $this->normalizeValues( $value )
                : $this->normalizeValue( $value );
        }

        return $values;
    }

    /**
     * Converts string values to the respective native value.
     * @param $value mixed Value to convert.
     * @return mixed A normalized value.
     */
    protected function normalizeValue($value = null ) {

        if ( 'false' === $value ) $value = false;
        elseif ( 'true' === $value ) $value = true;
        elseif ( 'null' === $value ) $value = null;
        return $value;
    }

    /**
     * Generates a display name using the identity data.
     * @param $identity mixed[] A user identity.
     * @return string A display name.
     */
    protected function buildDisplayName( $identity ) {

        if ( !empty( $identity['displayName'] ) ) return $identity['displayName'];

        $displayName = !empty( $identity['name'] ) ? $identity['name'] : '';
        if ( !empty( $displayName ) && !empty( $identity['family'] )) $displayName .= ' ';

        $displayName .= !empty( $identity['family'] ) ? $identity['family'] : '';

        return $displayName;
    }


    /**
     * Generates a GUID.
     * @return string A guid.
     */
    protected function getGuid() {

        if (function_exists('com_create_guid') === true) {
            $guid = trim(com_create_guid(), '{}');
        } else {

            $guid = sprintf(
                '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479),
                mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
            );
        }

        return strtolower( str_replace('-', '', $guid) );
    }

    /**
     * Converts a given string to a camel case string.
     * See: https://stackoverflow.com/questions/2791998/convert-dashes-to-camelcase-in-php
     * @param $string string A string to convert.
     * @param bool $capitalizeFirstCharacter If true, the first letter will be capitalized.
     * @param string $separator A string separator to find words.
     * @return string|string[] Camel case string.
     */
    protected function toCamelCase($string, $capitalizeFirstCharacter = false, $separator = '_') {

        $str = str_replace($separator, '', ucwords($string, $separator));
        if (!$capitalizeFirstCharacter) $str = lcfirst($str);
        return $str;
    }
}