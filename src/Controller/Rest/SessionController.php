<?php

namespace App\Controller\Rest;

use App\Exception\NotFoundException;
use App\Model\User;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Request;

class SessionController
{
    /** @var Session */
    protected $session;

    /** @var User */
    protected $user;

    protected $appVersion;

    protected $debug;

    protected $acpSubdomain;

    public function __construct(Session $session, User $user, $appVersion, $debug, $acpSubdomain)
    {
        $this->session = $session;
        $this->user = $user;
        $this->appVersion = $appVersion;
        $this->debug = $debug;
        $this->acpSubdomain = $acpSubdomain;
    }

    public function oneTimeTokenAction()
    {
        $result = $this->session->createOneTimeToken();

        return array('token' => $result);
    }

    public function deleteAction($sessionToken)
    {
        $this->session->setToken($sessionToken)->invalidate();
    }

    public function postAction(Request $request)
    {
        if($this->session->hasToken()) {
            $this->session->invalidate();
        }

        $this->session->generateToken();

        $result = array(
            'token' => $this->session->getToken(),
            'app_version' => $this->appVersion,
            'debug' => $this->debug
        );

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $slug = explode('.', $request->getHost())[0];
        $licensorId = null;

        if($slug != $this->acpSubdomain) {
            $licensor = $this->user->createModel('Licensor')->findEnabledBySlug($slug);
            $licensorId = $licensor->getId();
            $result['licensor'] = $licensor->getValues();
        }

        if($email) {
            $this->session->login($email, $password, $licensorId);
            $result['user'] = $this->session->getUser()->getValues();
        }

        return $result;
    }
}
