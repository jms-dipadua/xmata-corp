<?php

namespace bizpanda\includes\gates\context;

/**
 * Returns values from the configuration storage.
 */
class ConfigService implements IContextReader
{
    /**
     * @inheritDoc
     */
    public function init($id = null)
    {
        // no init needed
    }

    /**
     * Returns a value from configuration.
     * @param $name string A key of the config to return.
     * @param $default mixed A value to return if the param doesn't exist.
     * @return mixed A value from the config.
     */
    public function get( $name, $default = null ) {
        return opanda_get_handler_options( $name );
    }
}