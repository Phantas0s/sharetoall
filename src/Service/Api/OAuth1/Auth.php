<?php
declare(strict_types=1);

namespace App\Service\Api\OAuth1;

use App\Service\Api\Client\ClientInterface;
use App\Service\Api\TwitterApi;

class Auth
{
    const API_OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';
    const API_OAUTH_VERSION = '1.0';

    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var Token */
    private $token;

    /** @var Consumer */
    private $consumer;

    /** @var ClientInterface */
    private $client;

    public function __construct(
        ClientInterface $client,
        Consumer $consumer,
        Token $token
    ) {
        $this->consumer = $consumer;
        $this->client = $client;
        $this->queryBuilder = new QueryBuilder();
        $this->token = $token;
    }

    public function requestToken(string $url)
    {
        $headers = [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => $this->buildOauthHeaders($url, 'POST')
        ];

        try {
            $response = $this->client->post($url, $headers);
        } catch (\Exception $e) {
            $this->handleOauthException($e);
        }

        $response = $response->getBodyAsArray();

        $this->token = new Token($response['oauth_token'], $response['oauth_token_secret']);
        return $this->token;
    }

    public function getOauthParameters()
    {
        $parameters = [
            'oauth_consumer_key' => $this->consumer->getKey(),
            'oauth_nonce' => $this->generateNonce(),
            'oauth_signature_method' => self::API_OAUTH_SIGNATURE_METHOD,
            'oauth_timestamp' => (string)time(),
            'oauth_version' => self::API_OAUTH_VERSION
        ];

        if (!empty($this->token->getKey())) {
            $parameters['oauth_token'] = $this->token->getKey();
        }

        ksort($parameters);
        return $parameters;
    }

    public function buildOauthHeaders(
        string $url,
        string $method = 'GET',
        array $parameters = []
    ): string {
        $parameters = array_merge($this->getOauthParameters(), $parameters);
        $queryParameters = $this->queryBuilder->createUrlParameters($parameters);
        $signature = $this->buildSignature($url, $method, $queryParameters);

        $parameterQueryParts = explode('&', $queryParameters);
        $parameterQueryParts[] = 'oauth_signature='.rawurlencode($signature);
        sort($parameterQueryParts);

        return 'OAuth '.implode(',', $parameterQueryParts);
    }

    public function getLongTimeToken(string $url, string $oAuthVerifier, Token $oneTimeToken)
    {
        $this->token = $oneTimeToken;

        $headers = [
            'Authorization' => $this->buildOauthHeaders($url, 'POST')
        ];

        $parameters = [
            'oauth_verifier' => $oAuthVerifier
        ];

        try {
            $response = $this->client->post($url, $headers,$parameters);
        } catch (\Exception $e) {
            $this->handleOauthException($e);
        }

        $response = $response->getBodyAsArray();

        $this->token = new Token($response['oauth_token'], $response['oauth_token_secret']);
        return $this->token;
    }

    private function buildSignature(string $url, string $method, string $queryParameters): string
    {
        $signatureString = strtoupper($method).'&'.rawurlencode($url).'&'.rawurlencode($queryParameters);
        $signatureKey = rawurlencode($this->consumer->getSecret()).'&';

        if (!empty($this->token->getSecret())) {
            $signatureKey .= rawurlencode($this->token->getSecret());
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
