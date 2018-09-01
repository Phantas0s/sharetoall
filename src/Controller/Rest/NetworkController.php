<?php declare(strict_types=1);

namespace App\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        // Invalidate user network which have token expiration date
        // TODO: do that via CRON?
        $networks = $this->model->invalidateUserNetwork();
        $networks = $this->model->findAllNetworkByUserId($userId);
        return $this->model->mapNetworksToFrontend($networks);
    }

    public function deleteAction(string $userId, string $networkSlug, Request $request): Response
    {
        $userId = (int)$userId;
        $this->model->deleteUserNetwork($userId, $networkSlug);

        return new Response("", 202);
    }
}
