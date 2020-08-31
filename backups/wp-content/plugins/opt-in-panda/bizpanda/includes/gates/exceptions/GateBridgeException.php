<?php

namespace bizpanda\includes\gates\exceptions;

use Throwable;

/**
 * An exception which shows the error for public.
 */
class GateBridgeException extends GateException {

    const UNEXPECTED_RESPONSE = 'gate.bridge.unexpected-response';
    const ERROR_RESPONSE = 'gate.bridge.error-response';
    const NOT_AUTHENTICATED = 'gate.bridge.not-supported';
    const NOT_SUPPORTED = 'gate.bridge.not-supported';

    public static function create( $code, $details = [] ) {

        $baseData = [
            'code' => $code,
            'details' => $details
        ];

        $exceptionData = [];

        switch ( $code ) {

            case self::UNEXPECTED_RESPONSE:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'Unexpected response.'
                ];
                break;

            case self::ERROR_RESPONSE:

                $exceptionData = [
                    'priority' => GateExceptionPriority::NORMAL,
                    'message' => 'Error occurred during the request.'
                ];
                break;

            case self::NOT_AUTHENTICATED:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'Authentication failed.'
                ];
                break;

            case self::NOT_SUPPORTED:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'The method is not supported.',
                ];
                break;
        }

        $exceptionData = array_merge($baseData, $exceptionData);
        return new GateBridgeException($exceptionData);
    }
}