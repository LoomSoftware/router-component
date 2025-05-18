<?php

declare(strict_types=1);

namespace Loom\Router;

use Psr\Container\ContainerInterface;

final class Router
{
    private array $routes;

    public function __construct(ContainerInterface $container)
    {
    }
}