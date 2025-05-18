<?php

declare(strict_types=1);

namespace Loom\RouterComponent;

use Loom\RouterComponent\Interface\RouteInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Route implements RouteInterface
{
    protected ContainerInterface $container;

    public function __construct(
        protected string $name,
        protected string $path,
        protected string $handler,
        protected array $methods = ['GET']
    ) {
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
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

    /**
     * @throws ContainerExceptionInterface|\InvalidArgumentException
     */
    public function callHandler(RequestInterface $request, array $args): ResponseInterface
    {
        $handler = explode('::', $this->handler);
        $controllerString = $handler[0];
        $method = $handler[1];

        if (!class_exists($controllerString)) {
            throw new \InvalidArgumentException(sprintf('Controller "%s" does not exist.', $controllerString));
        }

        $controller = $this->container->get($controllerString);

        if (!method_exists($controller, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s" does not exist.', $controllerString, $method));
        }

        return $controller->$method($request, ...$args);
    }
}