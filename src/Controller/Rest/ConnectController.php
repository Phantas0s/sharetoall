<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\Exception;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Service\Api\NetworkFactory;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Request;

class ConnectController extends EntityControllerAbstract
{
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

        if (!empty($networks)) {
            throw new Exception('This network has already a token');
        }

        $network = $this->networkFactory->create($networkSlug);

        $oneTimeToken = $this->session->createOneTimeToken();
        $redirectUri = $this->redirectUri . $networkSlug . '?t=' . $oneTimeToken;

        return $network->getAuthUrl($this->session->getUserId(), $redirectUri);
    }
}
