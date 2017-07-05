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
    private $client;

    private $consumer;

    private $auth;

    private $token;

    /** @var CacheInterface */
    private $cache;

    const API_HOST = 'https://api.twitter.com/';
    const API_VERSION = '1.0';
    const API_TIMEOUT = '1000';

    const API_TOKEN_REQUEST_METHOD = 'oauth/request_token';

    public function __construct(
        CacheInterface $cache,
        ClientInterface $client,
        string $consumerKey,
        string $consumerSecret
    ) {
        $this->cache = $cache;
        $this->client = $client;
        $this->consumer = new Consumer($consumerKey, $consumerSecret);
        $this->auth = new Auth($client, $this->consumer, $this->getToken());
    }

    public function getAuthUrl()
    {
        $this->fetchToken();
        return self::API_HOST . 'oauth/authenticate?oauth_token=' . $this->token->getKey().'&force_login=true';
    }

    public function getLongTimeToken(string $authVerifier)
    {
        $url = TwitterApi::API_HOST . 'oauth/access_token';

        $oneTimeToken = $this->getOneTimeToken();
        $this->token = $this->auth->getLongTimeToken($url, $authVerifier, $oneTimeToken);
        $this->cache->set('twitter.long_token', $this->token->getKey());
        $this->cache->set('twitter.long_secret', $this->token->getSecret());
        return $token;
    }

    public function getLongTimeTokenFromCache()
    {
        return new Token($this->cache->get('twitter.long_token'),$this->cache->get('twitter.long_secret'));
    }

    private function fetchToken()
    {
        $url = TwitterApi::API_HOST . TwitterApi::API_TOKEN_REQUEST_METHOD;

        $this->deleteCachedToken();
        $this->token = $this->auth->requestToken($url);
        $this->cache->set('twitter.auth_token', $this->token->getKey());
        $this->cache->set('twitter.auth_secret', $this->token->getSecret());
    }

    public function getOneTimeToken(): Token
    {
        if ($this->cache->has('twitter.auth_token') && $this->cache->has('twitter.auth_secret')) {
            return new Token($this->cache->get('twitter.auth_token'), $this->cache->get('twitter.auth_secret'));
        }

        throw new Exception('one time token doesn\'t exists');
    }

    public function getToken()
    {
        if ($this->token) {
            return $this->token;
        }

        return new Token();
    }

    public function postTweet(string $content)
    {
        $token = $this->getLongTimeTokenFromCache();

        if (empty($content)) {
            throw new InvalidArgumentException('You can\'t tweet an empty content!');
        }

        if (empty($token->getKey())) {
            throw new InvalidArgumentException('The token is impossible to find');
        }

        $url = self::API_HOST . '1.1/statuses/update.json?status='.rawurlencode($content);

        $headers = [
            'Content-Type: multipart/form-data',
            'Authorization: ' . $this->auth->buildauthHeaders($url, 'POST')
        ];

        $this->client->post($url, $headers);
    }

    private function deleteCachedToken()
    {
        $this->cache->delete('twitter.auth_token');
        $this->cache->delete('twitter.auth_secret');
    }

    public function verifyCallbackToken(string $callbackToken)
    {
        $token = $this->getOneTimeToken();
        if ($callbackToken != $token->getKey()) {
            throw new \Exception('There is a problem!');
        }

        return $this;
    }
}
