<?php

namespace App\Tests\Model;

use App\Dao\DaoAbstract;
use App\Exception\InvalidArgumentException;
use App\Exception\NotFoundException;
use App\Model\User;
use Doctrine\ActiveRecord\Exception\Exception As DoctrineException;
use Mockery\Mock;
use Symfony\Component\HttpFoundation\Request;
use TestTools\TestCase\UnitTestCase;

class UserTest extends UnitTestCase
{
    /**
     * @var \App\Model\User
     */
    protected $model;

    public function setUp()
    {
        $this->model = $this->get('model.user');
    }

    public function testFind()
    {
        $this->model->find(1);
        $this->assertEquals(1, $this->model->getId());

        $this->assertInternalType('string', $this->model->userEmail);
        $this->assertInternalType('string', $this->model->userPassword);
    }

    public function testGetId()
    {
        $this->model->find(1);
        $this->assertEquals(1, $this->model->getId());
    }

    public function testFindAll()
    {
        $users = $this->model->findAll(['userEmail' => 'user@sharetoall.com']);
        $this->assertCount(1, $users);

        $user = $users[0];

        $this->assertEquals('user@sharetoall.com', $user->userEmail);
    }

    public function testGetPasswordException()
    {
        $this->expectException(DoctrineException::class);
        $this->model->userPassword;
    }

    public function testInsecurePassword()
    {
        $password = 'foo';
        $this->model->find(1);

        $this->expectException(InvalidArgumentException::class);
        $this->model->updatePassword($password);
    }

    public function testEmptyPassword()
    {
        $this->model->find(1);

        $this->expectException(InvalidArgumentException::class);
        $this->model->updatePassword('');
    }

    public function testFindByPasswordResetToken()
    {
        $user = $this->model->findByPasswordResetToken('123456');
        $this->assertEquals(2, $user->getId());
    }

    public function testFindByPasswordResetTokenWithInvalidToken()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->model->findByPasswordResetToken('XXX');
    }

    public function testFindByVerificationToken()
    {
        $user = $this->model->findByVerificationToken('123456');
        $this->assertEquals(2, $user->getId());
    }

    public function testFindByVerificationTokenWithInvalidToken()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->model->findByVerificationToken('XXX');
    }

    public function testFindByEmail()
    {
        $user = $this->model->findByEmail('user_cow@sharetoall.com');
        $this->assertEquals(2, $user->getId());
    }

    public function testFindByEmailError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->model->findByEmail('admin@XXX.com');
    }

    public function testPasswordIsValid()
    {
        $user = $this->model->find(1);
        $result = $user->passwordIsValid('password');

        $this->assertTrue($result);
    }

    public function testEmailVerified()
    {
        $user = $this->model->find(1);
        $this->assertTrue($user->emailVerified());
    }

    public function testSetPasswordResetToken()
    {
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');

        /** @var User $user */
        $user = $this->model->find(2);
        $token = '123456';
        $user->setPasswordResetToken($token);
        $this->assertInternalType('string', $user->userResetPasswordToken);
        $this->assertGreaterThan(5, strlen($user->userResetPasswordToken));
    }

    public function testSetPasswordResetTokenError()
    {
        /** @var User $user */
        $user = $this->model->find(2);
        $this->expectException(InvalidArgumentException::class);
        $user->setPasswordResetToken(1234);
    }

    public function testDeletePasswordResetToken()
    {
        $user = $this->model->find(2);
        $this->assertInternalType('string', $user->userResetPasswordToken);
        $this->assertGreaterThan(5, strlen($user->userResetPasswordToken));
        $user->deletePasswordResetToken();
        $this->assertEmpty($user->userResetPasswordToken);
    }

    public function testDeleteVerificationToken()
    {
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');
        /** @var User $user */
        $user = $this->model->find(2);
        $this->assertInternalType('string', $user->userVerifEmailToken);
        $this->assertGreaterThan(5, strlen($user->userVerifEmailToken));
        $user->deleteVerificationToken();
        $this->assertNull($user->userVerifEmailToken);
    }
}
