<?php

namespace App\Controller\Web;

use App\Controller\Web\EntityControllerAbstract;
use App\Model\ModelFactory;
use App\Model\Network;
use App\Service\Api\TwitterApi;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends EntityControllerAbstract
{
    /** @var Session */
    private $session;

    /** @var Network */
    private $model;

    /** @var TwitterApi */
    private $twitterApi;

    public function __construct(Session $session, ModelFactory $modelFactory, TwitterApi $twitterApi)
    {
        $this->session = $session;
        $this->model = $modelFactory->create('Network');
        $this->twitterApi = $twitterApi;
    }

    public function twitterAction(Request $request)
    {
        if ($request->get('oauth_token') && $request->get('oauth_verifier')) {
            $cachedTokenUid = $this->session->getUserId();

            $this->twitterApi->verifyCallbackToken(
                $request->get('oauth_token'),
                $cachedTokenUid
            );

            $token = $this->twitterApi->getLongTimeToken(
                $request->get('oauth_verifier'),
                $cachedTokenUid
            );

            $this->model->saveUserNetwork([
                'userId' => $this->session->getUserId(),
                'networkSlug' => 'twitter',
                'userNetworkToken' => $token->getKey()
            ]);
        }

        return '/sharetoall#/dashboard';
    }
}
