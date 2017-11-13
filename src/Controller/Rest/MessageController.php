<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\Exception;
use App\Exception\NotFoundException;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Service\Api\NetworkFactory;
use App\Service\Api\NetworkFactoryInterface;
use App\Service\Api\OAuth1\Token;
use App\Service\Session;
use Doctrine\ActiveRecord\Search\SearchResult;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends EntityControllerAbstract
{
    protected $modelName = 'Message';
    protected $createFormName = 'Message/CreateForm';

    /** @var NetworkFactory */
    private $NetworkFactory;

    /** @var Network */
    private $networkModel;

    /** @var Session */
    protected $session;

    public function __construct(
        Session $session,
        ModelFactory $modelFactory,
        FormFactory $formFactory,
        NetworkFactoryInterface $networkFactory
    ) {
        parent::__construct($session, $modelFactory, $formFactory);
        $this->networkFactory = $networkFactory;
        $this->networkModel = $modelFactory->create('Network');
        $this->session = $session;
    }

    public function postAction(Request $request)
    {
        $message = $request->get('message');

        $networkSlugs = $request->get('networkSlugs');

        $networks = $this->networkModel->findByNetworkUser($this->session->getUserId());
        $networks = $this->sortNetworks($networks->getAllResults());

        $results = [];
        foreach ($networkSlugs as $networkSlug) {
            try {
                $networkApi = $this->networkFactory->create($networkSlug);

                if (!isset($networks[$networkSlug]) || empty($networks[$networkSlug])) {
                    throw new NotFoundException('Impossible to find connect to the API');
                }

                $token = new Token(
                    $networks[$networkSlug]['key'],
                    $networks[$networkSlug]['secret']
                );

                $results[] = $networkApi->postUpdate($message, $token);
            } catch (\Exception $e) {
                return $e->getMessage();
                continue;
            }
        }

        return $results;
    }

    private function sortNetworks(array $networks): array
    {
        $results = [];
        foreach ($networks as $network) {
            $results[$network->networkSlug] = [
                'key' => $network->userNetworkTokenKey,
                'secret' => $network->userNetworkTokenSecret,
            ];
        }

        return $results;
    }
}
