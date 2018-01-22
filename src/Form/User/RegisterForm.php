<?php
declare(strict_types=1);

namespace App\Form\User;

use App\Form\FormAbstract;

/**
 * @see https://github.com/symlex/input-validation
 */
class RegisterForm extends FormAbstract
{
    private $_passwordHash;
    private $_verificationToken;

    protected function init(array $params = array())
    {
        $definition = [
            'userEmail' => [
                'caption' => $this->_('Email Address'),
                'type' => 'email',
                'max' => 127,
                'required' => true,
                'tags' => ['user']
            ],
            'userPassword' => [
                'type' => 'string',
                'caption' => $this->_('Password'),
                'required' => true,
                'min' => 6,
                'max' => 100,
                'tags' => ['user']
            ],
            'userPasswordConfirm' => [
                'type' => 'string',
                'caption' => $this->_('Password confirm'),
                'required' => true,
                'matches' => 'userPassword',
                'min' => 6,
                'max' => 100,
            ],
            'userNewsletter' => [
                'type' => 'bool',
                'caption' => $this->_('Newsletter'),
                'required' => false,
                'optional' => true,
                'tags' => ['user']
            ]
        ];

        $this->setDefinition($definition);
    }

    public function setPasswordHash(string $hash)
    {
        if ($this->_passwordHash) {
            throw new \SecurityException('Password hash already set');
        }

        if (strlen($hash) < 10) {
            throw new \SecurityException('Password hash is too short');
        }

        $this->_passwordHash = $hash;
    }

    public function getPasswordHash()
    {
        if (!$this->_passwordHash) {
            $this->setPasswordHash(password_hash($this->userPassword, PASSWORD_DEFAULT));
        }

        return $this->_passwordHash;
    }

    public function setVerificationToken(string $token)
    {
        if ($this->_verificationToken) {
            throw new \SecurityException('Verification token already set');
        }

        if (strlen($token) < 8) {
            throw new \SecurityException('Verification token is too short');
        }

        $this->_verificationToken = $token;
    }

    public function getVerificationToken()
    {
        if (!$this->_verificationToken) {
            $this->setVerificationToken(bin2hex(random_bytes(16)));
        }

        return $this->_verificationToken;
    }
}
