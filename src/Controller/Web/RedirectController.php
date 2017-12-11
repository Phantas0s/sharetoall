<?php
declare(strict_types=1);

namespace App\Controller\Web;

use App\Controller\Web\EntityControllerAbstract;
use App\Exception\NetworkErrorException;
use App\Exception\NotFoundException;
use App\Model\ModelFactory;
use App\Model\Network;
use App\Service\Api\LinkedinApi;
use App\Service\Api\TwitterApi;
use App\Service\Session;
use App\Service\Api\NetworkFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends EntityControllerAbstract
{
    /** @var Session */
    private $session;

    /** @var Network */
    private $model;

    /** @var string */
    private $redirectUri;

    /** @var TwitterApi */
    private $twitterApi;

    /** @var LinkedinApi */
    private $linkedinApi;

    private $dashboardUri = '/sharetoall#/dashboard';

    public function __construct(
        Session $session,
        ModelFactory $modelFactory,
        string $redirectUri,
        NetworkFactoryInterface $networkFactory
    ) {
        $this->session = $session;
        $this->model = $modelFactory->create('Network');
        $this->redirectUri = $redirectUri;

        $this->twitterApi = $networkFactory->create(TwitterApi::NETWORK_SLUG);
        $this->linkedinApi = $networkFactory->create(LinkedinApi::NETWORK_SLUG);
    }

    public function twitterAction(Request $request)
    {
        if (!$request->get('oauth_token') || !$request->get('oauth_verifier')) {
            throw new NotFoundException('The twitter api response miss oauth_token or oauth_verifier');
        }

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
            'networkSlug' => $this->twitterApi->getNetworkSlug(),
            'userNetworkTokenKey' => $token->getKey(),
            'userNetworkTokenSecret' => $token->getSecret(),
        ]);

        return $this->dashboardUri;
    }

    public function linkedinAction(Request $request)
    {
        if (!$request->get('state') || !$request->get('code')) {
            throw new NotFoundException('The linkedin redirect uri doesn\'t have the mandatory state parameter');
        }

        $state = $request->get('state');
        $code = $request->get('code');

        // @todo see https://developer.linkedin.com/docs/oauth2 to manage better the error
        $networkError = $request->get('error');
        if ($networkError) {
            throw new NetworkErrorException($networkError);
        }

        $cachedTokenUid = $this->session->getUserId();

        $this->linkedinApi->verifyCallbackToken(
            $state,
            $cachedTokenUid
        );

        $redirectUri = $this->redirectUri.'linkedin?t='.$request->get('t');
        $token = $this->linkedinApi->getLongTimeToken($code, $cachedTokenUid, $redirectUri);

        $this->model->saveUserNetwork([
            'userId' => $this->session->getUserId(),
            'networkSlug' => $this->linkedinApi->getNetworkSlug(),
            'userNetworkTokenKey' => $token->getKey(),
            'userNetworkTokenSecret' => '',
        ]);

        return $this->dashboardUri;
    }

}
