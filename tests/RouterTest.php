<?php

declare(strict_types=1);

namespace Loom\RouterComponent\Tests;

use Loom\DependencyInjectionComponent\DependencyContainer;
use Loom\DependencyInjectionComponent\DependencyManager;
use Loom\DependencyInjectionComponent\Exception\NotFoundException;
use Loom\HttpComponent\Request;
use Loom\HttpComponent\Uri;
use Loom\RouterComponent\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\RequestInterface;

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

        self::assertCount(5, $router->getRoutes());
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

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    #[DataProvider('requestDataProvider')]
    public function testHandleRequest(RequestInterface $request, int $expectedStatusCode, string $responseText): void
    {
        $router = new Router($this->container);
        $router->loadRoutesFromFile(__DIR__. '/Config/routes.yaml');

        $response = $router->handleRequest($request);

        self::assertEquals($expectedStatusCode, $response->getStatusCode());
        self::assertEquals($responseText, $response->getBody()->getContents());
    }

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    #[DataProvider('invalidRouteConfigDataProvider')]
    public function testHandleRequestWithInvalidRouteConfig(RequestInterface $request): void
    {
        $router = new Router($this->container);
        $router->loadRoutesFromFile(__DIR__. '/Config/routes.yaml');

        self::expectException(\InvalidArgumentException::class);

        $router->handleRequest($request);
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

    public static function invalidRouteConfigDataProvider(): array
    {
        return [
            'Controller does not exist' => [
                'request' => RouterTest::createRequest('GET', '/invalid-controller'),
            ],
            'Method does not exist' => [
                'request' => RouterTest::createRequest('GET', '/invalid-method'),
            ],
        ];
    }

    public static function requestDataProvider(): array
    {
        return [
            'Should return 404 Response' => [
                'request' => RouterTest::createRequest('POST', '/non_existent_route'),
                'expectedStatusCode' => 404,
                'responseText' => 'Not Found',
            ],
            'Static path, should return 200 Response' => [
                'request' => RouterTest::createRequest('GET', '/'),
                'expectedStatusCode' => 200,
                'responseText' => 'Hello, World!',
            ],
            'Dynamic path, should return 200 Response' => [
                'request' => RouterTest::createRequest('GET', '/page/about'),
                'expectedStatusCode' => 200,
                'responseText' => 'Page: about',
            ],
            'Editing dynamic path, should return 200 Response' => [
                'request' => RouterTest::createRequest('GET', '/page/123/edit'),
                'expectedStatusCode' => 200,
                'responseText' => 'Editing Page: 123',
            ]
        ];
    }

    public static function createRequest(string $method, string $uri, string $query = ''): RequestInterface
    {
        return new Request(method: $method, uri: new Uri('http', 'localhost', $uri, $query));
    }
}