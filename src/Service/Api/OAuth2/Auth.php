<?php
declare(strict_types=1);

namespace App\Service\Api\OAuth2;

use App\Service\Api\Client\ClientInterface;
use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\OAuth1\QueryBuilder;
use App\Service\Api\OAuth2\Token;
use Psr\SimpleCache\CacheInterface;

class Auth
{
    /** @var CacheInterface */
    private $cache;

    /** @var ClientInterface */
    private $client;

    /** @var Consumer */
    private $consumer;

    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var string Name of the api to qualify the cache keys */
    private $apiName;

    public function __construct(
        CacheInterface $cache,
        ClientInterface $client,
        Consumer $consumer,
        string $apiName
    ) {
        $this->cache = $cache;
        $this->consumer = $consumer;
        $this->client = $client;

        $this->queryBuilder = new QueryBuilder();
    }

    public function getAuthUrl(string $url)
    {
        $token = rawurlencode($this->generateNonce());
        $params = [
            'response_type' => 'code',
            'client_id' => rawurlencode($this->consumer->getKey()),
            'redirect_uri' => 'http://sharetoall.loc',
            'state' => $token
        ];

        $this->cacheOnetimeToken($token);

        return $this->queryBuilder->createUrl($url, $params);
    }

    public function verifyCallbackToken(string $callbackToken)
    {
        $token = $this->getCachedOnetimeToken();
        if ($callbackToken != $token->getKey()) {
            // should be 401?
            throw new OAuthException('The token from the callback url is different than the token sent');
        }
    }

    private function getCachedOnetimeToken(): Token
    {
        if ($this->cache->has($this->apiName . '_oauth2_onetime_token')) {
            return new Token($this->cache->get($this->apiName . '_oauth2_onetime_token'));
        }

        throw new Exception('one time token doesn\'t exists');
    }

    private function cacheOnetimeToken(string $token)
    {
        $this->cache->set($this->apiName. '_oauth2_onetime_token', $token);
    }

    public function getLongTimeToken(string $url, string $oneTimeToken)
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $parameters = [
            'grant_type' => 'authorization_code',
            'code' => $oneTimeToken,
            'redirect_uri' => 'http://sharetoall.loc',
            'client_id' => $this->consumer->getKey(),
            'client_secret' => $this->consumer->getSecret()
        ];

        try {
            $response = $this->client->post($url, $headers, $parameters);
        } catch (\Exception $e) {
            $this->handleOauthException($e);
        }

        $response = $response->getBodyAsArray();

        $response = json_decode(array_keys($response)[0], true);

        $token = new Token($response['access_token'], $response['expires_in']);
        $this->cacheLongTimeToken($token);
    }

    private function cacheLongTimeToken(Token $token)
    {
        $tokenKeyName = $this->apiName . '_oauth2_longtime_token_key';
        $tokenKeyTtl = $this->apiName . '_oauth2_longtime_token_ttl';
        $this->cache->set($tokenKeyName, $token->getKey());
        $this->cache->set($tokenKeyTtl, $token->getTtl());
    }

    public function getCachedLongtimeToken(): Token
    {
        $tokenKeyName = $this->apiName . '_oauth2_longtime_token_key';
        $tokenKeyTtl = $this->apiName . '_oauth2_longtime_token_ttl';

        if ($this->cache->has($tokenKeyName) && $this->cache->has($tokenKeyTtl)) {
            return new Token($this->cache->get($tokenKeyName), (int)$this->cache->get($tokenKeyTtl));
        }

        throw new \Exception('long time token is not in the cache');
    }

    private function generateNonce(): string
    {
        return md5(microtime().mt_rand());
    }

    /**
     * @todo \Exception $e log the error
     */
    public function handleOauthException(\Exception $e)
    {
        throw $e;
    }
}
