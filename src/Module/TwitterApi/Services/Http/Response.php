<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

class Response
{
    /** @var string */
    private $statusCode;

    /** @var string */
    private $body;

    /** @var string */
    private $headers;

    public function __construct(string $statusCode, string $body, string $headers)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = headers;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders():string
    {
        return $this->headers;
    }
}
