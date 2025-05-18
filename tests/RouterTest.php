<?php

declare(strict_types=1);

namespace Loom\Router\Tests;

use Loom\DependencyInjectionComponent\DependencyContainer;
use Loom\DependencyInjectionComponent\DependencyManager;
use Loom\DependencyInjectionComponent\Exception\NotFoundException;
use Loom\HttpComponent\Request;
use Loom\HttpComponent\Response;
use Loom\HttpComponent\StreamBuilder;
use Loom\HttpComponent\Uri;
use Loom\Router\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class RouterTest extends TestCase
{
    private DependencyContainer $container;

    /**
     * @throws NotFoundException
     */
    public function setUp(): void
    {
        $this->container = new DependencyContainer();
        $dependencyManager = new DependencyManager($this->container);
        $dependencyManager->loadDependenciesFromFile(__DIR__ . '/Config/services.yaml');
    }

    public function testLoadRoutesFromFile(): void
    {
        $router = new Router($this->container);

        $router->loadRoutesFromFile(__DIR__ . '/Config/routes.yaml');

        self::assertCount(3, $router->getRoutes());
        self::assertEquals('index', $router->getRoutes()[0]->getName());
        self::assertEquals('/', $router->getRoutes()[0]->getPath());
        self::assertFalse($router->getRoutes()[0]->isMethodAllowed('POST'));
    }

    #[DataProvider('invalidYamlDataProvider')]
    public function testLoadRoutesFromFileWithInvalidYaml(string $filePath): void
    {
        $router = new Router($this->container);

        self::expectException(\InvalidArgumentException::class);

        $router->loadRoutesFromFile($filePath);
    }

    public function testHandleRequest(): void
    {
        $router = new Router($this->container);
        $router->loadRoutesFromFile(__DIR__. '/Config/routes.yaml');

        $request = new Request(
            method: 'POST',
            uri: new Uri('http', 'localhost', '/non-existent-route', '')
        );

        $response = $router->handleRequest($request);

        self::assertEquals(404, $response->getStatusCode());
    }

    public static function invalidYamlDataProvider(): array
    {
        return [
            'Invalid YAML' => [
                __DIR__ . '/Config/invalid_routes.yaml',
            ],
            'Non-existent file' => [
                __DIR__. '/Config/non_existent_file.yaml',
            ],
        ];
    }

    private function get404Response(): ResponseInterface
    {
        return new Response(404, 'Not Found', ['Content-Type' => 'text/html'], StreamBuilder::build('Not Found'));
    }
}