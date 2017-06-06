<?php

namespace App\Tests\Form\User;

use TestTools\TestCase\UnitTestCase;

class SearchFormTest extends UnitTestCase
{
    /**
     * @var \App\Form\User\SearchForm
     */
    protected $form;

    public function setUp()
    {
        $this->markTestSkipped(
            'User Form not implemented'
        );
        $container = $this->getContainer();
        $session = $container->get('service.session');
        $session->generateToken()->login('user@pantaflix.com', 'passwd', 2);
        $this->form = $container->get('form.factory')->create('User\Search', array('session' => $session));
    }

    public function testValidForm()
    {
        $this->markTestSkipped(
            'User Form not implemented'
        );
        $inputValues = array(
            'licensorId' => 2
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertFalse($this->form->hasErrors());
        $this->assertCount(0, $this->form->getErrors());

        $this->form->clearErrors()->setDefinedValues($inputValues);

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
            'licensorId' => 'f'
        );

        $this->form->setDefinedValues($inputValues);

        $this->form->validate();

        $this->assertTrue($this->form->hasErrors());

        $this->assertCount(1, $this->form->getErrors());
    }
}
