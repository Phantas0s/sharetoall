<?php

namespace App\Tests\Service\Api\OAuth1;

use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\Client\GuzzleClient;
use App\Service\Api\OAuth1\Auth;
use App\Service\Api\OAuth1\QueryBuilder;
use App\Service\Api\OAuth1\Token;
use TestTools\TestCase\UnitTestCase;

class AuthTest extends UnitTestCase
{
    /** @var CacheInterface */
    private $cache;

    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var GuzzleClient */
    private $client;

    /** @var Consumer */
    private $consumer;

    /** @var Auth */
    private $oAuth;

    /** @var Token */
    private $token;

    public function setUp()
    {
        $container = $this->getContainer();
        $this->cache = $container->get('cache');
        $this->consumer = $container->get('service.twitter_consumer');
        $this->client = new GuzzleClient();
        $this->queryBuilder = new QueryBuilder();
        $this->oAuth = new Auth($this->cache, $this->client, $this->consumer, 'dummyApi');
        $this->token = new Token('dummytoken', 'dummysecrettoken');
    }

    public function testBuildSignature()
    {

        $parameters = [
            'oauth_nonce' => 'thisisadummynonce',
            'oauth_timestamp' => '1499105805',
        ];

        $headers = $this->oAuth->buildOauthHeaders(
            'https://api.twitter.com/oauth/access_token',
            'post',
            $this->token,
            $parameters
        );

        $correctHeaders = 'OAuth oauth_consumer_key=o9WYRPTW6PHEcDcjMVHgoLsLp,oauth_nonce=thisisadummynonce,oauth_signature=smwbG77Wk5UMeP0yRJ8Nw1SwF%2Bc%3D,oauth_signature_method=HMAC-SHA1,oauth_timestamp=1499105805,oauth_token=dummytoken,oauth_version=1.0';

        $this->assertEquals($headers, $correctHeaders);
    }

    public function testCache()
    {
        $this->oAuth->cacheOnetimeToken($this->token);
    }
}
