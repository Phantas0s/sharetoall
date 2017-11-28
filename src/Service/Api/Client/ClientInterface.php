<?php
declare(strict_types=1);

namespace App\Service\Api\Client;

/**
 * Interface ClientInterface
 */
interface ClientInterface
{
    public function get(string $url, array $headers = [], array $body = []): Response;
    public function post(string $url, array $headers = [], array $body = [], string $bodyType): Response;
}
