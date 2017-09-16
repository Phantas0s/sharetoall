<?php

namespace App\Tests\Controller\Rest;

use App\Service\Session;
use TestTools\TestCase\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

class SessionControllerTest extends UnitTestCase
{
    protected $controller;
    protected $session;

    public function setUp()
    {
        $container = $this->getContainer();
        // $this->session = $container->get('service.session');
        // $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.rest.session');
    }

    public function testPostAction()
    {
        $request = Request::create('http://sharetoall.com');
        $result = $this->controller->postAction($request);
        var_dump($result);

        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('app_version', $result);
        $this->assertArrayHasKey('debug', $result);

        $this->assertTrue($result['debug']);
        $this->assertEquals('1.2.3', $result['app_version']);
    }
}
