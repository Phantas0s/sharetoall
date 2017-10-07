<?php
declare(strict_types=1);

namespace App\Service\Api;

/** Interface NetworkInterface */
interface NetworkInterface
{
    public function getNetworkSlug(): string;
    public function getAuthUrl(int $uid, string $redirectUri): string;
}
