<?php

namespace App\Tests\Service\Api\OAuth1;

use App\Service\Api\OAuth1\Token;
use TestTools\TestCase\UnitTestCase;

class TokenTest extends UnitTestCase
{
    public function testGetter()
    {
        $token = new Token('key', 'secret');
        $this->assertEquals('key', $token->getKey());
        $this->assertEquals('secret', $token->getSecret());
    }
}
