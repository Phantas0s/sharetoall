<?php

namespace App\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;

class ConnectController extends EntityControllerAbstract
{
    public function getAction(string $network, Request $request)
    {
        $network = $request->request->get('network');
    }
}
