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
        $fakeApi = $this->container->get('network.fake');

        if ($networkSlug == TwitterApi::NETWORK_SLUG) {
            $fakeApi->setNetworkSlug('fakeTwitter');
        }

        if ($networkSlug == LinkedinApi::NETWORK_SLUG) {
            $fakeApi->setNetworkSlug('fakeLinkedin');
        }

        return $fakeApi;
    }
}
