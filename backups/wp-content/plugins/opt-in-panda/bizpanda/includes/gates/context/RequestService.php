<?php

namespace bizpanda\includes\gates\context;

/**
 * Returns values from the current request.
 */
class RequestService implements IContextReader
{
    /**
     * @inheritDoc
     */
    public function init($id = null)
    {
        // no init needed
    }

    /**
     * Returns a param from the $_REQUEST array.
     * @param $name string A key of the param to return.
     * @param $default mixed A value to return if the param doesn't exist.
     * @return mixed A value to return.
     */
    public function get( $name, $default = null ) {

        $prefixedName = 'opanda' . ucfirst( $name );
        if ( isset( $_REQUEST[$prefixedName] ) ) return $_REQUEST[$prefixedName];

        return ( isset( $_REQUEST[$name] ) ) ? $_REQUEST[$name] : $default;
    }
}