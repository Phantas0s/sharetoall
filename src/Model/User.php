<?php declare(strict_types=1);

namespace App\Model;

use App\Exception\FormInvalidException;
use App\Exception\InvalidArgumentException;
use App\Exception\NotFoundException;
use App\Form\User\RegisterForm;

/**
 * @see https://github.com/lastzero/doctrine-active-record
 */
class User extends ModelAbstract
{
    protected $_daoName = 'User';

    public function updatePassword($password)
    {
        if ($password == '') {
            throw new InvalidArgumentException('Password can not be empty');
        };

        if (strlen($password) <= 6) {
            throw new InvalidArgumentException('Your password need to be at least 6 characters.');
        }

        // @codeCoverageIgnoreStart
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->getDao()->userPassword = $hash;
        $this->getDao()->update();
        // @codeCoverageIgnoreEnd
    }

    public function findByPasswordResetToken($token)
    {
        $users = $this->findAll(array('userResetPasswordToken' => $token));

        if (count($users) != 1) {
            throw new InvalidArgumentException('Invalid password reset token');
        }

        return $users[0];
    }

    public function findByVerificationToken($token)
    {
        $users = $this->findAll(array('userVerifEmailToken' => $token));

        if (count($users) != 1) {
            throw new InvalidArgumentException('Invalid verification token');
        }

        return $users[0];
    }


    public function findByEmail($email)
    {
        $users = $this->findAll(array('userEmail' => $email));

        if (count($users) != 1) {
            throw new InvalidArgumentException('User not found: ' . $email);
        }

        return $users[0];
    }

    public function setPasswordResetToken(string $token)
    {
        if(strlen($token) < 5) {
            throw new InvalidArgumentException('Password reset token is too short');
        }

        $this->getDao()->userResetPasswordToken = $token;
        $this->getDao()->update();

        return $this;
    }

    public function deleteVerificationToken()
    {
        if ($this->getDao()->userVerifEmailToken) {
            $this->getDao()->userVerifEmailToken = null;
            $this->getDao()->update();
        }

        return $this;
    }

    public function verifyEmail()
    {
        if (!$this->emailVerified()) {
            $currentDate = new \Datetime();
            $this->getDao()->userVerified = $currentDate->format('Y-m-d H:i:s');
            $this->getDao()->update();
        }
    }

    public function deletePasswordResetToken()
    {
        if ($this->getDao()->userResetPasswordToken) {
            $this->getDao()->userResetPasswordToken = '';
            $this->getDao()->update();
        }

        return $this;
    }

    public function passwordIsValid($password)
    {
        return password_verify($password, $this->userPassword);
    }

    public function passwordIsConfirmed($password, $confirmPassword)
    {
        if($password !== $confirmPassword) {
            return false;
        }

        return true;
    }

    public function emailVerified()
    {
        return !empty($this->userVerified);
    }

    public function getId(): int
    {
        return (int)$this->userId;
    }
}
