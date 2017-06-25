<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

use App\Module\TwitterApi\TwitterApi;

class OAuth
{
    const API_TOKEN_REQUEST_METHOD = 'oauth/request_token';
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
        $this->token = $token;

        $this->queryBuilder = new QueryBuilder();
    }

    public function requestToken()
    {
        $url = TwitterApi::API_HOST . self::API_TOKEN_REQUEST_METHOD;

        $headers = ['headers' => [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => $this->buildOauthHeaders($url, 'POST', $this->getOauthParameters())
        ]];

        $response = $this->client->callPost($url, $headers);
        return (string)$response;
    }

    public function getOauthParameters()
    {
        $parameters = [
            'oauth_consumer_key' => $this->consumer->getKey(),
            'oauth_nonce' => $this->generateNonce(),
            'oauth_signature_method' => self::API_OAUTH_SIGNATURE_METHOD,
            'oauth_timestamp' => (string)time(),
            "oauth_version" => self::API_OAUTH_VERSION
        ];

        if (!empty($this->token->getKey())) {
            $parameters['oauth_token'] = (string)$this->token->getKey();
        }

        return $parameters;
    }

    private function generateNonce(): string
    {
        return md5(microtime().mt_rand());
    }

    public function buildOauthHeaders(
        string $url,
        string $method = 'GET',
        array $parameters = []
    ): string {
        $queryParameters = $this->queryBuilder->createUrlParameters($parameters);
        $signature = $this->buildSignature($url, $method, $queryParameters);

        $parameterQueryParts = explode('&', $queryParameters);
        $parameterQueryParts[] = 'oauth_signature='.rawurlencode($signature);
        sort($parameterQueryParts);

        return 'OAuth '.implode(',', $parameterQueryParts);
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
}
