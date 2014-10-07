<?php

use CurlRemote\CurlRemote;

class CurlRemoteTest extends PHPUnit_Framework_TestCase
{
    public function testCanSetUrl()
    {
        $curlRemote = new CurlRemote( 'http://httpbin.org/get' );

        $curlRemote->setUrl( 'http://httpbin.org/get' );

        $this->assertEquals( 'http://httpbin.org/get', $curlRemote->getURL() );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadUrlFails()
    {
        $curlRemote = new CurlRemote( 'foobar' );
    }

    public function testSetMethod()
    {
        $curlRemote = new CurlRemote( 'http://httpbin.org/get' );

        $this->assertEquals( 'get', $curlRemote->getMethod() );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetBadMethodFails()
    {
        $curlRemote = new CurlRemote( 'http://httpbin.org/get' );

        $curlRemote->setMethod( 'execute' );
    }
} 