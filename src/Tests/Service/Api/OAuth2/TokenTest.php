<?php

namespace App\Tests\Service\Api\OAuth2;

use App\Service\Api\OAuth2\Token;
use TestTools\TestCase\UnitTestCase;

class TokenTest extends UnitTestCase
{
    public function testGetter()
    {
        $token = new Token('key', 123);
        $this->assertEquals('key', $token->getKey());
        $this->assertEquals(123, $token->getTtl());
    }
}
