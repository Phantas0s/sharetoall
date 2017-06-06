<?php

namespace App\Tests\Form\User;

use TestTools\TestCase\UnitTestCase;

class EditFormTest extends UnitTestCase
{
    /**
     * @var \App\Form\User\EditForm
     */
    protected $form;

    public function setUp()
    {
        $this->form = $this->get('form.factory')->create('User\Edit');
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
            'admin' => true
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
            'admin' => ''
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertTrue($this->form->hasErrors());

        $this->assertCount(3, $this->form->getErrors());
    }
}
