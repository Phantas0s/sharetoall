<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Licensor;
use App\Model\User;
use Swift_Mailer;
use Swift_Message as Message;
use Twig_Environment;

class Mail
{
    protected $mailer;
    protected $twig;
    protected $from;

    public function __construct(
        Swift_Mailer $mailer,
        Twig_Environment $twig,
        $from
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    public function createNewMessage($subject, array $recipients, $templateName, array $values = array())
    {
        $body = $this->twig->render('email/' . $templateName . '.twig', $values);

        $message = Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->from)
            ->setTo($recipients)
            ->setContentType("text/html")
            ->setBody($body);

        return $message;
    }

    protected function getRandomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); //turn the array into a string
    }

    public function passwordReset(User $user)
    {
        $token = sha1(random_bytes(32));

        $user->setPasswordResetToken($token);

        $values = array(
            'email' => $user->userEmail,
            'token' => $token,
        );

        $message = $this->createNewMessage('Password Reset', array($user->userEmail), 'password', $values);

        $this->mailer->send($message);
    }

    public function welcome(User $user)
    {
        $values = array(
            'firstname' => $user->userFirstname,
            'lastname' => $user->userLastname,
            'email' => $user->userEmail,
        );

        $message = $this->createNewMessage('Welcome', array($user->userEmail), 'welcome', $values);

        return $this->mailer->send($message);
    }

    public function confirmEmail(User $user)
    {
        $token = $user->userVerifEmailToken;

        $values = [
            'email' => $user->userEmail,
            'token' => $token,
        ];

        $message = $this->createNewMessage(
            'Welcome',
            [$user->userEmail],
            'confirm_email',
            $values
        );

        return $this->mailer->send($message);
    }

    public function contact(string $email, string $message)
    {
        $values = [
            'email' => $email,
            'message' => $message
        ];

        $message = $this->createNewMessage(
            'Sharetoall - contact form',
            ["sharetoall@gmail.com"],
            'contact',
            $values
        );

        return $this->mailer->send($message);
    }
}
