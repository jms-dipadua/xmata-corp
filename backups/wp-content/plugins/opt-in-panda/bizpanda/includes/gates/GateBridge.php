<?php

namespace bizpanda\includes\gates;

use bizpanda\includes\gates\exceptions\GateBridgeException;
use bizpanda\includes\gates\Gate;
use bizpanda\includes\gates\GateException;

/**
 * The class to proxy requests to gate API.
 */
class GateBridge
{
    /**
     * Contains the last HTTP status code returned.
     * @var int
     */
    public $httpCode;

    /**
     * Contains the last API call.
     * @var string
     */
    public $httpUrl;

    /**
     * Contains the last HTTP info returned.
     * @var mixed[];
     */
    public $httpInfo;

    /**
     * Contains the last HTTP headers returned.
     * @var mixed[];
     */
    public $httpHeaders;

    /**
     * Contains the last HTTP content returned.
     * @var string;
     */
    public $httpContent;

    /**
     * Information about last request (url, method, options)
     * @var mixed[]
     */
    public $lastRequestInfo = null;

    /**
     * Set timeout default.
     * @var int
     */
    public $timeout = 30;

    /**
     * Set connect timeout.
     * @var int
     */
    public $connectTimeout = 30;

    /**
     * Verify SSL Cert.
     * @var bool
     */
    public $sslVerifyPeer = false;

    /**
     * Set the useragent.
     * @var string
     */
    public $useragent = 'Social Login by OnePress v.1.0';

    /**
     * Make an HTTP request
     *
     * @param $url string An URL to make requests.
     * @param $method string A request method (GET, POST, DELETE, PUT).
     * @param mixed[] $options POST data, headers and other date to pass.
     * @return bool|string|mixed[]
     * @throws GateBridgeException
     */
    function http($url, $method, $options = []) {

        $this->httpInfo = array();

        $ci = curl_init();

        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);

        if ( !empty( $options['headers'] ) ) {

            $resultHeaders = [];
            foreach ( $options['headers'] as $headerName => $headerValue ) {
                $resultHeaders[] = $headerName . ': ' . $headerValue;
            }

            curl_setopt($ci, CURLOPT_HTTPHEADER, $resultHeaders);
        }

        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);

        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {

            case 'POST':

                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty( $options['data'] ) ) {

                    if ( is_array( $options['data'] ) ) {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query( $options['data'] ));
                    } else {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $options['data'] );
                    }
                }
                break;

            case 'GET':

                if ( !empty( $options['data'] ) ) {
                    $urlParts = explode('?', $url);
                    $url = $url . ( ( count( $urlParts ) == 1 ) ? '?' : '&' ) . http_build_query( $options['data'] );
                }

                break;

            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($options['data'])) {
                    $url = "{$url}?{$options['data']}";
                }
                break;
        }

        curl_setopt($ci, CURLOPT_URL, $url);

        // saves information about this request

        $this->lastRequestInfo = [
            'url' => $url,
            'method' => $method,
            'options' => $options
        ];

        $content = curl_exec($ci);

        $this->httpContent = $content;
        $this->httpCode = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->httpInfo = array_merge($this->httpInfo, curl_getinfo($ci));
        $this->httpUrl = $url;

        curl_close($ci);

        if ( empty( $content ) ) {
            throw $this->createException( GateBridgeException::UNEXPECTED_RESPONSE, 'Empty response received.');
        }

        $json = json_decode( $content, true );

        if ( !empty( $options['expectJson'] ) && $options['expectJson'] ) {

            if ( null === $json ) {
                throw $this->createException( GateBridgeException::UNEXPECTED_RESPONSE, 'JSON expected.');
            }

            return $json;
        }

        if ( !empty( $json ) ) return $json;

        if ( !empty( $options['expectQuery'] ) && $options['expectQuery']  ){

            $queryData = [];
            parse_str( $content, $queryData );
            return $queryData;
        }

        return $content;
    }

    /**
     * Get the header info to store.
     */
    function getHeader($ch, $header)
    {
        $i = strpos($header, ':');

        if ( !empty( $i ) ) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->httpHeaders[$key] = $value;
        }

        return strlen($header);
    }

    /**
     * Creates an exception with the given code to throw.
     * @param $code
     * @param $clarification
     * @return GateBridgeException
     */
    function createException($code, $clarification = null ) {

        return GateBridgeException::create( $code, [
            'clarification' => $clarification,
            'request' => $this->getLastRequestInfo(),
            'response' => $this->getLastResponseInfo()
        ]);
    }

    /**
     * Returns info about the last request.
     * @return mixed[]
     */
    function getLastRequestInfo() {
        return $this->lastRequestInfo;
    }

    /**
     * Returns info about the last response.
     * @return mixed[]
     */
    function getLastResponseInfo() {

        return [
            'url' => $this->httpUrl,
            'code' => $this->httpCode,
            'info' => $this->httpInfo,
            'headers' => $this->httpHeaders,
            'content' => $this->httpContent
        ];
    }
}


