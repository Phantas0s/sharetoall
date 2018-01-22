<?php

namespace App\Tests\Form\User;

use TestTools\TestCase\UnitTestCase;

class RegisterFormTest extends UnitTestCase
{
    /**
     * @var \App\Form\User\RegisterForm
     */
    protected $form;

    public function setUp()
    {
        $this->form = $this->get('form.factory')->create('User\Register');
    }

    public function testValidForm()
    {
        $inputValues = array(
            'userEmail' => 'test@example.com',
            'userPassword' => 'es58bhst89e5',
            'userPasswordConfirm' => 'es58bhst89e5',
            'userNewsletter' => 0
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertFalse($this->form->hasErrors());
        $this->assertCount(0, $this->form->getErrors());
    }

    public function testInvalidForm()
    {
        $inputValues = array(
            'userEmail' => '',
            'userPassword' => '',
            'userPasswordConfirm' => '',
            'userNewsletter' => ''
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertTrue($this->form->hasErrors());

        $this->assertCount(3, $this->form->getErrors());
    }
}
