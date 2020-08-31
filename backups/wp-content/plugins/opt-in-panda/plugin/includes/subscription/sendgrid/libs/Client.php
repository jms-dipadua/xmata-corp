<?php
/**
  * HTTP Client library
  *
  * PHP version 5.2
  *
  * @author    Matt Bernier <dx@sendgrid.com>
  * @author    Elmer Thomas <dx@sendgrid.com>
  * @copyright 2016 SendGrid
  * @license   https://opensource.org/licenses/MIT The MIT License
  * @version   GIT: <git_id>
  * @link      http://packagist.org/packages/sendgrid/php-http-client
  */

/**
 * Class InvalidHttpRequest
 *
 * Thrown when invalid payload was constructed, which could not reach SendGrid server.
 *
 * @package SendGrid\Exceptions
 */
class Opanda_Sendgrid_InvalidRequest extends \Exception
{
    public function __construct(
        $message = "",
        $code = 0,
        $previous = null
    ) {
        $message = 'Could not send request to server. '.
            'CURL error '.$code.': '.$message;
        parent::__construct($message, $code, $previous);
    }

}

/**
 * Holds the response from an API call.
 */
class Opanda_Sendgrid_Response
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $headers;

    /**
     * Setup the response data
     *
     * @param int    $statusCode the status code.
     * @param string $body       the response body.
     * @param array  $headers    an array of response headers.
     */
    public function __construct($statusCode = 200, $body = '', array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * The status code
     *
     * @return int
     */
    public function statusCode()
    {
        return $this->statusCode;
    }

    /**
     * The response body
     *
     * @return string
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * The response headers
     *
     * @param bool $assoc
     *
     * @return array
     */
    public function headers($assoc = false)
    {
        if (!$assoc) {
            return $this->headers;
        }

        return $this->prettifyHeaders($this->headers);
    }

    /**
     * Returns response headers as associative array
     *
     * @param array $headers
     *
     * @return array
     */
    private function prettifyHeaders(array $headers)
    {
        return array_reduce(
            array_filter($headers),
            function ($result, $header) {

                if (false === strpos($header, ':')) {
                    $result['Status'] = trim($header);

                    return $result;
                }

                list($key, $value) = explode(':', $header, 2);
                $result[trim($key)] = trim($value);

                return $result;
            },
            []
        );
    }
}

