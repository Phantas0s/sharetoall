<?php
declare(strict_types=1);

namespace App\Tests\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class MessageControllerTest extends UnitTestCase
{
    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.rest.message');
    }

    public function testPostAction()
    {
        $params = [
            'message' => 'This is a super message',
            'networkSlugs' => [
                'supernetwork'
            ]
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);

        $result = $this->controller->postAction($request);

        $this->assertCount(1, $result);
        $this->assertEquals('posted!', $result[0]);
    }
}
