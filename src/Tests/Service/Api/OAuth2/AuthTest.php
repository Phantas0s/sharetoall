<?php

namespace App\Tests\Service\Api\OAuth2;

use App\Exception\ApiException;
use App\Exception\NotFoundException;
use App\Exception\OAuthException;
use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\OAuth2\Auth;
use App\Tests\Service\Api\Client\FakeClient;
use App\Tests\Service\Api\Client\FakeClientException;
use TestTools\TestCase\UnitTestCase;

class AuthTest extends UnitTestCase
{
    /** @var CacheInterface */
    private $cache;

    /** @var Consumer */
    private $consumer;

    /** @var Auth */
    private $oAuth;

    /** @var int */
    private $uid;

    public function setUp()
    {
        $container = $this->getContainer();
        $this->cache = $container->get('cache');
        $this->consumer = new Consumer('dummy', 'dummySecret');
        $this->uid = 1234;

        $this->oAuth = new Auth($this->cache, new FakeClient(), $this->consumer, 'dummyApi');
    }

    public function testGetAuthUrl()
    {
        $authUrl = $this->generateOneTimeToken();

        $this->assertEquals(
            'http://dummyurl?response_type=code&client_id=dummy&redirect_uri=http%3A%2F%2Fsharetoall.loc&state=dummyToken',
            $authUrl
        );
    }

    public function testVerifyCallBackTokenWhenTokenWrong()
    {
        $this->generateOneTimeToken();
        $this->expectException(OAuthException::class);
        $this->oAuth->verifyCallbackToken('wrongToken', $this->uid);
    }

    public function testVerifyCallBackTokenWhenTokenDoesNotExist()
    {
        $this->generateOneTimeToken();
        $this->expectException(NotFoundException::class);
        $this->oAuth->verifyCallbackToken('wrongToken', 999999999);
    }

    public function testGetLongTimeToken()
    {
        $token = $this->generateLongTimeToken();

        $this->assertEquals('dummyAccessToken', $token->getKey());
        $this->assertEquals(60, $token->getTtl());
    }

    public function testGetLongTimeTokenWithException()
    {
        $responseBody = [
            'access_token' => 'dummyAccessToken',
            'expires_in' => 60,
        ];

        $this->oAuth = new Auth($this->cache, new FakeClientException($responseBody, 'json'), $this->consumer, 'dummyApi');

        $this->expectException(ApiException::class);
        $this->oAuth->getLongTimeToken('http://dummyUrl', 'dummyToken', $this->uid, 'http://dummyredirect');
    }


    public function testGetCachedLongTimeToken()
    {
        $this->generateLongTimeToken();
        $token = $this->oAuth->getCachedLongtimeToken($this->uid);

        $this->assertEquals('dummyAccessToken', $token->getKey());
        $this->assertEquals(60, $token->getTtl());
    }

    private function generateOneTimeToken()
    {
        return $this->oAuth->getAuthUrl('http://dummyurl', $this->uid, 'http://sharetoall.loc', ['state' => 'dummyToken']);
    }

    private function generateLongTimeToken()
    {
        $responseBody = [
            'access_token' => 'dummyAccessToken',
            'expires_in' => 60,
        ];

        $this->oAuth = new Auth($this->cache, new FakeClient($responseBody, 'json'), $this->consumer, 'dummyApi');
        return $this->oAuth->getLongTimeToken('http://dummyUrl', 'dummyToken', $this->uid, 'http://dummyredirect');
    }
}
