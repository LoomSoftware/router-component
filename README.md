# Loom | Router Component

<p>
<!-- Version Badge -->
<img src="https://img.shields.io/badge/Version-1.0.0-blue" alt="Version 1.0.0">
<!-- Coverage Badge -->
<img src="https://img.shields.io/badge/Coverage-100.00%25-1ccb3c" alt="Coverage 100.00%">
<!-- License Badge -->
<img src="https://img.shields.io/badge/License-GPL--3.0--or--later-40adbc" alt="License GPL-3.0-or-later">
</p>

# Installation

```shell
composer require loomlabs/router-component
```

# Usage

```php
use Loom\DependencyInjectionComponent\DependencyContainer;
use Loom\DependencyInjectionComponent\DependencyManager;
use Loom\HttpComponent\Request;
use Loom\HttpComponent\Uri;
use Loom\RoutingComponent\Router;

$container = new DependencyContainer();
$dependencyManager = new DependencyManager($container);
$dependencyManager->loadDependenciesFromFile(__DIR__ . '/config/services.yaml');

$router = new Router($container);

$router->loadRoutesFromFile(__DIR__ . '/config/routes.yaml');

$request = new Request(
  'GET',
  new Uri('http', 'localhost', $uri, $query)
);
  
echo $router->handleRequest()->getBody()->getContents();
```

```yaml
routes:
  app.index:
    path: /
    handler: App\Controller\AppController::index
    methods: [GET]
  page.view:
    path: /page/{page}
    handler: App\Controller\PageController::view
```