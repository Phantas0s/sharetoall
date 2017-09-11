<?php

namespace App\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;

class NetworkController extends EntityControllerAbstract
{
    protected $modelName = 'Network';

    public function cgetAction(Request $request)
    {
        $networks = $this->model->findAll([], false);
        return $networks;
    }

    public function getAction($userId, Request $request)
    {
        $networks = $this->model->findWithNetworkUser($userId);
        return $networks;
    }
}
