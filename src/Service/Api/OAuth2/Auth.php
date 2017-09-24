<?php
declare(strict_types=1);

namespace App\Service\Api\OAuth2;

use App\Exception\Exception;
use App\Exception\NotFoundException;
use App\Exception\OAuthException;
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

    public function getAuthUrl(string $url, int $uid, string $redirectUri = '/', array $params = []): string
    {
        $params = array_merge([
            'response_type' => 'code',
            'client_id' => rawurlencode($this->consumer->getKey()),
            'redirect_uri' => $redirectUri,
            'state' => rawurlencode($this->generateNonce())
        ], $params);

        $this->cacheOnetimeToken($params['state'], $uid);

        return $this->queryBuilder->createUrl($url, $params);
    }

    public function verifyCallbackToken(string $callbackToken, int $uid)
    {
        $token = $this->getCachedOnetimeToken($uid);

        if ($callbackToken != $token->getKey()) {
            // should be 401?
            throw new OAuthException('The token from the callback url is different than the token sent');
        }
    }

    private function getCachedOnetimeToken(int $uid): Token
    {
        $tokenKey = $uid . '-' . $this->apiName . '_oauth2_onetime_token';
        if ($this->cache->has($tokenKey)) {
            return new Token($tokenKey);
        }

        throw new NotFoundException('one time token doesn\'t exists');
    }

    private function cacheOnetimeToken(string $token, int $uid)
    {
        $this->cache->set($uid . '-' . $this->apiName. '_oauth2_onetime_token', $token);
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
    private function handleOauthException(\Exception $e)
    {
        throw $e;
    }
}
