<?php

namespace App\Controller\Web;

use App\Exception\FormInvalidException;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Service\Api\Client\GuzzleClient;
use App\Service\Api\Client\Session;
use App\Service\Api\LinkedinApi;
use App\Service\Api\TwitterApi;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    protected $session;
    protected $formFactory;

    /** @var TwitterApi */
    private $twitterApi;

    private $formName = 'Message\Create';

    /** @var Token */
    private $token;

    /** @var LinkedinApi */
    private $linkedinApi;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function indexAction(Request $request)
    {
    }

    protected function createForm(string $name)
    {
        return $this->formFactory->create($name);
    }
}
