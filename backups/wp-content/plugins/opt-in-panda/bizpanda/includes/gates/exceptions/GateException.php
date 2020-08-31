<?php

namespace bizpanda\includes\gates\exceptions;

/**
 * An exception that shows the error for public.
 */
class GateException extends \Exception {

    const INVALID_INBOUND_REQUEST = 'gate.invalid-request';
    const SCOPE_MISSED = 'gate.scope-missed';
    const APP_NOT_CONFIGURED = 'gate.app-not-configured';
    const AUTH_FLOW_BROKEN = 'gate.auth-flow-broken';
    const INVALID_VISITOR_ID = 'gate.invalid-visitor-id';

    public static function create( $code, $details = [] ) {

        if ( is_string( $details ) ) {

            $details = [
                'clarification' => $details
            ];
        }

        $exceptionData = [];

        $baseData = [
            'code' => $code,
            'details' => $details
        ];

        switch ( $code ) {

            case self::INVALID_INBOUND_REQUEST:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'Invalid request type.'
                ];
                break;

            case self::SCOPE_MISSED:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'The scope is not set.'
                ];
                break;

            case self::APP_NOT_CONFIGURED:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'The app is not configured properly.',
                ];
                break;

            case self::AUTH_FLOW_BROKEN:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'Authorization flow broken. Please try again.',
                ];
                break;

            case self::INVALID_VISITOR_ID:

                $exceptionData = [
                    'priority' => GateExceptionPriority::HIGH,
                    'message' => 'Invalid Visitor ID.',
                ];
                break;
        }

        $exceptionData = array_merge($baseData, $exceptionData);
        throw new GateException($exceptionData);
    }

    protected $exceptionCode = null;
    protected $exceptionDetails = null;
    protected $exceptionPriority = null;

    public function __construct( $data )
    {
        parent::__construct($data['message'], 0, null);

        $this->exceptionPriority = isset( $data['priority'] ) ? $data['priority'] : GateExceptionPriority::NORMAL;
        $this->exceptionCode = $data['code'];
        $this->exceptionDetails = $data['details'];
    }

    public function getExceptionCode() {
        return $this->exceptionCode;
    }

    public function getExceptionDetails() {
        return $this->exceptionDetails;
    }

    public function getExceptionPriority() {
        return !empty( $this->exceptionPriority ) ? $this->exceptionPriority : GateExceptionPriority::NORMAL;
    }

    public function getExceptionVisibleMessage() {
        $error = $this->getMessage();

        if ( isset( $this->exceptionDetails['clarification'] ) ) {
            $error = trim($error, '.') . '. ' . trim( $this->exceptionDetails['clarification'], '.' ) . '.';
        }

        return $error;
    }

    /**
     * Returns exception data to log.
     * @return array
     */
    public function getDataToLog() {

        return [
            'code' => $this->exceptionCode,
            'message' => $this->getExceptionVisibleMessage(),
            'details' => $this->getExceptionDetails(),
            'priority' => $this->getExceptionPriority(),
            'trace' => $this->getTraceAsString()
        ];
    }
}