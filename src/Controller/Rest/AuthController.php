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

            $this->mail->passwordReset($user, $token);
        } catch(InvalidArgumentException $e) {
            $error = $e->getMessage();
        }

        return $user;
    }

    public function postResetAction($resetToken, Request $request)
    {
        $form = $request->get('form');

        try {
            $user = $this->user->findByPasswordResetToken($resetToken);

            $password = $form['newPassword'];
            $passwordConfirm = $form['newPasswordConfirm'];

            if ($password == $passwordConfirm) {
                $user->updatePassword($password);
                $user->deletePasswordResetToken();
            } else {
                $error = 'Passwords do not match';
            }
        } catch(InvalidArgumentException $e) {
            $error = $e->getMessage();
        }
    }
}
