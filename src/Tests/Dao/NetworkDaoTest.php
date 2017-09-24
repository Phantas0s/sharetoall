<?php

namespace Tests\Dao;

use App\Dao\NetworkDao;
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
        $result = $this->dao->searchWithNetworkUser(['cond' => ['un.userId' => 1]]);

        $this->assertEquals('supernetwork', $result->getFirstResult()->networkSlug);
        $this->assertEquals('1234', $result->getFirstResult()->userNetworkToken);
    }
}
