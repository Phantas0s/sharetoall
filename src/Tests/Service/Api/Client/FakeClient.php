<?php

namespace App\Tests\Service\Api\Client;

use App\Service\Api\Client\ClientInterface;
use App\Service\Api\Client\Response;

class FakeClient implements ClientInterface
{
    /** @var array */
    private $responseBody;

    /** @var string */
    private $responseType;

    public function __construct(array $responseBody = [], string $responseType = 'form_params')
    {
        $this->responseType = $responseType;
        $this->responseBody = $responseBody;
    }

    public function post(string $url, array $header = [], array $body = [], string $bodyType = 'form_params'): Response
    {
        return $this->request();
    }

    public function get(string $url, array $header = [], array $body = []): Response
    {
        return $this->request();
    }

    private function request(): Response
    {
        $response = new Response(
            '200',
            [],
            $this->formatResponse()
        );

        return $response;
    }

    private function formatResponse()
    {
        if ($this->responseType == 'form_params') {
            return implode('&', $this->responseBody);
        }

        if ($this->responseType == 'json') {
            return json_encode($this->responseBody);
        }
    }
}
