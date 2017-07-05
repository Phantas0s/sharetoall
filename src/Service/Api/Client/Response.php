<?php
declare(strict_types=1);

namespace App\Service\Api\Client;

class Response
{
    /** @var int */
    private $statusCode;

    /** @var array */
    private $headers;

    /** @var string */
    private $body;

    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getBodyAsArray(): array
    {
        parse_str($this->body, $body);
        return $body;
    }


    public function getHeaders(): array
    {
        return $this->headers;
    }
}
