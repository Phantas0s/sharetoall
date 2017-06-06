<?php

namespace App\Module\TwitterApi\Services\Http;

use App\Module\TwitterApi\Services\Http\Response;

/**
 * Interface ClientInterface
 */
interface ClientInterface
{
    public function callPost(string $url, array $headers, string $content);
}
