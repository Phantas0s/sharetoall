<?php declare(strict_types=1);

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

    public function getAction(string $userId, Request $request): array
    {
        $userId = (int)$userId;
        $networks = $this->model->invalidateUserNetwork();
        $networks = $this->model->findAllNetworkByUserId($userId)->getAllResultsAsArray();

        return $networks;
    }
}
