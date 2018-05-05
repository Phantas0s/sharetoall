<?php
declare(strict_types=1);

namespace App\Tests\Controller\Rest;

use App\Dao\DaoAbstract;
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
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');
        $params = [
            'oauth_token' => 'dummy',
            'oauth_verifier' => 'superDummy'
        ];

        $request = Request::create('https://dummyurl', 'GET', $params);
        $result = $this->controller->twitterAction($request);

        $this->assertEquals('/sharetoall#/dashboard', $result);
    }

    public function testLinkedinAction()
    {
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');
        $params = [
            'code' => 'dummyCode',
            'state' => 'superDummy'
        ];

        $request = Request::create('https://dummyurl', 'GET', $params);
        $result = $this->controller->linkedinAction($request);

        $this->assertEquals('/sharetoall#/dashboard', $result);
    }
}