/**
 *
 * Class Client
 * @package SendGrid
 * @version 3.9.5
 *
 * Quickly and easily access any REST or REST-like API.
 *
 * @method Opanda_Sendgrid_Response get($body = null, $query = null, $headers = null)
 * @method Opanda_Sendgrid_Response post($body = null, $query = null, $headers = null)
 * @method Opanda_Sendgrid_Response patch($body = null, $query = null, $headers = null)
 * @method Opanda_Sendgrid_Response put($body = null, $query = null, $headers = null)
 * @method Opanda_Sendgrid_Response delete($body = null, $query = null, $headers = null)
 *
 * @method Opanda_Sendgrid_Client version($value)
 * @method Opanda_Sendgrid_Client|Opanda_Sendgrid_Response send()
 *
 * Adding all the endpoints as a method so code completion works
 *
 * General
 * @method Opanda_Sendgrid_Client stats()
 * @method Opanda_Sendgrid_Client search()
 * @method Opanda_Sendgrid_Client monthly()
 * @method Opanda_Sendgrid_Client sums()
 * @method Opanda_Sendgrid_Client monitor()
 * @method Opanda_Sendgrid_Client test()
 *
 * Access settings
 * @method Opanda_Sendgrid_Client access_settings()
 * @method Opanda_Sendgrid_Client activity()
 * @method Opanda_Sendgrid_Client whitelist()
 *
 * Alerts
 * @method Opanda_Sendgrid_Client alerts()
 *
 * Api keys
 * @method Opanda_Sendgrid_Client api_keys()
 *
 * ASM
 * @method Opanda_Sendgrid_Client asm()
 * @method Opanda_Sendgrid_Client groups()
 * @method Opanda_Sendgrid_Client suppressions()
 *
 * Browsers
 * @method Opanda_Sendgrid_Client browsers()
 *
 * Campaigns
 * @method Opanda_Sendgrid_Client campaigns()
 * @method Opanda_Sendgrid_Client schedules()
 * @method Opanda_Sendgrid_Client now()
 *
 * Categories
 * @method Opanda_Sendgrid_Client categories()
 *
 * Clients
 * @method Opanda_Sendgrid_Client clients()
 *
 * Marketing
 * @method Opanda_Sendgrid_Client marketing()
 * @method Opanda_Sendgrid_Client contacts()
 * @method Opanda_Sendgrid_Client count()
 * @method Opanda_Sendgrid_Client exports()
 * @method Opanda_Sendgrid_Client imports()
 * @method Opanda_Sendgrid_Client lists()
 * @method Opanda_Sendgrid_Client field_definitions()
 * @method Opanda_Sendgrid_Client segments()
 * @method Opanda_Sendgrid_Client singlesends()
 *
 *
 * Devices
 * @method Opanda_Sendgrid_Client devices()
 *
 * Geo
 * @method Opanda_Sendgrid_Client geo()
 *
 * Ips
 * @method Opanda_Sendgrid_Client ips()
 * @method Opanda_Sendgrid_Client assigned()
 * @method Opanda_Sendgrid_Client pools()
 * @method Opanda_Sendgrid_Client warmup()
 *
 * Mail
 * @method Opanda_Sendgrid_Client mail()
 * @method Opanda_Sendgrid_Client batch()
 *
 * Mailbox Providers
 * @method Opanda_Sendgrid_Client mailbox_providers()
 *
 * Mail settings
 * @method Opanda_Sendgrid_Client mail_settings()
 * @method Opanda_Sendgrid_Client address_whitelist()
 * @method Opanda_Sendgrid_Client bcc()
 * @method Opanda_Sendgrid_Client bounce_purge()
 * @method Opanda_Sendgrid_Client footer()
 * @method Opanda_Sendgrid_Client forward_bounce()
 * @method Opanda_Sendgrid_Client forward_spam()
 * @method Opanda_Sendgrid_Client plain_content()
 * @method Opanda_Sendgrid_Client spam_check()
 * @method Opanda_Sendgrid_Client template()
 *
 * Partner settings
 * @method Opanda_Sendgrid_Client partner_settings()
 * @method Opanda_Sendgrid_Client new_relic()
 *
 * Scopes
 * @method Opanda_Sendgrid_Client scopes()
 *
 * Senders
 * @method Opanda_Sendgrid_Client senders()
 * @method Opanda_Sendgrid_Client resend_verification()
 *
 * Sub Users
 * @method Opanda_Sendgrid_Client subusers()
 * @method Opanda_Sendgrid_Client reputations()
 *
 * Supressions
 * @method Opanda_Sendgrid_Client suppression()
 * @method Opanda_Sendgrid_Client global()
 * @method Opanda_Sendgrid_Client blocks()
 * @method Opanda_Sendgrid_Client bounces()
 * @method Opanda_Sendgrid_Client invalid_emails()
 * @method Opanda_Sendgrid_Client spam_reports()
 * @method Opanda_Sendgrid_Client unsubcribes()
 *
 * Templates
 * @method Opanda_Sendgrid_Client templates()
 * @method Opanda_Sendgrid_Client versions()
 * @method Opanda_Sendgrid_Client activate()
 *
 * Tracking settings
 * @method Opanda_Sendgrid_Client tracking_settings()
 * @method Opanda_Sendgrid_Client click()
 * @method Opanda_Sendgrid_Client google_analytics()
 * @method Opanda_Sendgrid_Client open()
 * @method Opanda_Sendgrid_Client subscription()
 *
 * User
 * @method Opanda_Sendgrid_Client user()
 * @method Opanda_Sendgrid_Client account()
 * @method Opanda_Sendgrid_Client credits()
 * @method Opanda_Sendgrid_Client email()
 * @method Opanda_Sendgrid_Client password()
 * @method Opanda_Sendgrid_Client profile()
 * @method Opanda_Sendgrid_Client scheduled_sends()
 * @method Opanda_Sendgrid_Client enforced_tls()
 * @method Opanda_Sendgrid_Client settings()
 * @method Opanda_Sendgrid_Client username()
 * @method Opanda_Sendgrid_Client webhooks()
 * @method Opanda_Sendgrid_Client event()
 * @method Opanda_Sendgrid_Client parse()
 *
 * Missed any? Simply add them by doing: @method Opanda_Sendgrid_Client method()
 */
class Opanda_Sendgrid_Client
{
    const TOO_MANY_REQUESTS_HTTP_CODE = 429;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var array
     */
    protected $path;

    /**
     * @var array
     */
    protected $curlOptions;

    /**
     * @var bool
     */
    protected $isConcurrentRequest;

    /**
     * @var array
     */
    protected $savedRequests;

    /**
     * @var bool
     */
    protected $retryOnLimit;

    /**
     * These are the supported HTTP verbs
     *
     * @var array
     */
    private $methods = ['get', 'post', 'patch', 'put', 'delete'];

