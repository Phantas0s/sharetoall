<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

class Consumer
{
    /** @var string */
    private $key;

    /** @var string */
    private $secret;

    public function __construct(string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}
