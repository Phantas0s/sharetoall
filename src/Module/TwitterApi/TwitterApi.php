<?php
declare(strict_types=1);

namespace App\Module\TwitterApi;

use App\Module\TwitterApi\Services\Http\ClientInterface;
use App\Module\TwitterApi\Services\Http\Consumer;
use App\Module\TwitterApi\Services\Http\OAuth;
use App\Module\TwitterApi\Services\Http\QueryBuilder;
use App\Module\TwitterApi\Services\Http\Token;
use Psr\SimpleCache\CacheInterface;

class TwitterApi
{
    private $client;

    private $consumer;

    private $oAuth;

    private $token;

    /** @var CacheInterface */
    private $cache;

    const API_HOST = 'https://api.twitter.com/';
    const API_VERSION = '1.0';
    const API_TIMEOUT = '1000';

    public function __construct(
        CacheInterface $cache,
        ClientInterface $client,
        string $consumerKey,
        string $consumerSecret
    ) {
        $this->cache = $cache;
        $this->client = $client;
        $this->consumer = new Consumer($consumerKey, $consumerSecret);
        $this->oAuth = new OAuth($client, $this->consumer, $this->getToken());
    }

    public function getAuthUrl()
    {
        $this->fetchToken();
        return self::API_HOST . 'oauth/authenticate?oauth_token=' . $this->token->getKey().'&force_login=true';
    }

    public function getLongTimeToken(string $oAuthVerifier)
    {
        $oneTimeToken = $this->getOneTimeToken();
        $token = $this->oAuth->getLongTimeToken($oAuthVerifier, $oneTimeToken);
        return $token;
    }

    private function fetchToken()
    {
        $this->deleteCachedToken();
        $this->token = $this->oAuth->requestToken();
        $this->cache->set('twitter.oauth_token', $this->token->getKey());
        $this->cache->set('twitter.oauth_secret', $this->token->getSecret());
    }

    public function getOneTimeToken(): Token
    {
        if ($this->cache->has('twitter.oauth_token') && $this->cache->has('twitter.oauth_secret')) {
            return new Token($this->cache->get('twitter.oauth_token'), $this->cache->get('twitter.oauth_secret'));
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
        $token = $this->getToken();

        if (empty($content)) {
            throw new InvalidArgumentException('You can\'t tweet an empty content!');
        }

        if (empty($token->getKey())) {
            throw new InvalidArgumentException('The token is impossible to find');
        }

        $url = self::API_HOST . '1.1/statuses/update.json?status='.rawurlencode($content);

        $headers = [
            'Content-Type: multipart/form-data',
            'Authorization: ' . $this->oAuth->buildOauthHeaders($url, 'POST')
        ];

        $this->client->post($url, $headers);
    }

    private function deleteCachedToken()
    {
        $this->cache->delete('twitter.oauth_token');
        $this->cache->delete('twitter.oauth_secret');
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
