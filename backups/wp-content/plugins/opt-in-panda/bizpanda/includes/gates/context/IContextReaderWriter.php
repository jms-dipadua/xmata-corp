<?php

namespace bizpanda\includes\gates\context;

/**
 * Interface IContextReader
 */
interface IContextReaderWriter extends IContextReader
{
    /**
     * Saves a value into the context.
     * @param $name string A key of the value to save.
     * @param $value mixed A value to save.
     */
    public function set( $name, $value );
}