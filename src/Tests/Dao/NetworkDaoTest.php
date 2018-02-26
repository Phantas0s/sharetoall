<?php

namespace Tests\Dao;

use App\Dao\NetworkDao;
use App\Model\Network;
use TestTools\TestCase\UnitTestCase;

class NetworkDaoTest extends UnitTestCase
{
    /** @var UserDao */
    private $dao;

    public function setUp()
    {
        $this->dao = $this->get('dao.factory')->create('Network');
    }

    public function testFind()
    {
        $this->dao->find('supernetwork');
        $this->assertEquals('Super Network', $this->dao->networkName);
    }

    public function testSearchWithUser()
    {
        $cond = [
            'un.userId' => 1,
        ];

        $result = $this->dao->searchWithNetworkUser(['cond' => $cond]);

        $this->assertInstanceOf(NetworkDao::class, $result->getFirstResult());
        $this->assertTrue(count($result->getAllResults()) > 0);
    }

    public function testSearchAllNetworksByUserId()
    {
        $result = $this->dao->searchAllNetworksByUserId(1);

        $this->assertInstanceOf(NetworkDao::class, $result->getFirstResult());
        $this->assertTrue(count($result->getAllResults()) > 0);
    }
}
