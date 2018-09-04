<?php
declare(strict_types=1);

namespace App\Controller\Web;

use App\Traits\LoggerTrait;

use App\Controller\Web\EntityControllerAbstract;
use App\Exception\ApiException;
use App\Exception\NetworkErrorException;
use App\Exception\NotFoundException;
use App\Model\ModelFactory;
use App\Model\Network;
use App\Service\Api\LinkedinApi;
use App\Service\Api\NetworkFactoryInterface;
use App\Service\Api\OAuth2\Token;
use App\Service\Api\TwitterApi;
use App\Service\Session;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController
{
    use LoggerTrait;

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

        $ui = $this->twitterApi->getUserInfo($token);
        $userInfo = json_decode($ui->getBody(), true);

        $this->model->saveUserNetwork([
            'userId' => $this->session->getUserId(),
            'userNetworkAccount' => isset($userInfo["screen_name"]) ? $userInfo["screen_name"] : null,
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

        // TODO see https://developer.linkedin.com/docs/oauth2
        // to manage better the LogLevel::ERROR
        $networkError = $request->get('error');
        if ($networkError) {
            throw new NetworkErrorException($networkError);
            $this->log(LogLevel::ERROR, 'Error from Linkedin Api: '.$newtworkError);
        }

        $cachedTokenUid = $this->session->getUserId();

        $this->linkedinApi->verifyCallbackToken(
            $state,
            $cachedTokenUid
        );

        $redirectUri = $this->redirectUri.'linkedin?t='.$request->get('t');

        try {
            $token = $this->linkedinApi->getLongTimeToken($code, $cachedTokenUid, $redirectUri);
        } catch (ApiException $e) {
            $this->log(LogLevel::ERROR, $e->getMessage());
            return $this->dashboardUri;
        }

        $now = new \DateTime();
        $now->modify(sprintf("+ %d seconds", $token->getTtl()));
        // -1 hour to be sure every messages to linkedin are properly processed
        $expire = $now->modify("-1 hour")->format('Y-m-d H:i:s');

        $ui = $this->linkedinApi->getUserInfo($token->getKey());
        $userInfo = json_decode($ui->getBody(), true);

        $name = null;
        if (isset($userInfo["firstName"]) && isset($userInfo["lastName"])) {
            $name = sprintf("%s %s", $userInfo["firstName"], $userInfo["lastName"]);
        }

        $this->model->saveUserNetwork([
            'userId' => $this->session->getUserId(),
            'userNetworkAccount' => $name,
            'networkSlug' => $this->linkedinApi->getNetworkSlug(),
            'userNetworkTokenKey' => $token->getKey(),
            'userNetworkTokenSecret' => '',
            'UserNetworkTokenExpire' => $expire
        ]);

        return $this->dashboardUri;
    }
}
