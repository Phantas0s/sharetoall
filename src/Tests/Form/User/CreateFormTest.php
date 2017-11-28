<?php

namespace App\Tests\Form\User;

class CreateFormTest extends EditFormTest
{
    /**
     * @var \App\Form\User\CreateForm
     */
    protected $form;

    public function setUp()
    {
        $this->markTestSkipped(
            'User Form not implemented'
        );
        $this->form = $this->get('form.factory')->create('User\Create');
    }
}
