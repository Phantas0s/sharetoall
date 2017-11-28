<?php
declare(strict_types=1);

namespace App\Controller\Web;

class SharetoallController extends EntityControllerAbstract
{
    public function indexAction()
    {
        return [
            'realm' => 'sharetoall'
        ];
    }
}
