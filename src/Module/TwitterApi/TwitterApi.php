<?php
declare(strict_types=1);

namespace App\Module\TwitterApi;

use App\Module\TwitterApi\Services\Http\ClientInterface;
use App\Module\TwitterApi\Services\Http\Consumer;
use App\Module\TwitterApi\Services\Http\OAuth;
use App\Module\TwitterApi\Services\Http\QueryBuilder;
use App\Module\TwitterApi\Services\Http\Token;

class TwitterApi
{
    private $client;

    private $consumer;

    private $oAuth;

    const API_HOST = 'https://api.twitter.com/';
    const API_VERSION = '1.0';
    const API_TIMEOUT = '1000';

    public function __construct(
        ClientInterface $client,
        string $consumerKey,
        string $consumerSecret
    ) {
        $this->client = $client;
        $this->consumer = new Consumer($consumerKey, $consumerSecret);
    }

    public function authenticate()
    {
        $this->oAuth = new OAuth($this->client, $this->consumer, new Token());
        $token = $this->oAuth->requestToken();

        return $token;
    }

    public function postTweet(Token $token, string $content)
    {
        if (empty($content)) {
            throw new InvalidArgumentException('You can\'t tweet an empty content!');
        }

        if (empty($token->getKey())) {
            throw new InvalidArgumentException('You can\'t tweet an empty content!');
        }

        $url = 'status/update';
        $parameters = [];

        $headers = [
            'Content-Type: multipart/form-data',
            'Authorization: ' . $this->oAuth->buildOauthHeaders($url)
        ];

        $this->client->send($headers, $content);
        $response = new Response();
    }
}
