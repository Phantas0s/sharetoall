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
        $this->markTestSkipped(
            'User Form not implemented'
        );
        $inputValues = array(
            'userFirstname' => 'Jens',
            'userLastname' => 'Mander',
            'userEmail' => 'test@example.com',
            'userEmailConfirm' => 'test@example.com',
            'userPassword' => 'es58bhst89e5',
            'licensorName' => 'Foo Bar GmbH',
            'licensorSlug' => 'foobar',
            'userTermsAccepted' => 1,
            'userNewsletter' => 0
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertFalse($this->form->hasErrors());
        $this->assertCount(0, $this->form->getErrors());
    }

    public function testInvalidForm()
    {
        $this->markTestSkipped(
            'User Form not implemented'
        );
        $inputValues = array(
            'userFirstname' => '',
            'userLastname' => '',
            'userEmail' => 'testexample.com',
            'userEmailConfirm' => 'test@example.com',
            'userPassword' => '2423',
            'licensorName' => '',
            'licensorSlug' => 'foo bar!',
            'userTermsAccepted' => 0,
            'userNewsletter' => array()
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertTrue($this->form->hasErrors());

        $this->assertCount(8, $this->form->getErrors());
    }
}
