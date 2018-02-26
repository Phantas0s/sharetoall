<?php
declare(strict_types=1);

namespace App\Tests\Service\Api;

use App\Service\Api\LinkedinApi;
use App\Service\Api\NetworkFactoryInterface;
use App\Service\Api\NetworkInterface;
use App\Service\Api\TwitterApi;
use Symfony\Component\DependencyInjection\Container;

class FakeNetworkFactory implements NetworkFactoryInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(string $networkSlug): NetworkInterface
    {
        $fakeApi = $this->container->get('network.fake.' . $networkSlug);
        $fakeApi->setNetworkSlug('fake'.$networkSlug);

        return $fakeApi;
    }
}
