<?php
declare(strict_types=1);

namespace App\Tests\Service\Api;

use App\Exception\NotFoundException;
use App\Service\Api\NetworkFactory;
use TestTools\TestCase\UnitTestCase;

class ApiFactoryTest extends UnitTestCase
{
    /** @var NetworkFactory */
    private $networkFactory;

    public function setUp()
    {
        $this->networkFactory = $this->get('service.network-factory');
    }

    public function testCreate()
    {
        $networks = $this->get('model.network')->findAll();
        $results = [];

        foreach ($networks as $network) {
            //dummy network for test purposes
            if ($network->networkSlug == 'supernetwork') {
                continue;
            }

            $results[] = $this->networkFactory->create($network->networkSlug);
        }

        $this->assertCount(2, $results);
    }

    public function testCreateUnknownApiClass()
    {
        $network = $this->get('model.network')->find(['networkSlug' => 'supernetwork']);

        $this->expectException(NotFoundException::class);
        $this->networkFactory->create($network->networkSlug);
    }
}
