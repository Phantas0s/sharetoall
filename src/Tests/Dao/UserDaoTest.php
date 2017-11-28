<?php

namespace Tests\Dao;

use App\Dao\UserDao;
use TestTools\TestCase\UnitTestCase;

class UserDaoTest extends UnitTestCase
{
    /** @var UserDao */
    private $dao;

    public function setUp()
    {
        $this->dao = $this->get('dao.factory')->create('User');
    }

    public function testFind()
    {
        $this->dao->find(1);

        $this->assertInternalType('string', $this->dao->userFirstname);
        $this->assertInternalType('string', $this->dao->userLastname);
        $this->assertInternalType('string', $this->dao->userEmail);
        $this->assertInternalType('string', $this->dao->userPassword);
    }
}
