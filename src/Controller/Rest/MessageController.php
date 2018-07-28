<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Traits\LoggerTrait;

use App\Exception\ApiException;
use App\Exception\Exception;
use App\Exception\NotFoundException;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Service\Api\NetworkFactory;
use App\Service\Api\NetworkFactoryInterface;
use App\Service\Api\OAuth1\Token;
use App\Service\Session;
use Doctrine\ActiveRecord\Search\SearchResult;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends EntityControllerAbstract
{
    use LoggerTrait;

    protected $modelName = 'Message';
    protected $createFormName = 'Message/CreateForm';

    /** @var NetworkFactory */
    private $networkFactory;

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

    public function postAction(Request $request): array
    {
        $message = $request->get('message');

        if (strlen($message) <= 0) {
            throw new NotFoundException('The message can\'t be empty.');
        }

        $networkSlug = $request->get('networkSlug');
        $network = $this->networkModel->findWithNetworkUser(['networkSlug' => $networkSlug])->getFirstResult();

        $networkApi = $this->networkFactory->create($networkSlug);

        if (empty($network->userNetworkTokenKey)) {
            throw new NotFoundException('Error: You need to reconnect to '.$networkSlug);
        }

        $token = new Token(
            $network->userNetworkTokenKey,
            $network->userNetworkTokenSecret
        );

        $response['network'] = $networkSlug;

        try {
            $response['response'] = $networkApi->postUpdate($message, $token);
        } catch (ApiException $e) {
            $message = sprintf(
                "can't send message %s to network with slug %s with exception %s",
                $message,
                $networkSlug,
                $e->getMessage()
            );
            $this->log(LogLevel::ERROR, $message);
            throw new ApiException($message);
        }

        return $response;
    }
}
