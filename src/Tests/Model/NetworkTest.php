<?php

namespace App\Tests\Model;

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
        $results = $this->model->findByNetworkUser(1);

        $this->assertEquals('Super Network', $results[0]['networkName']);
        $this->assertEquals('1234', $results[0]['userNetworkToken']);
    }

    public function testFindWithNetworkUser()
    {
        $results = $this->model->findWithNetworkUser(1);

        $this->assertEquals('Super Network', $results[0]['networkName']);
        $this->assertEquals('1234', $results[0]['userNetworkToken']);
    }
}
