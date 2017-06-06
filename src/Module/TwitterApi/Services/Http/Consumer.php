<?php

namespace App\Module\TwitterApi\Services\Http;

class Consumer
{
    private $key;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return "Consumer[key=$this->key,secret=$this->secret]";
    }
}
