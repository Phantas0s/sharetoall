<?php

namespace App\Tests\Router;

use App\Service\Session;
use Silex\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;
use App\Router\RestRouter;

class RestRouterTest extends UnitTestCase
{
    /** @var RestRouter */
    protected $router;

    /** @var Application */
    protected $app;

    /** @var FakeRestController */
    protected $controller;

    /** @var Container */
    protected $container;

    /** @var Session */
    protected $session;

    public function setUp()
    {
        $this->container = $this->getContainer();
        $this->app = $this->container->get('app.silex');
        $this->router = $this->container->get('router.rest');
        $this->controller = $this->container->get('controller.rest.fake');
        $this->session = $this->container->get('service.session');
        $this->container->get('router.error')->route();
    }

    public function testGetRoute()
    {
        $request = Request::create('http://localhost/api/fake/345');
        $this->router->route('/api', 'controller.rest.');
        $response = $this->app->handle($request);
        $result = json_decode($response->getContent(), true);
        $this->assertEquals('getAction', $this->controller->actionName);
        $this->assertInstanceOf(Request::class, $this->controller->request);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('request', $result);
        $this->assertArrayHasKey('actionName', $result);
        $this->assertEquals('getAction', $result['actionName']);
        $this->assertEquals(345, $result['id']);
        $this->assertInternalType('array', $result['request']);
    }
}
