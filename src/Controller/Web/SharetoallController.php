<?php
declare(strict_types=1);

namespace App\Controller\Web;

use App\Exception\UnauthorizedException;
use App\Service\Session;

class SharetoallController
{
    public function indexAction()
    {
        return [
            'realm' => 'sharetoall'
        ];
    }
}

