<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Service\Api\NetworkInterface;

interface NetworkFactoryInterface
{
    public function create(string $networkSlug): NetworkInterface;
}
