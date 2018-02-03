<?php

namespace App\Tests\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class IndexControllerTest extends UnitTestCase
{
    public function setUp()
    {
        $container = $this->getContainer();
        $this->controller = $container->get('controller.web.index');
    }

    public function testIndexController()
    {
        $request = Request::create('http://dummyurl');
        $result = $this->controller->indexAction($request);

        $this->assertEquals(['realm' => 'web'], $result);
    }

    public function testIndexControllerResetToken()
    {
        $request = Request::create('http://dummyurl?reset=1234');
        $result = $this->controller->indexAction($request);

        $this->assertEquals(['realm' => 'web', 'resetToken' => 1234], $result);
    }
}
