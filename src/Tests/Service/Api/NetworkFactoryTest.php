<?php
declare(strict_types=1);

namespace App\Tests\Service\Api;

use App\Exception\InvalidArgumentException;
use App\Exception\NotFoundException;
use App\Service\Api\NetworkFactory;
use TestTools\TestCase\UnitTestCase;

class NetworkFactoryTest extends UnitTestCase
{
    /** @var NetworkFactory */
    private $networkFactory;

    public function setUp()
    {
        $this->networkFactory = $this->get('service.network-factory');
    }

    public function testCreate()
    {
        $modelNetwork = $this->get('model.network');

        $networks[] = $modelNetwork->find('twitter');
        $networks[] = $modelNetwork->find('linkedin');

        $results = [];
        foreach ($networks as $network) {
            $results[] = $this->networkFactory->create($network->networkSlug);
        }

        $this->assertCount(2, $results);
    }

    public function testCreateUnknownApiClass()
    {
        $network = $this->get('model.network')->find(['networkSlug' => 'supernetwork']);

        $this->expectException(InvalidArgumentException::class);
        $this->networkFactory->create($network->networkSlug);
    }
}
