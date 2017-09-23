<?php

namespace App\Tests\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class ConnectControllerTest extends UnitTestCase
{
    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.rest.connect');
    }

    public function testGetAction()
    {
        $this->markTestSkipped('find a way to inject the dummy client?');
        $request = Request::create('https://dummyurl');
        $result = $this->controller->getAction('twitter', $request);
    }
}
