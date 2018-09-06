<?php

namespace App\Tests\Model;

use App\Model\Network;
use TestTools\TestCase\UnitTestCase;

class NetworkTest extends UnitTestCase
{
    /**
     * @var \App\Model\User
     */
    protected $model;

    public function setUp()
    {
        $this->model = $this->get('model.network');
    }

    public function testFind()
    {
        $this->model->find('supernetwork');
        $this->assertEquals('supernetwork', $this->model->getId());

        $this->assertEquals('Super Network', $this->model->networkName);
    }

    public function testFindByNetworkUser()
    {
        $results = $this->model->findAllNetworkByUserId(1);

        $this->assertInstanceOf(Network::class, $results->getFirstResult());
        $this->assertTrue(count($results->getAllResults()) > 0);
    }

    public function testFindWithNetworkUser()
    {
        $results = $this->model->findWithNetworkUser(['un.userId' => 1]);

        $this->assertInstanceOf(Network::class, $results->getFirstResult());
        $this->assertTrue(count($results->getAllResults()) > 0);
    }

    public function testMapNetworksToFrontend()
    {
        $results = $this->model->findAllNetworkByUserId(1);
        $networks = $this->model->mapNetworksToFrontend($results);
        foreach ($networks as $network) {
            $this->assertArrayHasKey("userAccount", $network);
            $this->assertArrayHasKey("networkSlug", $network);
            $this->assertArrayHasKey("networkName", $network);
            $this->assertArrayHasKey("userId", $network);
            $this->assertArrayHasKey("networkTokenExpire", $network);
        }
    }
}
