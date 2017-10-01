<?php

namespace App\Service\Api;

/** Interface NetworkInterface */
interface NetworkInterface
{
    public function getAuthUrl(int $uid, string $redirectUri);
}
