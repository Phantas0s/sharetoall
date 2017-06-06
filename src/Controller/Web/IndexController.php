<?php

namespace App\Controller\Web;

use App\Exception\FormInvalidException;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Module\TwitterApi\TwitterApi;
use App\Module\TwitterApi\Services\Http\GuzzleClient;
use App\Service\Session;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see https://github.com/lastzero/symlex#controllers
 */
class IndexController
{
    protected $session;
    protected $formFactory;

    private $formName = 'Message\Create';

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function indexAction()
    {
        $messageForm = $this->formFactory->create($this->formName);

        $result = [
            'form' => $messageForm->getAsArray()
        ];

        return $result;
    }

    protected function createForm(string $name)
    {
        return $this->formFactory->create($name);
    }

    public function postIndexAction(Request $request)
    {
        $form = $this->createForm($this->formName);

        $form->setDefinedWritableValues($request->request->all())->validate();

        if ($form->hasErrors()) {
            throw new FormInvalidException($form->getFirstError());
        }

        $twitterApi = new TwitterApi(
            new GuzzleClient(),
            'o9WYRPTW6PHEcDcjMVHgoLsLp',
            '8D8Xemn4ntTVmLFReUNwQovqck5uiNYkxKwAO6rFNC3dN5IUcP'
        );

        $token = $twitterApi->authenticate();

        $formValue = $form->getValues();

        return ['token' => $token];
    }
}
