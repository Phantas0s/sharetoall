<?php
declare(strict_types=1);

namespace App\Service\Api\OAuth1;

use App\Exception\Exception;
use App\Exception\OAuthException;
use App\Service\Api\Client\ClientInterface;
use App\Service\Api\TwitterApi;
use Psr\SimpleCache\CacheInterface;

class Auth
{
    const OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';
    const OAUTH_VERSION = '1.0';

    /** @var CacheInterface */
    private $cache;

    /** @var ClientInterface */
    private $client;

    /** @var Consumer */
    private $consumer;

    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var string identifier for creating different cache depending on API */
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

    public function fetchOnetimeToken(string $url): Token
    {
        $token = $this->requestToken($url);
        $this->cacheOnetimeToken($token);

        return $token;
    }

    private function requestToken(string $url): Token
    {
        $headers = [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => $this->buildOauthHeaders($url, 'POST', new Token())
        ];

        try {
            $response = $this->client->post($url, $headers);
        } catch (\Exception $e) {
            $this->handleOauthException($e);
        }

        $response = $response->getBodyAsArray();

        return new Token($response['oauth_token'], $response['oauth_token_secret']);
    }

    private function cacheOnetimeToken(Token $token)
    {
        $tokenKeyName = $this->apiName . '_onetime_token_key';
        $tokenSecretName = $this->apiName . '_onetime_token_secret';

        $this->cache->set($tokenKeyName, $token->getKey());
        $this->cache->set($tokenSecretName, $token->getSecret());
    }

    private function getCachedOnetimeToken(): Token
    {
        $tokenKeyName = $this->apiName . '_onetime_token_key';
        $tokenSecretName = $this->apiName . '_onetime_token_secret';

        if ($this->cache->has($tokenKeyName) && $this->cache->has($tokenSecretName)) {
            return new Token(
                $this->cache->get($tokenKeyName), $this->cache->get($tokenSecretName)
            );
        }

        throw new Exception('one time token doesn\'t exists');
    }

    public function getAuthUrl(string $url)
    {
        $token = $this->getCachedOnetimeToken();
        $params = [
            'oauth_token' => $token->getKey(),
            'force_login' => 'true',
        ];

        return $this->queryBuilder->createUrl($url, $params);
    }

    public function verifyCallbackToken(string $callbackToken)
    {
        $token = $this->getCachedOnetimeToken();
        if ($callbackToken != $token->getKey()) {
            throw new OAuthException('The token from the callback url is different than the one time token.');
        }
    }

    private function getOauthParameters(Token $token): array
    {
        $parameters = [
            'oauth_consumer_key' => $this->consumer->getKey(),
            'oauth_nonce' => $this->generateNonce(),
            'oauth_signature_method' => self::OAUTH_SIGNATURE_METHOD,
            'oauth_timestamp' => (string)gmdate('U', time()),
            'oauth_version' => self::OAUTH_VERSION
        ];

        if (!empty($token->getKey())) {
            $parameters['oauth_token'] = $token->getKey();
        }

        ksort($parameters);
        return $parameters;
    }

    public function buildOauthHeaders(
        string $url,
        string $method = 'GET',
        Token $token,
        array $parameters = []
    ): string {
        $parameters = array_merge($this->getOauthParameters($token), $parameters);
        $queryParameters = $this->queryBuilder->createUrlParameters($parameters);

        $signature = $this->buildSignature($url, $method, $token, $queryParameters);

        $parameterQueryParts = explode('&', $queryParameters);
        $parameterQueryParts[] = 'oauth_signature='.rawurlencode($signature);
        sort($parameterQueryParts);

        return 'OAuth '.implode(',', $parameterQueryParts);
    }

    public function getLongTimeToken(string $url, string $oAuthVerifier)
    {
        $onetimeToken = $this->getCachedOnetimeToken();

        $headers = [
            'Authorization' => $this->buildOauthHeaders($url, 'POST', $onetimeToken)
        ];

        $parameters = [
            'oauth_verifier' => $oAuthVerifier
        ];

        try {
            $response = $this->client->post($url, $headers, $parameters);
        } catch (Exception $e) {
            $this->handleOauthException($e);
        }

        $response = $response->getBodyAsArray();

        $token = new Token($response['oauth_token'], $response['oauth_token_secret']);
        $this->cacheLongTimeToken($token);
    }

    private function cacheLongTimeToken(Token $token)
    {
        $tokenKeyName = $this->apiName . '_longtime_token_key';
        $tokenSecretName = $this->apiName . '_longtime_token_secret';

        $this->cache->set($tokenKeyName, $token->getKey());
        $this->cache->set($tokenSecretName, $token->getSecret());
    }

    public function getCachedLongtimeToken(): Token
    {
        $tokenKeyName = $this->apiName . '_longtime_token_key';
        $tokenSecretName = $this->apiName . '_longtime_token_secret';

        if ($this->cache->has($tokenKeyName) && $this->cache->has($tokenSecretName)) {
            return new Token($this->cache->get($tokenKeyName), $this->cache->get($tokenSecretName));
        }

        throw new \Exception('long time token is not in the cache');
    }

    private function buildSignature(string $url, string $method, Token $token, string $queryParameters): string
    {
        $signatureString = strtoupper($method).'&'.rawurlencode($url).'&'.rawurlencode($queryParameters);
        $signatureKey = rawurlencode($this->consumer->getSecret()).'&';

        if (!empty($token->getSecret())) {
            $signatureKey .= rawurlencode($token->getSecret());
        }

        $signature = base64_encode(hash_hmac('sha1', $signatureString, $signatureKey, true));

        return $signature;
    }

    private function generateNonce(): string
    {
        return md5(microtime().mt_rand());
    }

    /**
     * @todo
     */
    private function handleOauthException(\Exception $e)
    {
        throw $e;
    }
}
