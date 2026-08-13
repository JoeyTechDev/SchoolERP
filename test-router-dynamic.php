<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Routing\Router;

echo "DYNAMIC ROUTER TEST\n";
echo "===================\n\n";

$container = new Container();

$container->instance(
    Container::class,
    $container
);

$router = new Router($container);

$router->get(
    '/student/{id}',
    function (Request $request, int $id): Response {
        return Response::make(
            "Student ID: {$id}"
        );
    }
);

/*
|--------------------------------------------------------------------------
| Test dynamic route registration.
|--------------------------------------------------------------------------
*/

$request = Request::capture();

$response = $router->dispatch($request);

echo 'Router Instance Test: '
    . ($router instanceof Router ? 'PASSED' : 'FAILED')
    . PHP_EOL;

echo 'Response Instance Test: '
    . ($response instanceof Response ? 'PASSED' : 'FAILED')
    . PHP_EOL;

echo PHP_EOL;
echo "DYNAMIC ROUTER TEST COMPLETE\n";