<?php
declare(strict_types=1);

namespace App\Form\Contact;

use App\Form\FormAbstract;

/**
 * @see https://github.com/symlex/input-validation
 */
class CreateForm extends FormAbstract
{
    protected function init(array $params = array())
    {
        $definition = [
            'email' => [
                'caption' => $this->_('Email Address'),
                'type' => 'email',
                'max' => 127,
                'required' => true,
            ],
            'message' => [
                'caption' => $this->_('Message'),
                'type' => 'string',
                'min' => 2,
                'max' => 500,
                'required' => true,
            ],
        ];

        $this->setDefinition($definition);
    }
}
