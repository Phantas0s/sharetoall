<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

/**
 * Interface ClientInterface
 */
interface ClientInterface
{
    public function post(string $url, array $headers = [], array $body = []): Response;
}
