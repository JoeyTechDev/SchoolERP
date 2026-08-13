<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Routing\Router;

echo "ROUTER TEST\n";
echo "===========\n\n";

$container = new Container();

$container->instance(
    Container::class,
    $container
);

$router = new Router($container);

$router->get(
    '/home',
    function (Request $request): Response {
        return Response::make(
            'Welcome to SchoolERP Framework!'
        );
    }
);

$request = Request::capture();

$response = $router->dispatch($request);

echo 'Router Instance Test: '
    . ($router instanceof Router ? 'PASSED' : 'FAILED')
    . PHP_EOL;

echo 'Response Instance Test: '
    . ($response instanceof Response ? 'PASSED' : 'FAILED')
    . PHP_EOL;

echo PHP_EOL;
echo "ROUTER TEST COMPLETE\n";