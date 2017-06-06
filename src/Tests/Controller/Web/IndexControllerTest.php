<?php

namespace App\Tests\Controller\Web;

use TestTools\TestCase\UnitTestCase;

class IndexControllerTest extends UnitTestCase
{
    /**
     * @var \App\Controller\Web\IndexController
     */
    protected $controller;

    public function setUp()
    {
        $this->controller = $this->get('controller.web.index');
    }

    public function testIndexAction()
    {
        $this->markTestSkipped(
            'No proper controller implemented'
        );
        $result = $this->controller->indexAction();
        $this->assertInternalType('array', $result);
    }
}
