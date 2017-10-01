<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Service\Api\Client\ClientInterface;
use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\OAuth2\Auth;
use App\Service\Api\OAuth1\QueryBuilder;

use Psr\SimpleCache\CacheInterface;

class LinkedinApi implements NetworkInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var Auth */
    private $auth;

    /** @var CacheInterface */
    private $cache;

    const API_HOST = 'https://www.linkedin.com';
    const API_AUTH_HOST = 'https://www.linkedin.com/oauth';

    const API_TOKEN_AUTHORISE_APP_METHOD = 'authorization';
    const API_TOKEN_FETCH_LONGTIME_METHOD = 'accessToken';

    const API_SHARE_METHOD = 'people/~/shares?format=json';

    const API_VERSION = 'v2';
    const API_TIMEOUT = '1000';

    public function __construct(
        CacheInterface $cache,
        ClientInterface $client,
        string $consumerKey,
        string $consumerSecret
    ) {
        $consumer = new Consumer($consumerKey, $consumerSecret);
        $this->client = $client;
        $this->auth = new Auth($cache, $client, $consumer, 'linkedin');
    }

    public function getAuthUrl(int $uid, string $redirectUri = '/')
    {
        $url = $this->createAuthBaseUrl() . self::API_TOKEN_AUTHORISE_APP_METHOD;
        return $this->auth->getAuthUrl($url, $uid, $redirectUri);
    }

    public function getLongTimeToken(string $oneTimeToken, int $uid)
    {
        $url = $this->createAuthBaseUrl() . self::API_TOKEN_FETCH_LONGTIME_METHOD;
        $this->auth->getLongTimeToken($url, $oneTimeToken, $uid);
    }

    public function verifyCallbackToken(string $callbackToken)
    {
        $this->auth->verifyCallbackToken($callbackToken);
    }

    public function postUpdate(string $content)
    {
        $url = self::API_HOST . '/v1/' . self::API_SHARE_METHOD;

        $parameters = [
            'comment' => $content,
            'visibility' => [
                'code' => 'anyone'
            ]
        ];

        $headers = [
            'Content-type' => 'application/json',
            'x-li-format' => 'json',
            'Authorization' => 'Bearer ' . $this->auth->getCachedLongTimeToken()->getKey(),
        ];

        $this->client->post($url, $headers, $parameters, 'json');
    }

    private function createAuthBaseUrl(): string
    {
        return self::API_AUTH_HOST . '/' . self::API_VERSION . '/';
    }
}
