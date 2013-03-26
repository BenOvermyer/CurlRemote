<?php

// NOTE: This is just a sample script to demonstrate usage of the CurlRemote class.

require_once 'CurlRemote.php';

$curl = new CurlRemote('http://httpbin.org/post'); // Prepare the endpoint for a POST request
//$curl = new CurlRemote('http://httpbin.org/get'); // Prepare the endpoint for a GET request

//$curl->setUserAgent('Test'); // Change the User Agent

$data = array('test' => '123', 'test2' => 'yay'); // Set up the associative array containing data to send

$response = $curl->send($data, 'post'); // Send a POST request
//$response = $curl->send($data); // Send a GET request

echo $response; // Output the response to the screen
