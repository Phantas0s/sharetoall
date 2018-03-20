<?php declare(strict_types=1);

namespace App\Tests\Controller\Rest;

use App\Dao\DaoAbstract;
use App\Exception\FormInvalidException;
use App\Model\User;
use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class RegisterControllerTest extends UnitTestCase
{
    public function setUp()
    {
        $container = $this->getContainer();
        $this->session = $container->get('service.session');
        $this->session->generateToken()->login('user@sharetoall.com', 'password');
        $this->controller = $container->get('controller.rest.register');
    }

    public function testPostAction()
    {
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');

        $params = [
            'form' => [
                'userEmail' => 'user@example.com',
                'userPassword' => 'es58bhst89e5',
                'userPasswordConfirm' => 'es58bhst89e5',
                'userNewsletter' => 1
            ]
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);
        $user = $this->controller->postAction($request);

        $this->assertInstanceOf(User::class, $user);

        $values = $user->getValues();
        $this->assertEquals($params['form']['userEmail'], $values['userEmail']);
        $this->assertEquals($params['form']['userNewsletter'], $values['userNewsletter']);
    }

    public function testPostActionWithExistingEmail()
    {
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');

        $params = [
            'form' => [
                'userEmail' => 'user@sharetoall.com',
                'userPassword' => 'es58bhst89e5',
                'userPasswordConfirm' => 'es58bhst89e5',
                'userNewsletter' => '0'
            ]
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);

        $this->expectException(FormInvalidException::class);
        $user = $this->controller->postAction($request);
    }

    public function testPostActionWithDifferentPasswords()
    {
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');

        $params = [
            'form' => [
                'userEmail' => 'user@sharetoall.com',
                'userPassword' => 'es58bhst89e5',
                'userPasswordConfirm' => 'sdjflsfdjk',
                'userNewsletter' => 0
            ]
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);

        $this->expectException(FormInvalidException::class);
        $user = $this->controller->postAction($request);
    }

    public function testPostActionWithErrors()
    {
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');

        $params = [
            'form' => [
                'userEmail' => 'user@sharetoall.com',
                'userPassword' => 'es58bhst89e5',
                'userPasswordConfirm' => 'es58bhst89e5',
            ]
        ];

        $request = Request::create('http://dummyUrl', 'POST', $params);

        $this->expectException(FormInvalidException::class);
        $user = $this->controller->postAction($request);
    }


}
