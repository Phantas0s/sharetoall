<?php

namespace App\Tests\Service\Api\OAuth1;

use App\Exception\OAuthException;
use App\Service\Api\OAuth1\Auth;
use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\OAuth1\QueryBuilder;
use App\Service\Api\OAuth1\Token;
use App\Tests\Service\Api\Client\FakeClient;
use TestTools\TestCase\UnitTestCase;

class AuthTest extends UnitTestCase
{
    /** @var CacheInterface */
    private $cache;

    /** @var QueryBuilder */
    private $queryBuilder;

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
        $this->consumer = new Consumer('dummy', 'dummysecret');
        $this->queryBuilder = new QueryBuilder();
        //Unique id in order to cache the tokens in different keys (if multiple tokens)
        $this->uid = 1234;

        $this->oAuth = new Auth($this->cache, new FakeClient(), $this->consumer, 'dummyApi');
    }

    public function testBuildSignature()
    {
        $token = new Token('dummytoken', 'dummysecrettoken');

        $parameters = [
            'oauth_nonce' => 'thisisadummynonce',
            'oauth_timestamp' => '1499105805',
        ];

        $headers = $this->oAuth->buildOauthHeaders(
            'https://api.twitter.com/oauth/access_token',
            'post',
            $token,
            $parameters
        );

        $correctHeaders = 'OAuth oauth_consumer_key=dummy,oauth_nonce=thisisadummynonce,oauth_signature=%2BySrIJ06VI7K7maG7rzUmxskLOg%3D,oauth_signature_method=HMAC-SHA1,oauth_timestamp=1499105805,oauth_token=dummytoken,oauth_version=1.0';

        $this->assertEquals($headers, $correctHeaders);
    }

    public function testRequestToken()
    {
        $token = $this->createAndCacheOneTimeToken($this->uid);

        $this->assertEquals('token', $token->getKey());
        $this->assertEquals('secret', $token->getSecret());
    }

    public function testGetAuthUrl()
    {
        $this->createAndCacheOneTimeToken();
        $url = $this->oAuth->getAuthUrl('http://dummyUrl', $this->uid);

        $this->assertEquals('http://dummyUrl?oauth_token=token&force_login=true', $url);
    }

    public function testVerifyCallBackTokenWithWrongToken()
    {
        $this->createAndCacheOneTimeToken();
        $this->expectException(OAuthException::class);
        $this->oAuth->verifyCallbackToken('wrongToken', $this->uid);
    }

    public function testGetCachedLongTimeToken()
    {
        $this->createAndCacheLongTimeToken();
        $token = $this->oAuth->getCachedLongTimeToken();

        $this->assertEquals('longtoken', $token->getKey());
        $this->assertEquals('longsecret', $token->getSecret());
    }

    private function createAndCacheOneTimeToken()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $this->oAuth = new Auth($this->cache, new FakeClient($response), $this->consumer, 'dummyApi');
        return $this->oAuth->fetchOnetimeToken('http://dummyUrl', $this->uid);
    }

    private function createAndCacheLongTimeToken()
    {
        $this->createAndCacheOneTimeToken();
        $response = [
            'oauth_token=longtoken',
            'oauth_token_secret=longsecret'
        ];

        $this->oAuth = new Auth($this->cache, new FakeClient($response), $this->consumer, 'dummyApi');
        return $this->oAuth->getLongTimeToken('http://dummyUrl', 'oauthVerifier', $this->uid);
    }
}
