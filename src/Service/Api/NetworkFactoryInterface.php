<?php

namespace App\Service\Api;

use App\Service\Api\NetworkInterface;

interface NetworkFactoryInterface
{
    public function create(string $networkSlug): NetworkInterface;
}
