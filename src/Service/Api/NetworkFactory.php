<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Exception\InvalidArgumentException;
use App\Service\Api\NetworkFactoryInterface;
use App\Service\Api\NetworkInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class NetworkFactory implements NetworkFactoryInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(string $networkSlug): NetworkInterface
    {
        try {
            $class = $this->container->get('network.' . $networkSlug);
        } catch (ServiceNotFoundException $e) {
            throw new InvalidArgumentException(sprintf('Impossible to find the network %s', $networkSlug));
        }

        return $class;
    }
}
