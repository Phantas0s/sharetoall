<?php

namespace App\Controller\Web;

use App\Exception\FormInvalidException;
use App\Form\FormFactory;
use App\Model\ModelFactory;
use App\Service\Api\TwitterApi;
use App\Service\Api\Client\GuzzleClient;
use App\Service\Api\Client\Session;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    protected $session;
    protected $formFactory;
    private $twitterApi;

    private $formName = 'Message\Create';

    /** @var Token */
    private $token;

    public function __construct(FormFactory $formFactory, TwitterApi $twitterApi)
    {
        $this->formFactory = $formFactory;
        $this->twitterApi = $twitterApi;
    }

    public function indexAction(Request $request)
    {
        if ($request->get('oauth_token') && $request->get('oauth_verifier')) {
            $this->twitterApi->verifyCallbackToken(
                $request->get('oauth_token'),
                $request->get('oauth_verifier')
            );

            $this->token = $this->twitterApi->getLongTimeToken($request->get('oauth_verifier'));
        }

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
        $url = $this->twitterApi->getAuthUrl();
        return $url;
    }

    public function tweetAction()
    {
    }

    public function postTweetAction(Request $request)
    {
        $form = $this->createForm($this->formName);

        $form->setDefinedWritableValues($request->request->all())->validate();

        if ($form->hasErrors()) {
            throw new FormInvalidException($form->getFirstError());
        }

        $this->twitterApi->postTweet($request->get('messageContent'));
    }
}
