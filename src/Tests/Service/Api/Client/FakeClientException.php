<?php

namespace App\Tests\Service\Api\Client;

use App\Exception\ApiException;
use App\Service\Api\Client\ClientInterface;
use App\Service\Api\Client\Response;

class FakeClientException implements ClientInterface
{
    public function post(string $url, array $header = [], array $body = [], string $bodyType = 'form_params'): Response
    {
        throw new ApiException('Exception thrown for a test');
    }

    public function get(string $url, array $header = [], array $body = []): Response
    {
        throw new ApiException('Exception thrown for a test');
    }
}
