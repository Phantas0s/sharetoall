<?php

namespace App\Tests\Service\Api;

use App\Service\Api\TwitterApi;
use App\Tests\Service\Api\Client\FakeClient;
use TestTools\TestCase\UnitTestCase;

class TwitterApiTest extends UnitTestCase
{
    public function testGetAuthUrl()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $twitterApi = $this->getTwitterApi($response);

        $result = $twitterApi->getAuthUrl();
        $this->assertEquals('https://api.twitter.com/oauth/authorize?oauth_token=token&force_login=true', $result);
    }

    private function getTwitterApi(array $fakeClientResponse): TwitterApi
    {
        return new TwitterApi(
            $this->getContainer()->get('cache'),
            new FakeClient($fakeClientResponse),
            'dummyKey',
            'dummySecret'
        );
    }
}
