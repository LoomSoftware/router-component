<?php

declare(strict_types=1);

namespace Loom\Router\Interface;

interface RouteInterface
{
    public function isMethodAllowed(string $method): bool;
}