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
        $this->markTestSkipped(
            'User dao not implemented'
        );

        $this->dao->find(6);
        $this->assertInternalType('array', $this->dao->userRoles);
    }
}
