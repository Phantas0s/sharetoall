<?php

namespace App\Command;

use App\Service\Mail;
use App\Model\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commands are configured as service in app/config/console.yml
 */
class UserResetPasswordCommand extends CommandAbstract
{
    protected $mail;
    protected $user;

    public function __construct($name, Mail $mail, User $user)
    {
        $this->mail = $mail;
        $this->user = $user;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Send password reset email to a user');

        $this->addArgument('email', InputArgument::REQUIRED, 'E-Mail');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');

        $user = $this->user->findByEmail($email);

        $this->mail->passwordReset($user);
    }
}
