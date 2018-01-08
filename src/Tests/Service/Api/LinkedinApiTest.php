<?php
declare(strict_types=1);

namespace App\Tests\Service\Api;

use App\Exception\OAuthException;
use App\Service\Api\LinkedinApi;
use App\Service\Api\OAuth1\Token;
use App\Tests\Service\Api\Client\FakeClient;
use Symfony\Component\Cache\Simple\ArrayCache;
use TestTools\TestCase\UnitTestCase;

class LinkedinApiTest extends UnitTestCase
{
    /** @var int */
    private $uid = 1234;

    /** @var ArrayCache */
    private $cache;

    public function setUp()
    {
        $this->cache = $this->getContainer()->get('cache');
        $this->cache->set($this->uid.'-linkedin_oauth2_onetime_token', 'token');
    }

    public function testGetNetworkSlug()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $linkedinApi = $this->getLinkedinApi($response);
        $networkSlug = $linkedinApi->getNetworkSlug();

        $this->assertEquals('linkedin', $networkSlug);
    }

    public function testGetAuthUrl()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $linkedinApi = $this->getLinkedinApi($response);
        $authUrl = $linkedinApi->getAuthUrl($this->uid, 'http://dummmyRedirectUri');

        $this->assertNotNull($authUrl);
    }

    public function testGetLongTimeToken()
    {
        $response = [
            '{"access_token":"token","expires_in":5183999}'
        ];

        $linkedinApi = $this->getLinkedinApi($response);
        $token = $linkedinApi->getLongTimeToken('token', $this->uid, 'http://redirectDummyUri');

        $this->assertEquals('token', $token->getKey());
    }

    public function testVerifyCallbackToken()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $linkedinApi = $this->getLinkedinApi($response);
        $token = $linkedinApi->verifyCallbackToken('token', $this->uid);

        $this->assertEquals('token', $token->getKey());
    }

    public function testVerifyCallbackTokenWithWrongToken()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $linkedinApi = $this->getLinkedinApi($response);
        $this->expectException(OAuthException::class);
        $token = $linkedinApi->verifyCallbackToken('lalala', $this->uid);
    }

    public function testPostUpdate()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];

        $token = new Token('key', 'secret');

        $linkedinApi = $this->getLinkedinApi($response);
        $response = $linkedinApi->postUpdate('nice message', $token);

        $this->assertEquals('200', $response->getStatusCode());
    }

    private function getLinkedinApi(array $fakeClientResponse): LinkedinApi
    {
        return new LinkedinApi(
            $this->cache,
            new FakeClient($fakeClientResponse),
            'dummyKey',
            'dummySecret',
            'linkedin'
        );
    }
}
