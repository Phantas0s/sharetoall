<?php

namespace App\Tests\Service;

use App\Model\User;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;
use App\Service\Session;

class SessionTest extends UnitTestCase
{
    /** @var Session */
    protected $session;

    /** @var CacheInterface */
    protected $cache;

    /** @var Request */
    protected $request;

    /** @var User */
    protected $user;

    protected $url;

    public function setUp()
    {
        $this->markTestSkipped(
            'Session service not implemented'
        );
        $container = $this->getContainer();
        $this->cache = $container->get('cache');
        $this->session = $container->get('service.session');
        $this->request = Request::create('http://licensor.yoda.pantadev.com');
        $this->url = $this->request->getUri();
        $this->user = $container->get('model.user');
    }

    public function testLogin()
    {
        $this->markTestSkipped(
            'Session service not implemented'
        );
        $this->session->generateToken();
        $this->assertEquals(false, $this->session->hasUserId());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());

        $this->session->login('admin@pantaflix.com', 'passwd');

        $this->assertEquals(2, $this->session->getUserId());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());
    }

    public function testLogout()
    {
        $this->markTestSkipped(
            'Session service not implemented'
        );
        $this->session->generateToken();
        $this->session->login('admin@pantaflix.com', 'passwd');
        $this->assertEquals(2, $this->session->getUserId());
        $oldToken = $this->session->getToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $oldToken);
        $this->session->logout();
        $newToken = $this->session->getToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $newToken);
        $this->assertFalse($this->session->hasUserId());
    }

    public function testLicensorLogout()
    {
        $this->markTestSkipped(
            'Session service not implemented'
        );
        $this->session->generateToken();
        $this->session->login('admin@pantaflix.com', 'passwd');
        $this->assertEquals(2, $this->session->getUserId());
        $oldToken = $this->session->getToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $oldToken);
        $this->session->logout();
        $newToken = $this->session->getToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $newToken);
        $this->assertFalse($this->session->hasUserId());
    }

    public function testGenerateToken()
    {
        $this->markTestSkipped(
            'Session service not implemented'
        );
        $this->session->generateToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());
    }

    public function testUser()
    {
        $this->markTestSkipped(
            'Session service not implemented'
        );
        $this->session->generateToken();
        $this->session->logout();

        $this->assertTrue($this->session->isAnonymous());
        $this->assertFalse($this->session->isUser());
        $this->assertFalse($this->session->isAdmin());
        $this->assertEquals('', $this->session->getUserFirstName());
        $this->assertEquals('', $this->session->getUserLastName());

        $this->session->login('admin@pantaflix.com', 'passwd', null);

        $this->assertFalse($this->session->isAnonymous());
        $this->assertTrue($this->session->isUser());
        $this->assertTrue($this->session->isAdmin());
        $this->assertEquals('Admin', $this->session->getUserFirstName());
        $this->assertEquals('Pantaflix', $this->session->getUserLastName());
    }

    public function testCreateOneTimeToken()
    {
        $this->markTestSkipped(
            'Session service not implemented'
        );
        $this->session->generateToken();
        $this->session->login('admin@pantaflix.com', 'passwd');
        $oneTimeToken = $this->session->createOneTimeToken();

        $request = Request::create('http://localhost/foo?t=' . $oneTimeToken, 'GET');

        $oneTimeSession = new Session($this->cache, $request, $this->user);

        $this->assertEquals(2, $this->session->getUserId());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());
        $this->assertEquals(2, $oneTimeSession->getUserId());
        $this->assertEquals($this->session->getToken(), $oneTimeSession->getToken());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $oneTimeSession->getToken());
    }
}
