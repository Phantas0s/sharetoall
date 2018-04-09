<?php declare(strict_types=1);

namespace App\Controller\Web;

use App\Service\Mail;
use App\Service\Session;
use App\Model\User;
use App\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see https://github.com/lastzero/symlex#controllers
 */
class AuthController
{
    protected $session;
    protected $user;

    public function __construct(Session $session, User $user, Mail $mail)
    {
        $this->session = $session;
        $this->user = $user;
        $this->mail = $mail;
    }

    public function postLoginAction(Request $request)
    {
        $session_token = $request->get('session_token');

        $result = array(
            'page_name' => 'Login',
            'realm' => 'web',
        );

        try {
            $this->session->setToken($session_token);
            $this->session->getUserId();

            return '/sharetoall';
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return '/';
    }

    public function confirmEmailAction($token)
    {
        try {
            $user = $this->user->findByVerificationToken($token);
        } catch (\Exception $e) {
            return '/';
        }

        if ($this->session->hasToken()) {
            $this->session->invalidate();
        }

        $user->deleteVerificationToken()->verifyEmail();
        $this->session->generateToken()->setUserId($user->getId());

        $result = [
            'realm' => 'confirm',
            'page_name' => 'Email confirmation',
            'token' => $this->session->getToken(),
            'user' => json_encode($user->getValues()),
        ];

        return $result;
    }
}
