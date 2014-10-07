<?php namespace CurlRemote;
use Doctrine\Instantiator\Exception\InvalidArgumentException;

    /**
     * cURL Remote
     *
     * This class acts as a wrapper around cURL to provide streamlined access
     * to some of its more common functions.
     *
     * @author Ben Overmyer <manatrance@gmail.com>
     * @version 1.0.1
     */

class CurlRemote
{
    private $method;
    private $userAgent;
    private $url;
    private $handler;

    /**
     * Constructor
     *
     * Initializes the cURL handler and sets the URL. Will always return cURL responses as strings.
     * @param string $url The URL to be sent to
     * @param string $userAgent The User Agent to interact as, if any
     */
    function __construct( $url, $userAgent = '' )
    {
        $this->setURL( $url );
        $this->initialize();
        $this->setMethod( 'get' );

        if ( $userAgent != '' ) {
            $this->setUserAgent( $userAgent );
        } else {
            // Default to version 2.9.9 of Sleipnir, an obscure Japanese web browser.
            $this->setUserAgent( 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30618; .NET4.0C; .NET4.0E; Sleipnir/2.9.9)' );
        }

        curl_setopt( $this->handler, CURLOPT_RETURNTRANSFER, true );
    }

    /**
     * Destructor
     *
     * When destroyed, close the cURL connection.
     */
    function __destruct()
    {
        curl_close( $this->handler );
    }

    /**
     * initialize
     *
     * If not initialized, initialize.
     */
    private function initialize()
    {
        if ( !$this->handler ) {
            $this->handler = curl_init();
        }
    }

    /**
     * send
     *
     * Sends data to the remote URL, using a URL string appropriate to the transfer method.
     * @param Array $data The data to send as an associative array
     * @param string $method The transfer method to use. Defaults to get.
     * @return string
     */
    public function send( $data, $method = 'get' )
    {
        $this->setMethod( $method );

        if ( $this->method == 'get' ) {
            $queryString = '';

            foreach ( $data as $key => $value ) {
                $queryString .= "&$key=$value";
            }
            $queryString = '?' . substr( $queryString, 1 );
            $oldURL = $this->url;

            $this->setURL( $oldURL . $queryString );
        } elseif ( $this->method == 'post' ) {
            curl_setopt( $this->handler, CURLOPT_POSTFIELDS, $data );
        }

        curl_setopt( $this->handler, CURLOPT_URL, $this->url );

        $response = curl_exec( $this->handler );

        if ( curl_error( $this->handler ) ) {
            return false;
        }

        return $response;
    }

    /**
     * setMethod
     *
     * Set the transfer method. Currently only supports GET and POST operations.
     * @param string $method The method to use
     */
    public function setMethod( $method )
    {
        $method = strtolower( $method );

        if ( $method !== 'post' && $method !== 'get' ) {
            throw new InvalidArgumentException( 'Method should be one of: post, get' );
        } elseif ( $method == 'post' ) {
            curl_setopt( $this->handler, CURLOPT_POST, true );
            $this->method = 'post';
        } else {
            curl_setopt( $this->handler, CURLOPT_HTTPGET, true );
            $this->method = 'get';
        }

        return $this->method;
    }

    /**
     * getMethod
     *
     * Get the currently set method.
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * setURL
     *
     * Set the URL to which to send any requests. Note: do not add a query string on to the end of this URL.
     * @param string $url
     */
    public function setURL( $url )
    {
        $testURL = filter_var( $url, FILTER_SANITIZE_URL );
        $urlRegex = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
        $match = preg_match( $urlRegex, $testURL );

        if ( $testURL !== $url || $match !== 1 ) {
            throw new InvalidArgumentException( 'Invalid URL.' );
        } else {
            $this->url = $testURL;
        }

        return $this->url;
    }

    /**
     * getURL
     *
     * Get the currently set URL.
     * @return mixed
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * setUserAgent
     *
     * Sets the User Agent of the request.
     * @param string $agent
     */
    public function setUserAgent( $agent )
    {
        $testAgent = filter_var( $agent, FILTER_SANITIZE_STRING );

        if ( $testAgent !== $agent ) {
            throw new InvalidArgumentException( 'Invalid user agent string.' );
        } else {
            $this->userAgent = $testAgent;
        }

        curl_setopt( $this->handler, CURLOPT_USERAGENT, $this->userAgent );

        return $this->userAgent;
    }

    /**
     * getUserAgent
     *
     * Get the currently set user agent string.
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }
}
