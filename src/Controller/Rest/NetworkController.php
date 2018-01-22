<?php
declare(strict_types=1);

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

    public function getAction(string $userId, Request $request)
    {
        $networks = $this->model->findAllNetworkByUserId((int)$userId);
        return $networks->getAllResultsAsArray();
    }
}
