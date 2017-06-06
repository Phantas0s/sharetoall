<?php

namespace App\Form\Message;

use App\Form\FormAbstract;

/**
 * @see https://github.com/symlex/input-validation
 */
class CreateForm extends FormAbstract
{
    protected function init(array $params = array())
    {
        $definition = [
            'messageContent' => [
                'caption' => 'Message',
                'type' => 'string',
                'min' => 2,
                'max' => 64,
                'required' => true,
            ],
       ];

        $this->setDefinition($definition);
    }
}
