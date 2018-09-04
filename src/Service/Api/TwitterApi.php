<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Service\Api\Client\ClientInterface;
use App\Service\Api\OAuth1\Auth;
use App\Service\Api\OAuth1\Consumer;
use App\Service\Api\OAuth1\QueryBuilder;
use App\Service\Api\OAuth1\Token;

use App\Exception\ApiException;
use App\Exception\ApiMessageException;
use App\Exception\ApiStatusCodeException;
use Psr\SimpleCache\CacheInterface;

class TwitterApi implements NetworkInterface
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
    const API_GET_USER_INFO_METHOD = 'account/settings.json';

    const NETWORK_SLUG = 'twitter';

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

    public function getNetworkSlug(): string
    {
        return self::NETWORK_SLUG;
    }

    public function getAuthUrl(int $uid, string $redirectUri = '/'): string
    {
        $tokenUrl = self::API_HOST . self::API_TOKEN_REQUEST_METHOD;
        $authUrl = self::API_HOST . self::API_TOKEN_AUTHORISE_APP_METHOD;

        $this->auth->fetchOnetimeToken($tokenUrl, $uid, $redirectUri);
        return $this->auth->getAuthUrl($authUrl, $uid);
    }

    public function getLongTimeToken(string $authVerifier, int $uid): Token
    {
        $url = self::API_HOST . self::API_TOKEN_FETCH_LONGTIME_METHOD;
        $token = $this->auth->getLongTimeToken($url, $authVerifier, $uid);

        return $token;
    }

    public function verifyCallbackToken(string $callbackToken, int $uid)
    {
        return $this->auth->verifyCallbackToken($callbackToken, $uid);
    }

    public function getUserInfo(Token $token)
    {
        if (empty($token->getKey()) || empty($token->getSecret())) {
            throw new InvalidArgumentException('impossible to find token');
        }

        $url = self::API_HOST . self::API_VERSION . '/' . self::API_GET_USER_INFO_METHOD;

        $headers = [
            'Authorization' =>  $this->auth->buildOauthHeaders($url, 'GET', $token)
        ];

        try {
            $response = $this->client->get($url, $headers);
        } catch (\Exception $e) {
            throw new ApiMessageException($e->getMessage());
        }

        if ($response->getStatusCode() != 200) {
            throw new ApiStatusCodeException(
                sprintf(
                    'Wrong status code: %d with body %s',
                    $response->getStatusCode(),
                    (string)$response->getBody()
                )
            );
        }

        return $response;
    }

    public function postUpdate(string $content, Token $token)
    {
        if (empty($content)) {
            throw new InvalidArgumentException('You can\'t tweet an empty content!');
        }

        if (empty($token->getKey()) || empty($token->getSecret())) {
            throw new InvalidArgumentException('The token is impossible to find');
        }

        $parameters = ['status' => $content];
        $url = self::API_HOST . self::API_VERSION . '/' . self::API_POST_TWEET_METHOD;

        $headers = [
            'Authorization' =>  $this->auth->buildOauthHeaders($url, 'POST', $token, $parameters)
        ];

        try {
            $response = $this->client->post($url, $headers, $parameters);
        } catch (\Exception $e) {
            throw new ApiMessageException($e->getMessage());
        }

        if ($response->getStatusCode() != 200) {
            throw new ApiStatusCodeException(
                sprintf(
                    'Wrong status code: %d with body %s',
                    $response->getStatusCode(),
                    (string)$response->getBody()
                )
            );
        }

        return $response;
    }
}