    /**
     * Initialize the client
     *
     * @param string  $host          the base url (e.g. https://api.sendgrid.com)
     * @param array   $headers       global request headers
     * @param string  $version       api version (configurable) - this is specific to the SendGrid API
     * @param array   $path          holds the segments of the url path
     * @param array   $curlOptions   extra options to set during curl initialization
     * @param bool    $retryOnLimit  set default retry on limit flag
     */
    public function __construct($host, $headers = null, $version = null, $path = null, $curlOptions = null, $retryOnLimit = false)
    {
        $this->host = $host;
        $this->headers = $headers ?: [];
        $this->version = $version;
        $this->path = $path ?: [];
        $this->curlOptions = $curlOptions ?: [];
        $this->retryOnLimit = $retryOnLimit;
        $this->isConcurrentRequest = false;
        $this->savedRequests = [];
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }

    /**
     * Set extra options to set during curl initialization
     *
     * @param array $options
     *
     * @return Opanda_Sendgrid_Client
     */
    public function setCurlOptions(array $options)
    {
        $this->curlOptions = $options;

        return $this;
    }

    /**
     * Set default retry on limit flag
     *
     * @param bool $retry
     *
     * @return Opanda_Sendgrid_Client
     */
    public function setRetryOnLimit($retry)
    {
        $this->retryOnLimit = $retry;

        return $this;
    }

    /**
     * Set concurrent request flag
     *
     * @param bool $isConcurrent
     *
     * @return Opanda_Sendgrid_Client
     */
    public function setIsConcurrentRequest($isConcurrent)
    {
        $this->isConcurrentRequest = $isConcurrent;

        return $this;
    }

    /**
     * Build the final URL to be passed
     *
     * @param array $queryParams an array of all the query parameters
     *
     * @return string
     */
    private function buildUrl($queryParams = null)
    {
        $path = '/' . implode('/', $this->path);
        if (isset($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }
        return sprintf('%s%s%s', $this->host, $this->version ?: '', $path);
    }

    /**
     * Creates curl options for a request
     * this function does not mutate any private variables
     *
     * @param string $method
     * @param array $body
     * @param array $headers
     *
     * @return array
     */
    private function createCurlOptions($method, $body = null, $headers = null)
    {
        $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_CUSTOMREQUEST => strtoupper($method),
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FAILONERROR => false
            ] + $this->curlOptions;

        if (isset($headers)) {
            $headers = array_merge($this->headers, $headers);
        } else {
            $headers = $this->headers;
        }

        if (isset($body)) {
            $encodedBody = json_encode($body);
            $options[CURLOPT_POSTFIELDS] = $encodedBody;
            $headers = array_merge($headers, ['Content-Type: application/json']);
        }
        $options[CURLOPT_HTTPHEADER] = $headers;

        return $options;
    }

    /**
     * @param array $requestData
     *      e.g. ['method' => 'POST', 'url' => 'www.example.com', 'body' => 'test body', 'headers' => []]
     * @param bool $retryOnLimit
     *
     * @return array
     */
    private function createSavedRequest(array $requestData, $retryOnLimit = false)
    {
        return array_merge($requestData, ['retryOnLimit' => $retryOnLimit]);
    }

    /**
     * @param array $requests
     *
     * @return array
     */
    private function createCurlMultiHandle(array $requests)
    {
        $channels = [];
        $multiHandle = curl_multi_init();

        foreach ($requests as $id => $data) {
            $channels[$id] = curl_init($data['url']);
            $curlOpts = $this->createCurlOptions($data['method'], $data['body'], $data['headers']);
            curl_setopt_array($channels[$id], $curlOpts);
            curl_multi_add_handle($multiHandle, $channels[$id]);
        }

        return [$channels, $multiHandle];
    }

    /**
     * Prepare response object
     *
     * @param resource $channel  the curl resource
     * @param string   $content
     *
     * @return Opanda_Sendgrid_Response object
     */
    private function parseResponse($channel, $content)
    {
        $headerSize = curl_getinfo($channel, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);

        $responseBody = substr($content, $headerSize);

        $responseHeaders = substr($content, 0, $headerSize);
        $responseHeaders = explode("\n", $responseHeaders);
        $responseHeaders = array_map('trim', $responseHeaders);

        return new Opanda_Sendgrid_Response($statusCode, $responseBody, $responseHeaders);
    }

    /**
     * Retry request
     *
     * @param array  $responseHeaders headers from rate limited response
     * @param string $method          the HTTP verb
     * @param string $url             the final url to call
     * @param array  $body            request body
     * @param array  $headers         original headers
     *
     * @return Opanda_Sendgrid_Response response object
     */
    private function retryRequest(array $responseHeaders, $method, $url, $body, $headers)
    {
        $sleepDurations = $responseHeaders['X-Ratelimit-Reset'] - time();
        sleep($sleepDurations > 0 ? $sleepDurations : 0);
        return $this->makeRequest($method, $url, $body, $headers, false);
    }

