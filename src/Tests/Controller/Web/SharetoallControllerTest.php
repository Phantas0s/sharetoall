<?php
declare(strict_types=1);

namespace App\Tests\Controller\Web;

use App\Controller\Web\SharetoallController;
use TestTools\TestCase\UnitTestCase;

class SharetoallControllerTest extends UnitTestCase
{
    /** @var SharetoallController */
    private $controller;

    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.web.sharetoall');
    }

    public function testIndexAction()
    {
        $result = $this->controller->indexAction();
        $this->assertEquals('sharetoall', $result['realm']);
    }
}
