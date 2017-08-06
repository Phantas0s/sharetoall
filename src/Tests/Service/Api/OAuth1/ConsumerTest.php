<?php

namespace App\Tests\Service\Api\OAuth1;

use App\Service\Api\OAuth1\Consumer;
use TestTools\TestCase\UnitTestCase;

class ConsumerTest extends UnitTestCase
{
    public function testGetter()
    {
        $consumer = new Consumer('key', 'secret');
        $this->assertEquals('key', $consumer->getKey());
        $this->assertEquals('secret', $consumer->getSecret());
    }
}
