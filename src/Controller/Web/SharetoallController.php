<?php

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
