<?php

namespace App\Tests\Controller\Rest;

use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class RedirectControllerTest extends UnitTestCase
{
    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.web.redirect');
    }

    public function testTwitterAction()
    {
        $this->markTestSkipped('todo');
        $request = Request::create('https://dummyurl?oauth_token=dummy&oauth_verifier=superDummy');
        $result = $this->controller->twitterAction('twitter', $request);
    }
}
