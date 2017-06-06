<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

/**
 * Class Response
 */
class Response
{
    /**
     * @var string
     */
    private $statusCode;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $headers;

    /**
     * @param string $statusCode
     * @param string $body
     * @param string $headers
     */
    public function __construct(string $statusCode, string $body, string $headers)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = headers;
    }

    /**
     * @return string
     */
    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getHeaders():string
    {
        return $this->headers;
    }
}
