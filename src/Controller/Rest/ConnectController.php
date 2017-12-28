<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Traits\LoggerTrait;

use App\Exception\ApiException;
use App\Exception\Exception;
use App\Exception\InvalidArgumentException;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Service\Api\NetworkFactory;
use App\Service\Session;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Request;

class ConnectController extends EntityControllerAbstract
{
    use LoggerTrait;

    protected $modelName = 'Network';

    /** @var string */
    private $redirectUri;

    public function __construct(
        Session $session,
        ModelFactory $modelFactory,
        FormFactory $formFactory,
        NetworkFactory $networkFactory,
        string $redirectUri
    ) {
        parent::__construct($session, $modelFactory, $formFactory);
        $this->networkFactory = $networkFactory;
        $this->redirectUri = $redirectUri;
    }

    public function getAction(string $networkSlug, Request $request)
    {
        $networks = $this->model->findWithNetworkUser([
            'n.networkSlug' => $networkSlug, 'un.userId' => $this->session->getUserId()
        ]);

        if (count($networks->getAllResults()) > 0) {
            throw new InvalidArgumentException('This network has already a token');
        }

        $network = $this->networkFactory->create($networkSlug);

        $oneTimeToken = $this->session->createOneTimeToken();
        $redirectUri = $this->redirectUri . $networkSlug . '?t=' . $oneTimeToken;

        try {
            return $network->getAuthUrl($this->session->getUserId(), $redirectUri);
        } catch (ApiException $e) {
            $this->log(LogLevel::ERROR, $e->getMessage());
        }
    }
}
