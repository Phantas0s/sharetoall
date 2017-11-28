<?php

namespace App\Tests\Router;

use TestTools\TestCase\UnitTestCase;
use App\Router\ErrorRouter;

class ErrorRouterTest extends UnitTestCase
{
    /**
     * @var ErrorRouter
     */
    protected $router;

    public function setUp()
    {
        $this->router = $this->get('router.error');
    }

    public function testRoute()
    {
        $this->router->route();
        $this->assertTrue(true);
    }
}