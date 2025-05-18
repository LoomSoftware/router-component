<?php

declare(strict_types=1);

namespace Loom\RouterComponent\Interface;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RouteInterface
{
    public function isMethodAllowed(string $method): bool;
    public function getName(): string;
    public function getPath(): string;
    public function callHandler(RequestInterface $request, array $args): ResponseInterface;
    public function setContainer(ContainerInterface $container): void;
}