<?php
declare(strict_types=1);

namespace App\Service\Api\OAuth2;

class Token
{
    private $key;

    private $ttl;

    public function __construct(string $key = '', int $ttl = 0)
    {
        $this->key = $key;
        $this->ttl = $ttl;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }
}