    /**
     * Make the API call and return the response.
     * This is separated into it's own function, so we can mock it easily for testing.
     *
     * @param string $method       the HTTP verb
     * @param string $url          the final url to call
     * @param array  $body         request body
     * @param array  $headers      any additional request headers
     * @param bool   $retryOnLimit should retry if rate limit is reach?
     *
     * @return Opanda_Sendgrid_Response object
     * @throws Opanda_Sendgrid_InvalidRequest
     */
    public function makeRequest($method, $url, $body = null, $headers = null, $retryOnLimit = false)
    {
        $channel = curl_init($url);

        $options = $this->createCurlOptions($method, $body, $headers);

        curl_setopt_array($channel, $options);
        $content = curl_exec($channel);

        if ($content === false) {
            throw new Opanda_Sendgrid_InvalidRequest(curl_error($channel), curl_errno($channel));
        }

        $response = $this->parseResponse($channel, $content);

        if ($response->statusCode() === self::TOO_MANY_REQUESTS_HTTP_CODE && $retryOnLimit) {
            $responseHeaders = $response->headers(true);
            return $this->retryRequest($responseHeaders, $method, $url, $body, $headers);
        }

        curl_close($channel);

        return $response;
    }

    /**
     * Send all saved requests at once
     *
     * @param array $requests
     *
     * @return Opanda_Sendgrid_Response[]
     */
    public function makeAllRequests(array $requests = [])
    {
        if (empty($requests)) {
            $requests = $this->savedRequests;
        }
        list($channels, $multiHandle) = $this->createCurlMultiHandle($requests);

        // running all requests
        $isRunning = null;
        do {
            curl_multi_exec($multiHandle, $isRunning);
        } while ($isRunning);

        // get response and close all handles
        $retryRequests = [];
        $responses = [];
        $sleepDurations = 0;
        foreach ($channels as $id => $channel) {

            $content = curl_multi_getcontent($channel);
            $response = $this->parseResponse($channel, $content);

            if ($response->statusCode() === self::TOO_MANY_REQUESTS_HTTP_CODE && $requests[$id]['retryOnLimit']) {
                $headers = $response->headers(true);
                $sleepDurations = max($sleepDurations, $headers['X-Ratelimit-Reset'] - time());
                $requestData = [
                    'method' => $requests[$id]['method'],
                    'url' => $requests[$id]['url'],
                    'body' => $requests[$id]['body'],
                    'headers' => $headers,
                ];
                $retryRequests[] = $this->createSavedRequest($requestData, false);
            } else {
                $responses[] = $response;
            }

            curl_multi_remove_handle($multiHandle, $channel);
        }
        curl_multi_close($multiHandle);

        // retry requests
        if (!empty($retryRequests)) {
            sleep($sleepDurations > 0 ? $sleepDurations : 0);
            $responses = array_merge($responses, $this->makeAllRequests($retryRequests));
        }
        return $responses;
    }

    /**
     * Add variable values to the url. (e.g. /your/api/{variable_value}/call)
     * Another example: if you have a PHP reserved word, such as and, in your url, you must use this method.
     *
     * @param string $name name of the url segment
     *
     * @return Opanda_Sendgrid_Client object
     */
    public function _($name = null)
    {
        if (isset($name)) {
            $this->path[] = $name;
        }
        $client = new static($this->host, $this->headers, $this->version, $this->path);
        $client->setCurlOptions($this->curlOptions);
        $client->setRetryOnLimit($this->retryOnLimit);
        $this->path = [];

        return $client;
    }

    /**
     * Dynamically add method calls to the url, then call a method.
     * (e.g. client.name.name.method())
     *
     * @param string $name name of the dynamic method call or HTTP verb
     * @param array  $args parameters passed with the method call
     *
     * @return Opanda_Sendgrid_Client|Opanda_Sendgrid_Response|Opanda_Sendgrid_Response[]|null object
     */
    public function __call($name, $args)
    {
        $name = strtolower($name);

        if ($name === 'version') {
            $this->version = $args[0];
            return $this->_();
        }

        // send all saved requests
        if (($name === 'send') && $this->isConcurrentRequest) {
            return $this->makeAllRequests();
        }

        if (in_array($name, $this->methods, true)) {
            $body = isset($args[0]) ? $args[0] : null;
            $queryParams = isset($args[1]) ? $args[1] : null;
            $url = $this->buildUrl($queryParams);
            $headers = isset($args[2]) ? $args[2] : null;
            $retryOnLimit = isset($args[3]) ? $args[3] : $this->retryOnLimit;

            if ($this->isConcurrentRequest) {
                // save request to be sent later
                $requestData = ['method' => $name, 'url' => $url, 'body' => $body, 'headers' => $headers];
                $this->savedRequests[] = $this->createSavedRequest($requestData, $retryOnLimit);
                return null;
            }

            return $this->makeRequest($name, $url, $body, $headers, $retryOnLimit);
        }

        return $this->_($name);
    }
}