<?php

namespace App\Tests\Form\Contact;

use TestTools\TestCase\UnitTestCase;

class CreateFormTest extends UnitTestCase
{
    /**
     * @var \App\Form\Message\CreateForm
     */
    protected $form;

    public function setUp()
    {
        $container = $this->getContainer();
        $session = $container->get('service.session');
        $this->form = $this->get('form.factory')->create('Contact\Create', ['session' => $session]);
    }

    public function testValidForm()
    {
        $inputValues = array(
            'email' => 'user@sharetoall.com',
            'message' => 'Woupi',
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertFalse($this->form->hasErrors());
        $this->assertCount(0, $this->form->getErrors());
    }

    public function testInvalidForm()
    {
        $inputValues = array(
            'email' => '',
            'message' => '',
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertTrue($this->form->hasErrors());

        $this->assertCount(2, $this->form->getErrors());
    }
}
