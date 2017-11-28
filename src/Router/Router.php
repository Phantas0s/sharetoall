<?php
declare(strict_types=1);

namespace App\Router;

use Silex\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\NotFoundException;

abstract class Router
{
    protected $app;
    protected $container;

    public function __construct(Application $app, Container $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    public function getController($serviceName)
    {
        try {
            $result = $this->container->get($serviceName);
        } catch (InvalidArgumentException $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $result;
    }

    public function hasPermission(Request $request): bool
    {
        return true;
    }
}
