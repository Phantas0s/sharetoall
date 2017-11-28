<?php

namespace App\Tests\Service\Api\Client;

use App\Service\Api\Client\Response;
use TestTools\TestCase\UnitTestCase;

class ResponseTest extends UnitTestCase
{
    public function testResponse()
    {
        $response = new Response('200', ['header' => 'header'], 'first=value&arr[]=foo+bar&arr[]=baz');

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertArrayHasKey('header', $response->getHeaders());
        $this->assertEquals('first=value&arr[]=foo+bar&arr[]=baz', $response->getBody());
        $this->assertInternalType('array', $response->getBodyAsArray());
        $this->assertEquals('value', $response->getBodyAsArray()['first']);
    }
}
