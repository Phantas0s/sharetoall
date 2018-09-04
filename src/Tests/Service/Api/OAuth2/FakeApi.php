<?php

namespace App\Tests\Service\Api\OAuth2;

use App\Service\Api\Client\ClientInterface;
use App\Service\Api\NetworkInterface;
use App\Service\Api\OAuth2\Auth;
use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\OAuth2\Token;
use Psr\SimpleCache\CacheInterface;
use App\Tests\Service\Api\OAuth2\FakeApi;

class FakeApi implements NetworkInterface
{
    /** @var Consumer */
    private $consumer;

    /** @var ClientInterface */
    private $client;

    /** @var Auth */
    private $auth;

    /** @var string */
    private $networkSlug = 'fake';

    /** @var string */
    private $apiName;

    public function __construct(
        CacheInterface $cache,
        ClientInterface $client,
        string $consumerKey,
        string $consumerSecret,
        string $apiName
    ) {
        $consumer = new Consumer($consumerKey, $consumerSecret);
        $this->client = $client;
        $this->auth = new Auth($cache, $client, $consumer, $apiName);
    }

    public function getAuthUrl(int $uid, string $redirectUri): string
    {
        return 'http://dummyAuthUrl';
    }

    public function verifyCallbackToken(string $callbackToken, int $uid)
    {
        return true;
    }

    public function getLongTimeToken(string $authVerifier, int $uid): Token
    {
        $token = new Token('dummyLongTimeTokenKey', 1000127935);
        return $token;
    }

    public function getNetworkSlug(): string
    {
        return $this->networkSlug;
    }

    public function setNetworkSlug(string $networkSlug): FakeApi
    {
        $this->networkSlug = $networkSlug;
        return $this;
    }

    public function postUpdate(string $content, Token $token): string
    {
        return 'posted!';
    }

    public function getUserInfo(Token $token): string
    {
        return 'infos';
    }
}
