<?php
declare(strict_types=1);

namespace App\Tests\Form\Message;

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
        $session->generateToken()->login('user@sharetoall.com', 'password');
        $this->form = $this->get('form.factory')->create('Message\Create', ['session' => $session]);
    }

    public function testValidForm()
    {
        $inputValues = array(
            'messageContent' => 'hello',
            'userId' => '1',
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertFalse($this->form->hasErrors());
        $this->assertCount(0, $this->form->getErrors());
    }

    public function testInvalidForm()
    {
        $inputValues = array(
            'messageContent' => '',
            'userId' => '',
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertTrue($this->form->hasErrors());

        $this->assertCount(1, $this->form->getErrors());
    }
}
