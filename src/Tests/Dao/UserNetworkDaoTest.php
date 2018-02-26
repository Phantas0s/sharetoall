<?php

namespace App\Tests\Dao;

use App\Dao\NetworkDao;
use App\Dao\UserNetworkDao;
use Doctrine\ActiveRecord\Exception\NotFoundException as DoctrineNotFoundException;
use TestTools\TestCase\UnitTestCase;

class UserNetworkDaoTest extends UnitTestCase
{
    /** @var NetworkDao */
    private $dao;

    public function setUp()
    {
        $this->dao = $this->get('dao.factory')->create('UserNetwork');
    }

    public function testInvalidate()
    {
        $this->dao->invalidate();

        $result = $this->dao->find(['userNetworkTokenSecret' => 'notdeletedkey']);
        $this->assertInstanceOf(UserNetworkDao::class, $result);

        $this->expectException(DoctrineNotFoundException::class);
        $this->dao->find(['userNetworkTokenSecret' => 'deleteKey']);
    }
}
