<?php

declare(strict_types=1);

namespace Loom\Router;

use Loom\HttpComponent\Response;
use Loom\HttpComponent\StreamBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;

final class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    public function __construct(ContainerInterface $container)
    {
    }

    public function loadRoutesFromFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $filePath));
        }

        $parsedRoutes = Yaml::parseFile($filePath);

        if (!isset($parsedRoutes['routes'])) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain a "routes" section.', $filePath));
        }

        foreach ($parsedRoutes['routes'] as $key => $route) {
            $this->addRoute($key, $route);
        }
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $requestPath = strtok($request->getUri()->getPath(), '?');

        foreach  ($this->routes as $route) {
            if (!$route->isMethodAllowed($request->getMethod())) {
                continue;
            }
        }

        return $this->get404Response();
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    private function addRoute(string $name, array $routeData): void
    {
        $route = new Route($name, $routeData['path'], $routeData['handler'], $routeData['methods'] ?? ['GET']);

        $this->routes[] = $route;
    }

    private function get404Response(): ResponseInterface
    {
        return new Response(
            statusCode: 404,
            reasonPhrase: 'Not Found',
            headers: ['Content-Type' => 'text/html'],
            body: StreamBuilder::build('Not Found')
        );
    }
}