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

    public function __construct(FormFactory $formFactory, TwitterApi $twitterApi, LinkedinApi $linkedinApi)
    {
        $this->formFactory = $formFactory;
        $this->twitterApi = $twitterApi;
        $this->linkedinApi = $linkedinApi;
    }

    public function indexAction(Request $request)
    {
        //Twitter auth
        if ($request->get('oauth_token') && $request->get('oauth_verifier')) {
            // prevent csrf attacks
            $this->twitterApi->verifyCallbackToken(
                $request->get('oauth_token')
            );

            $this->token = $this->twitterApi->getLongTimeToken($request->get('oauth_verifier'));
        }

        //Linkedin auth
        if ($request->get('state')) {
            // prevent csrf attacks
            $this->linkedinApi->verifyCallbackToken(
                $request->get('state')
            );

            // see https://developer.linkedin.com/docs/oauth2 to manage better the error
            if ($request->get('error')) {
            }

            if ($request->get('code')) {
                $this->token = $this->linkedinApi->getLongTimeToken($request->get('code'));
            }

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

        $this->linkedinApi->postUpdate($request->get('messageContent'));
        $this->twitterApi->postTweet($request->get('messageContent'));
    }

    public function linkedinAction()
    {
    }

    public function postLinkedinAction()
    {
        $url = $this->linkedinApi->getAuthUrl();
        return $url;
    }
}
