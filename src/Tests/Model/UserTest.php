<?php

namespace App\Tests\Model;

use App\Dao\DaoAbstract;
use App\Exception\NotFoundException;
use App\Form\User\RegisterForm;
use App\Model\Licensor;
use App\Model\User;
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
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $this->model = $this->get('model.user');
        $this->licensorSlug = 'licensor';
    }

    public function testFind()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $this->model->find(1);

        $this->assertEquals(1, $this->model->getId());
        $this->assertNotNull($this->model->userEmail);
        $this->assertNotNull($this->model->userPassword);
    }

    public function testFindAll()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $users = $this->model->findAll(['userEmail' => 'user@pantaflix.com', 'licensorId' => 2]);
        $this->assertCount(1, $users);

        $user = $users[0];

        $this->assertEquals('user@pantaflix.com', $user->userEmail);
        $this->assertEquals(2, $user->licensorId);
    }

    /**
     * @expectedException \Doctrine\ActiveRecord\Exception\Exception
     */
    public function testGetPasswordException()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $this->model->userPassword;
    }

    /**
     * @expectedException \App\Exception\InvalidArgumentException
     */
    public function testInsecurePassword()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $password = 'fooBar';

        $this->model->find(3);

        $this->model->updatePassword($password);
    }

    /**
     * @expectedException \App\Exception\InvalidArgumentException
     */
    public function testEmptyPassword()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $password = '';

        $this->model->find(3);

        $this->model->updatePassword($password);
    }

    public function testFindByPasswordResetToken()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $user = $this->model->findByPasswordResetToken('4wai87E67cuDCe9');

        $this->assertEquals(65946, $user->getId());
    }

    /**
     * @expectedException \App\Exception\InvalidArgumentException
     */
    public function testFindByPasswordResetTokenWithInvalidToken()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $this->model->findByPasswordResetToken('XXX');
    }

    public function testFindByVerificationToken()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $user = $this->model->findByVerificationToken('tkkqEUTj1P13ucm');

        $this->assertEquals(65776, $user->getId());
    }

    /**
     * @expectedException \App\Exception\InvalidArgumentException
     */
    public function testFindByVerificationTokenWithInvalidToken()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $this->model->findByVerificationToken('XXX');
    }

    public function testFindByEmail()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $user = $this->model->findByEmail('admin@pantaflix.com');

        $this->assertEquals(2, $user->getId());
    }

    /**
     * @expectedException \App\Exception\InvalidArgumentException
     */
    public function testFindByEmailError()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $this->model->findByEmail('admin@XXX.com');
    }

    public function testPasswordIsValid()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $user = $this->model->find(2);

        $password = 'passwd';

        $result = $user->passwordIsValid($password);

        $this->assertTrue($result);
    }

    public function testRegister()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');

        /** @var RegisterForm $form */
        $form = $this->get('form.factory')->create('User\Register');

        $inputValues = array(
            'userFirstname' => 'Jens',
            'userLastname' => 'Mander',
            'userEmail' => 'test@example.com',
            'userEmailConfirm' => 'test@example.com',
            'userPassword' => 'es58bhst89e5',
            'licensorName' => 'Foo Bar GmbH',
            'licensorSlug' => 'foobar',
            'userTermsAccepted' => 1,
            'userNewsletter' => 0
        );

        $form->setDefinedWritableValues($inputValues);

        $form->setPasswordHash('$6$5ygXjBO2gNbW$p1eaS7isBLD1JfN6PaQzrGKJHf9UGmUOBCZiqq3VnhDSPhdbIzOnu3kbKO2mcKEFiD11jFoPE5YSyvA7cYbbK1');

        $form->setVerificationToken('5e6e341dcd74b472a63144cdaf239070');

        $this->model->register($form);

        $licensor = $this->model->getLicensor();

        $this->assertInstanceOf(Licensor::class, $licensor);
        $this->assertGreaterThan(0, $licensor->getId());

        $this->assertGreaterThan(0, $this->model->getId());
    }

    public function testGetLicensor()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $user = $this->model->find(9);
        $licensor = $user->getLicensor();

        $this->assertInstanceOf(Licensor::class, $licensor);
        $this->assertEquals(2, $licensor->getId());
    }

    public function testEmailVerified()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        $user = $this->model->find(9);
        $this->assertTrue($user->emailVerified());
    }

    public function testAddRole()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');
        $user = $this->model->find(11);

        $user->addRole(User::ROLE_OWNER);
        $this->assertEquals($user->getRoles(), ['owner']);
        $user->addRole(User::ROLE_ADMIN);
        $this->assertEquals($user->getRoles(), ['owner', 'admin']);
    }

    public function testAddUnknownRole()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');
        $user = $this->model->find(11);
        $this->expectException(NotFoundException::class);
        $user->addRole('thisRoleWillNeverExists');
    }

    public function testSetPasswordResetToken()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');
        /** @var User $user */
        $user = $this->model->find(3);
        $token = '123456789';
        $user->setPasswordResetToken($token);
        $this->assertInternalType('string', $user->userPasswordToken);
        $this->assertGreaterThan(5, strlen($user->userPasswordToken));
    }

    /**
     * @expectedException \App\Exception\InvalidArgumentException
     */
    public function testSetPasswordResetTokenError()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        /** @var User $user */
        $user = $this->model->find(3);
        $token = '1234';
        $user->setPasswordResetToken($token);
    }

    public function testDeletePasswordResetToken()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        /** @var User $user */
        $user = $this->model->find(65946);
        $this->assertInternalType('string', $user->userPasswordToken);
        $this->assertGreaterThan(5, strlen($user->userPasswordToken));
        $user->deletePasswordResetToken();
        $this->assertEmpty($user->userPasswordToken);
    }

    public function testDeleteVerificationToken()
    {
        $this->markTestSkipped(
            'User Model not implemented'
        );
        DaoAbstract::setDateTimeClassName('\TestTools\Util\FixedDateTime');
        /** @var User $user */
        $user = $this->model->find(65961);
        $this->assertInternalType('string', $user->userVerificationToken);
        $this->assertGreaterThan(5, strlen($user->userVerificationToken));
        $user->deleteVerificationToken();
        $this->assertNull($user->userVerificationToken);
    }
}
