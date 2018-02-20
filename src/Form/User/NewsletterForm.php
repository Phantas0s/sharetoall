<?php declare(strict_types=1);

namespace App\Form\User;

use App\Form\FormAbstract;

/**
 * @see https://github.com/symlex/input-validation
 */
class NewsletterForm extends FormAbstract
{
    protected function init(array $params = array())
    {
        $definition = [
            'newsletterEmail' => [
                'caption' => $this->_('Email Address'),
                'type' => 'email',
                'max' => 127,
                'required' => true,
            ],
        ];

        $this->setDefinition($definition);
    }
}
