<?php

declare(strict_types=1);

namespace Loom\Router;

use Loom\Router\Interface\RouteInterface;

class Route implements RouteInterface
{
    public function __construct(protected string $name, protected string $path, string $handler, protected array $methods = ['GET'])
    {
    }

    public function isMethodAllowed(string $method): bool
    {
        return in_array($method, $this->methods);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}