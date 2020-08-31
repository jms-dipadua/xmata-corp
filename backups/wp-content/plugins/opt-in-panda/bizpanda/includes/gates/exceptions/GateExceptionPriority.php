<?php

namespace bizpanda\includes\gates\exceptions;

/**
 * Priorities of Gate Exceptions
 */
class GateExceptionPriority extends \Exception {

    const LOW = 'low';
    const NORMAL = 'normal';
    const HIGH = 'high';
}