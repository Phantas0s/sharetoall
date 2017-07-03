<?php

namespace App\Tests\Model;

use App\Module\TwitterApi\Services\Http\Consumer;
use App\Module\TwitterApi\Services\Http\GuzzleClient;
use App\Module\TwitterApi\Services\Http\OAuth;
use App\Module\TwitterApi\Services\Http\QueryBuilder;
use App\Module\TwitterApi\Services\Http\Token;
use TestTools\TestCase\UnitTestCase;

class OAuthTest extends UnitTestCase
{
    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var GuzzleClient */
    private $client;

    /** @var Consumer */
    private $consumer;

    /** @var Token */
    private $token;

    public function setUp()
    {
        $container = $this->getContainer();
        $this->consumer = $container->get('service.twitter_consumer');
        $this->token = new Token('H4BtIwAAAAAA0fHMAAABXQmq_rE', 'XcC1BOHZfQOxvGNajqJkSm1GwbmyrlyO');
        $this->client = new GuzzleClient();
        $this->queryBuilder = new QueryBuilder();
    }

    public function testBuildSignature()
    {
        $oAuth = new OAuth($this->client, $this->consumer, $this->token);

        $parameters = [
            'oauth_nonce' => '12e77c4946347f50162a49f13d2ebd62',
            'oauth_timestamp' => '1499105805',
        ];

        $headers = $oAuth->buildOauthHeaders('https://api.twitter.com/oauth/access_token','post', $parameters);

        $correctHeaders = 'OAuth oauth_consumer_key=o9WYRPTW6PHEcDcjMVHgoLsLp,oauth_nonce=12e77c4946347f50162a49f13d2ebd62,oauth_signature=ng1Y2YRSfWYAeD%2FWcOLo2A3SZLs%3D,oauth_signature_method=HMAC-SHA1,oauth_timestamp=1499105805,oauth_token=H4BtIwAAAAAA0fHMAAABXQmq_rE,oauth_version=1.0';

        $this->assertEquals($headers, $correctHeaders);
    }
}
