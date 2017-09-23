<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Service\Api\Client\ClientInterface;
use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\OAuth1\Auth;
use App\Service\Api\OAuth1\QueryBuilder;
use App\Service\Api\OAuth1\Token;

use Psr\SimpleCache\CacheInterface;

class TwitterApi
{
    /** @var ClientInterface */
    private $client;

    /** @var Auth */
    private $auth;

    const API_HOST = 'https://api.twitter.com/';
    const API_VERSION = '1.1';
    const API_TIMEOUT = '1000';

    const API_TOKEN_REQUEST_METHOD = 'oauth/request_token';
    const API_TOKEN_AUTHORISE_APP_METHOD = 'oauth/authorize';
    const API_TOKEN_FETCH_LONGTIME_METHOD = 'oauth/access_token';

    const API_POST_TWEET_METHOD = 'statuses/update.json';

    public function __construct(
        CacheInterface $cache,
        ClientInterface $client,
        string $consumerKey,
        string $consumerSecret
    ) {
        $consumer = new Consumer($consumerKey, $consumerSecret);
        $this->client = $client;
        $this->auth = new Auth($cache, $client, $consumer, 'twitter');
    }

    public function getAuthUrl(int $uid)
    {
        $tokenUrl = self::API_HOST . self::API_TOKEN_REQUEST_METHOD;
        $authUrl = self::API_HOST . self::API_TOKEN_AUTHORISE_APP_METHOD;

        $this->auth->fetchOnetimeToken($tokenUrl, $uid);
        return $this->auth->getAuthUrl($authUrl, $uid);
    }

    public function getLongTimeToken(string $authVerifier)
    {
        $url = self::API_HOST . self::API_TOKEN_FETCH_LONGTIME_METHOD;
        $this->auth->getLongTimeToken($url, $authVerifier);
    }

    public function verifyCallbackToken(string $callbackToken, int $uid)
    {
        $this->auth->verifyCallbackToken($callbackToken, $uid);
    }

    public function postTweet(string $content)
    {
        $token = $this->auth->getCachedLongTimeToken();

        if (empty($content)) {
            throw new InvalidArgumentException('You can\'t tweet an empty content!');
        }

        if (empty($token->getKey())) {
            throw new InvalidArgumentException('The token is impossible to find');
        }

        $parameters = ['status' => $content];
        $url = self::API_HOST . '/' . self::API_VERSION . '/' . self::API_POST_TWEET_METHOD;

        $headers = [
            'Authorization' =>  $this->auth->buildOauthHeaders($url, 'POST', $token, $parameters)
        ];

        $this->client->post($url, $headers, $parameters);
    }
}
