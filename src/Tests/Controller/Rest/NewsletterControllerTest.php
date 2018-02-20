<?php

namespace App\Tests\Controller\Rest;

use App\Exception\FormInvalidException;
use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class NewsletterControllerTest extends UnitTestCase
{
    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.rest.newsletter');
    }

    public function testPostAction()
    {
        $params = [
            'email' => 'tralala@youpi.fr'
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);
        $result = $this->controller->postAction($request);

        $this->assertEquals($result, $params['email']);
    }

    public function testPostActionWithWrongEmail()
    {
        $params = [
            'email' => 'tralalayoupi.fr'
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);
        $this->expectException(FormInvalidException::class);

        $result = $this->controller->postAction($request);
    }
}
