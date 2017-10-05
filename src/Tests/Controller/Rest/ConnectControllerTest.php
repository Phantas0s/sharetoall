<?php

namespace App\Tests\Controller\Rest;

use App\Exception\InvalidArgumentException;
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
        $request = Request::create('https://dummyurl');
        $result = $this->controller->getAction('fake', $request);
        $this->assertEquals($result, 'http://dummyAuthUrl');
    }

    public function testGetActionTokenAlreadyThere()
    {
        $request = Request::create('https://dummyurl');
        $this->expectException(InvalidArgumentException::class);
        $result = $this->controller->getAction('supernetwork', $request);
    }
}
