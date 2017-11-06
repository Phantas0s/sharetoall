<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\Exception;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Service\Api\NetworkFactory;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends EntityControllerAbstract
{
    protected $modelName = 'Message';
    protected $createFormName = 'Message/CreateForm';

    /** @var NetworkFactory */
    private $NetworkFactory;

    public function __construct(
        Session $session,
        ModelFactory $modelFactory,
        FormFactory $formFactory,
        NetworkFactory $networkFactory
    ) {
        parent::__construct($session, $modelFactory, $formFactory);
        $this->networkFactory = $networkFactory;
    }

    public function postAction(Request $request)
    {
        $message = $request->get('message');

        $networkSlugs = $request->get('networkSlugs');
        $networkSlugs = explode(',', $networkSlugs[0]);

        foreach ($networkSlugs as $networkSlug) {
            try {
                $network = $this->networkFactory->create($networkSlug);
                $network->postUpdate($message);
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
