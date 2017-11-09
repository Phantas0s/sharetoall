<?php

namespace App\Tests\Service\Api;

use App\Exception\OAuthException;
use App\Service\Api\OAuth1\Token;
use App\Service\Api\TwitterApi;
use App\Tests\Service\Api\Client\FakeClient;
use Symfony\Component\Cache\Simple\ArrayCache;
use TestTools\TestCase\UnitTestCase;

class TwitterApiTest extends UnitTestCase
{
    /** @var int */
    private $uid = 1234;

    /** @var ArrayCache */
    private $cache;

    public function setUp()
    {
        $this->cache = $this->getContainer()->get('cache');
        $this->cache->set('twitter_'.$this->uid.'_onetime_token_key', 'dummyKey');
        $this->cache->set('twitter_'.$this->uid.'_onetime_token_secret', 'dummySecret');
    }

    public function testGetAuthUrl()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $twitterApi = $this->getTwitterApi($response);

        $result = $twitterApi->getAuthUrl($this->uid);
        $this->assertEquals('https://api.twitter.com/oauth/authorize?oauth_token=token&force_login=true', $result);
    }

    public function testGetNetworkSlug()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];
        $twitterApi = $this->getTwitterApi($response);
        $networkSlug = $twitterApi->getNetworkSlug();

        $this->assertEquals('twitter', $networkSlug);
    }

    public function testGetLongTimeToken()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $twitterApi = $this->getTwitterApi($response);
        $token = $twitterApi->getLongTimeToken('dummyVerifier', $this->uid);

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals('token', $token->getKey());
        $this->assertEquals('secret', $token->getSecret());
    }

    public function testVerifyCallbackToken()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $twitterApi = $this->getTwitterApi($response);
        $token = $twitterApi->verifyCallbackToken('dummyKey', $this->uid);

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals('dummyKey', $token->getKey());
        $this->assertEquals('dummySecret', $token->getSecret());
    }

    public function testVerifyCallbackTokenWrongToken()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $twitterApi = $this->getTwitterApi($response);
        $this->expectException(OAuthException::class);
        $token = $twitterApi->verifyCallbackToken('wrong', $this->uid);
    }

    public function testPostUpdate()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $token = new Token('dummyKey', 'dummySecret');

        $twitterApi = $this->getTwitterApi($response);
        $result = $twitterApi->postUpdate('nice message', $token);

        $this->assertEquals(200, $result->getStatusCode());
    }

    private function getTwitterApi(array $fakeClientResponse): TwitterApi
    {
        return new TwitterApi(
            $this->cache,
            new FakeClient($fakeClientResponse),
            'dummyKey',
            'dummySecret'
        );
    }
}
