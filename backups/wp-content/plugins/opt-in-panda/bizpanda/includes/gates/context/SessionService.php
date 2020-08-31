<?php

namespace bizpanda\includes\gates\context;

/**
 * Manages the session.
 */
class SessionService implements IContextReaderWriter
{
    public $sessionId = null;

    /**
     * Inits the session.
     * @param $id string An ID of the session.
     */
    public function init( $id = null ) {
        $this->sessionId = $id;
    }

    /**
     * Saves a value into the session.
     * @param $name string A key of the value to save.
     * @param $value mixed A value to save.
     */
    public function set( $name, $value ) {

        if ( defined('W3TC') ) {
            setcookie( 'opanda_' . md5($name), $value, time() + 60 * 60 * 1 , COOKIEPATH, COOKIE_DOMAIN  );
        } else {
            set_transient( 'opanda_' . md5($this->sessionId . '_' . $name), $value, 60 * 60 * 1 );
        }
    }

    /**
     * Returns a value from the session.
     * @param $name string A key of the session value to return.
     * @param $default mixed A value to return if the param doesn't exist.
     * @return mixed A value from the session.
     */
    public function get( $name, $default = null ) {

        if ( defined('W3TC') ) {

            $cookieName = 'opanda_' . md5($name);
            $value = isset( $_COOKIE[$cookieName] ) ? $_COOKIE[$cookieName] : null;
            if ( empty( $value ) ) return $default;
            return $value;

        } else {

            $value = get_transient( 'opanda_' . md5($this->sessionId . '_' . $name) );
            if ( empty( $value ) ) return $default;
            return $value;

        }
    }
}