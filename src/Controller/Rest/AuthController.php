<?php

namespace App\Controller\Rest;

use App\Model\User;
use App\Service\Mail;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Request;

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

    public function postAction(Request $request): User
    {
        $email = $request->get('resetPasswordEmail');

        try {
            $user = $this->user->findByEmail($email);

            $this->mail->passwordReset($user);
        } catch(InvalidArgumentException $e) {
            $error = $e->getMessage();
        }

        return $user;
    }
}
