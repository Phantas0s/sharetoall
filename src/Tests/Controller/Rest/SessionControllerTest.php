<?php

namespace App\Tests\Controller\Rest;

use App\Service\Session;
use TestTools\TestCase\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

class NetworkControllerTest extends UnitTestCase
{
    protected $controller;
    protected $session;

    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.rest.network');
    }

    public function testCgetAction()
    {
        $request = Request::create('https://localhost/api/network');
        $networks = $this->controller->cgetAction($request);

        foreach ($networks as $values) {
            $this->assertArrayHasKey('networkName', $values);
            $this->assertArrayHasKey('networkSlug', $values);
        }

        $this->assertNotEmpty($networks);
    }

    public function testGetAction()
    {
    }
}
