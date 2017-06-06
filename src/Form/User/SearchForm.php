<?php

namespace App\Form\User;

use App\Form\FormAbstract;

/**
 * @see https://github.com/symlex/input-validation
 */
class SearchForm extends FormAbstract {
    protected function init(array $params = [])
    {
        $definition = [
            'licensorId' => [
                'type' => 'int',
                'hidden' => true,
                'readonly' => true,
                'caption' => 'Licensor',
                'default' => $this->getParam('session')->getLicensorId()
            ]
        ];

        $this->setDefinition($definition);
    }
}
