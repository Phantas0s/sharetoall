<?php

namespace App\Model;

use App\Dao\LicensorDao;
use App\Exception\InvalidArgumentException;
use App\Exception\NotFoundException;
use App\Form\User\RegisterForm;

/**
 * @see https://github.com/lastzero/doctrine-active-record
 */
class User extends ModelAbstract
{
    protected $_daoName = 'User';

    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERCOW = 'supercow';

    private $listRoles = [
        self::ROLE_OWNER,
        self::ROLE_ADMIN,
        self::ROLE_SUPERCOW
    ];

    public function register(RegisterForm $form)
    {
        $this->transactional(function () use ($form) {
            $userDao = $this->getEntityDao();

            $userDao->setValues($form->getValuesByTag('user'));

            $userDao->userPassword = $form->getPasswordHash();
            $userDao->userVerificationToken = $form->getVerificationToken();

            /** @var LicensorDao $licensorDao */
            $licensorDao = $this->createDao('Licensor');

            $licensorDao->setValues($form->getValuesByTag('licensor'));

            $licensorDao->save();

            $userDao->licensorId = $licensorDao->getId();

            $userDao->save();
        });
    }

    public function updatePassword($password)
    {
        if ($password == '') {
            throw new InvalidArgumentException('Password can not be empty');
        };

        if (!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $password)) {
            throw new InvalidArgumentException('Password is not secure');
        }

        // @codeCoverageIgnoreStart
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->getDao()->userPassword = $hash;
        $this->getDao()->update();
    }
    // @codeCoverageIgnoreEnd

    public function findByPasswordResetToken($token)
    {
        $users = $this->findAll(array('userPasswordToken' => $token));

        if (count($users) != 1) {
            throw new InvalidArgumentException('Invalid password reset token');
        }

        return $users[0];
    }

    public function findByVerificationToken($token)
    {
        $users = $this->findAll(array('userVerificationToken' => $token));

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

        $this->getDao()->userPasswordToken = $token;
        $this->getDao()->update();

        return $this;
    }

    public function deleteVerificationToken()
    {
        if ($this->getDao()->userVerificationToken) {
            $this->getDao()->userVerificationToken = null;
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
        if ($this->getDao()->userPasswordToken) {
            $this->getDao()->userPasswordToken = '';
            $this->getDao()->update();
        }

        return $this;
    }

    public function getLicensor()
    {
        $licensor = $this->createModel('Licensor')->find($this->licensorId);
        return $licensor;
    }

    public function passwordIsValid($password)
    {
        return password_verify($password, $this->userPassword);
    }

    public function getRoles(): array
    {
        return (array)$this->userRoles;
    }

    public function addRole(string $role)
    {
        if (!in_array($role, $this->listRoles)) {
            throw new NotFoundException('The role '.$role.' doesn\'t exists!');
        }

        $roles = $this->getRoles();

        $roles[] = $role;

        $this->getDao()->userRoles = $roles;
        $this->getDao()->update();

        return $this;
    }

    public function isAdmin()
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
    }

    public function isOwner()
    {
        return in_array(self::ROLE_OWNER, $this->getRoles());
    }

    public function isSuperCow()
    {
        return in_array(self::ROLE_SUPERCOW, $this->getRoles());
    }

    public function emailVerified()
    {
        if ($this->isAdmin()) {
            return true;
        }

        return !empty($this->userVerified);
    }
}
