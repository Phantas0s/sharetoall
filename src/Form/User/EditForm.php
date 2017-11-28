<?php
declare(strict_types=1);

namespace App\Form\User;

use App\Form\FormAbstract;

/**
 * @see https://github.com/symlex/input-validation
 */
class EditForm extends FormAbstract
{
    protected function init(array $params = array())
    {
        $definition = [
            'userFirstname' => [
                'caption' => 'First Name',
                'type' => 'string',
                'min' => 2,
                'max' => 64,
                'required' => true,
            ],
            'userLastname' => [
                'caption' => 'Last Name',
                'type' => 'string',
                'min' => 2,
                'max' => 64,
                'required' => true,
            ],
            'userEmail' => [
                'caption' => 'E-Mail',
                'type' => 'email',
                'max' => 127,
                'required' => true,
            ]
       ];

        $this->setDefinition($definition);
    }
}
