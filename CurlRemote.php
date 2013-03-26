<?php

/**
 * cURL Remote
 *
 * This class acts as a wrapper around cURL to provide streamlined access
 * to some of its more common functions.
 *
 * @author Ben Overmyer <bovermyer@group3marketing.com>
 * @version 1.0
 */

/*
This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class CurlRemote {
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
	function __construct($url, $userAgent = '')
	{
		$this->setURL($url);
		$this->initialize();
		
		if ($userAgent != '')
		{
			$this->setUserAgent($userAgent);
		}
		else
		{
			// Default to version 2.9.9 of Sleipnir, an obscure Japanese web browser.
			$this->setUserAgent('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30618; .NET4.0C; .NET4.0E; Sleipnir/2.9.9)');
		}

		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
	}

	/**
	 * Destructor
	 *
	 * When destroyed, close the cURL connection.
	 */
	function __destruct()
	{
		curl_close($this->handler);
	}

	/**
	 * initialize
	 *
	 * If not initialized, initialize.
	 */
	private function initialize()
	{
		if (!$this->handler)
		{
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
	public function send($data, $method = 'get')
	{
		$this->setMethod($method);

		if ($this->method == 'get')
		{
			$queryString = '';

			foreach ($data as $key => $value)
			{
				$queryString .= "&$key=$value";
			}
			$queryString = '?' . substr($queryString, 1);
			$oldURL = $this->url;

			$this->setURL($oldURL . $queryString);
		}
		elseif ($this->method == 'post')
		{
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $data);
		}

		curl_setopt($this->handler, CURLOPT_URL, $this->url);

		$response = curl_exec($this->handler);

		if (curl_error($this->handler))
		{
			throw new Exception('cURL received an error: ' . curl_error($this->handler));
		}

		return $response;
	}

	/**
	 * setMethod
	 *
	 * Set the transfer method. Currently only supports GET and POST operations.
	 * @param string $method The method to use
	 */
	private function setMethod($method)
	{
		$method = strtolower($method);

		if ($method !== 'post' && $method !== 'get')
		{
			throw new Exception('Invalid method. Accepted methods are "get" and "post."');
		}
		elseif ($method == 'post')
		{
			curl_setopt($this->handler, CURLOPT_POST, true);
			$this->method = 'post';
		}
		else
		{
			curl_setopt($this->handler, CURLOPT_HTTPGET, true);
			$this->method = 'get';
		}
	}

	/**
	 * setURL
	 *
	 * Set the URL to which to send any requests. Note: do not add a query string on to the end of this URL.
	 * @param string $url
	 */
	public function setURL($url)
	{
		$testURL = filter_var($url, FILTER_SANITIZE_URL);

		if ($testURL !== $url)
		{
			throw new Exception('Invalid URL, please check your syntax.');
		}
		else
		{
			$this->url = $testURL;
		}
	}

	/**
	 * setUserAgent
	 *
	 * Sets the User Agent of the request.
	 * @param string $agent
	 */
	public function setUserAgent($agent)
	{
		$testAgent = filter_var($agent, FILTER_SANITIZE_STRING);

		if ($testAgent !== $agent)
		{
			throw new Exception('Invalid user agent, please check your syntax.');
		}
		else
		{
			$this->userAgent = $testAgent;
		}

		curl_setopt($this->handler, CURLOPT_USERAGENT, $this->userAgent);
	}
}
