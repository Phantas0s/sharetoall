<?php
declare(strict_types=1);

namespace App\Tests\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class ContactControllerTest extends UnitTestCase
{
    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->controller = $container->get('controller.rest.contact');
    }

    public function testPostAction()
    {
        $params = [
            "form" => [
                'email' => 'email@email.com',
                'message' => 'this is a super message',
            ]
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);
        $result = $this->controller->postAction($request);

        $this->assertEquals($params['form'], $result);
    }
}
