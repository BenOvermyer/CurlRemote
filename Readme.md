# CurlRemote

This class offers a simple wrapper around PHP cURL functionality. It can handle GET and POST operations and uses associative arrays as its data input.

[![I Love Open Source](http://www.iloveopensource.io/images/logo-lightbg.png)](http://www.iloveopensource.io/projects/526006fc43c6bdee14000084)

## POST request

    $curl = new CurlRemote( 'http://httpbin.org/post' ); // Prepare the endpoint for a POST request
    $response = $curl->send( [ 'foo' => 'bar', 'baz' => 'wiz' ], 'post' ); // Send a POST request

## GET request

    $curl = new CurlRemote( 'http://httpbin.org/get' ); // Prepare the endpoint for a GET request
    $response = $curl->send( [ 'foo' => 'bar', 'baz' => 'wiz' ], 'get' ); // Send a GET request

## Change the user agent

    $curl->setUserAgent( 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/600.1.17 (KHTML, like Gecko) Version/7.1 Safari/537.85.10' ); // Change the User Agent


