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
        $container = $this->getContainer();
        $this->cache = $container->get('cache');
        $this->session = $container->get('service.session');
        $this->request = Request::create('http://sharetoall.loc');
        $this->url = $this->request->getUri();
        $this->user = $container->get('model.user');
    }

    public function testLogin()
    {
        $this->session->generateToken();
        $this->assertEquals(false, $this->session->hasUserId());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());

        $this->session->login('user@sharetoall.com', 'password');

        $this->assertEquals(1, $this->session->getUserId());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());
    }

    public function testLogout()
    {
        $this->session->generateToken();
        $this->session->login('user@sharetoall.com', 'password');
        $this->assertEquals(1, $this->session->getUserId());
        $oldToken = $this->session->getToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $oldToken);
        $this->session->logout();

        $newToken = $this->session->getToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $newToken);
        $this->assertFalse($this->session->hasUserId());
    }

    public function testGenerateToken()
    {
        $this->session->generateToken();
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());
    }

    public function testUser()
    {
        $this->session->generateToken();
        $this->session->logout();

        $this->assertTrue($this->session->isAnonymous());
        $this->assertFalse($this->session->isUser());

        $this->session->login('user@sharetoall.com', 'password', null);

        $this->assertFalse($this->session->isAnonymous());
        $this->assertTrue($this->session->isUser());
    }

    public function testCreateOneTimeToken()
    {
        $this->session->generateToken();
        $this->session->login('user@sharetoall.com', 'password');
        $oneTimeToken = $this->session->createOneTimeToken();

        $request = Request::create('http://localhost/foo?t=' . $oneTimeToken, 'GET');

        $oneTimeSession = new Session($this->cache, $request, $this->user);

        $this->assertEquals(1, $this->session->getUserId());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $this->session->getToken());
        $this->assertEquals(1, $oneTimeSession->getUserId());
        $this->assertEquals($this->session->getToken(), $oneTimeSession->getToken());
        $this->assertRegExp('/[a-zA-Z0-9]{64}/', $oneTimeSession->getToken());
    }
}
