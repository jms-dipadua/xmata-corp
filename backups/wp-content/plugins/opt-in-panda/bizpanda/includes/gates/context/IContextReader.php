<?php

namespace bizpanda\includes\gates\context;

/**
 * Interface IContextReader
 */
interface IContextReader
{
    /**
     * Inits the context.
     * @param $id string An custom ID to init.
     */
    public function init( $id = null );

    /**
     * Gets a value with a given key/name.
     * @param $name string A key to get a value.
     * @param null $default A default value if there is not any values exist.
     * @return mixed A value.
     */
    public function get( $name, $default = null );
}