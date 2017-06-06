<?php

namespace App\Tests\Controller\Web;

use App\Service\Session;
use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class AuthControllerTest extends UnitTestCase
{
    /** @var \App\Controller\Web\AuthController */
    protected $controller;

    /** @var Session */
    protected $session;

    public function setUp()
    {
        $this->markTestSkipped(
            'Auth session not implemented'
        );
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken();
        $this->controller = $container->get('controller.web.auth');
    }

    public function testLoginAction()
    {
        $this->markTestSkipped(
            'Auth session not implemented'
        );

        $result = $this->controller->loginAction();
        $this->assertArrayHasKey('page_name', $result);
    }

    public function testResetAction () {
        $this->markTestSkipped(
            'Auth session not implemented'
        );
        $result = $this->controller->resetAction();

        $expected = array(
            'email' => '',
            'error' => false,
            'success' => false,
            'page_name' => 'Reset Password',
            'realm' => 'auth'
        );

        $this->assertEquals($expected, $result);
    }

    public function testLogoutAction () {
        $this->markTestSkipped(
            'Auth session not implemented'
        );
        $request = Request::create('http://localhost/auth/logout', 'POST', array('session_token' => $this->session->getToken()));

        $result = $this->controller->postLogoutAction($request);

        $expected = '/auth/login';

        $this->assertEquals($expected, $result);
    }
}
