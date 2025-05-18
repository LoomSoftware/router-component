<?php

declare(strict_types=1);

namespace Loom\RouterComponent;

use Loom\HttpComponent\Response;
use Loom\HttpComponent\StreamBuilder;
use Loom\RouterComponent\Interface\RouteInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;

final class Router
{
    /**
     * @var RouteInterface[]
     */
    private array $routes = [];

    public function __construct(private readonly ContainerInterface $container)
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

    /**
     * @throws \InvalidArgumentException
     */
    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $requestPath = strtok($request->getUri()->getPath(), '?');

        foreach  ($this->routes as $route) {
            if (!$route->isMethodAllowed($request->getMethod())) {
                continue;
            }

            if (preg_match($this->generateRoutePattern($route->getPath()), $requestPath, $matches)) {
                $args = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                return $route->callHandler($request, $args);
            }
        }

        return $this->get404Response();
    }

    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    private function addRoute(string $name, array $routeData): void
    {
        $route = new Route($name, $routeData['path'], $routeData['handler'], $routeData['methods'] ?? ['GET']);
        $route->setContainer($this->container);

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

    private function generateRoutePattern(string $routePath): string
    {
        $routePath = preg_replace_callback('/{([^}]+)}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $routePath);

        return "#^{$routePath}$#";
    }
}