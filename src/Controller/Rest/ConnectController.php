<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class ConnectController extends EntityControllerAbstract
{
    protected $modelName = 'NetworkUser';

    public function __construct(NetworkFactory $networkFactory)
    {
        $this->networkFactory = $networkFactory;
    }

    public function getAction(string $networkSlug, Request $request)
    {
        $network = $this->model->findWithNetworkUser([
            'networkSlug' => $networkSlug, 'userId' => $this->session->getUserId
        ]);

        if ($network->hasToken()) {
            throw new Exception('This network has already a token');
        }

        $network = $this->networkFactory->create($networkSlug);
    }
}
