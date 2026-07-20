<?php

declare(strict_types=1);

use SchoolERP\Container\Container;
use SchoolERP\Exceptions\ErrorHandler;
use SchoolERP\Http\Kernel;
use SchoolERP\Http\Request;
use SchoolERP\Routing\Router;

require_once __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Register Framework Error Handling
|--------------------------------------------------------------------------
*/

ErrorHandler::registerGlobalHandlers();

/*
|--------------------------------------------------------------------------
| Capture Current Request
|--------------------------------------------------------------------------
*/

$request = Request::capture();

/*
|--------------------------------------------------------------------------
| Create Router
|--------------------------------------------------------------------------
*/

$router = new Router();

/*
|--------------------------------------------------------------------------
| Load Application Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/../routes/web.php';

/*
|--------------------------------------------------------------------------
| Handle Request Through Kernel
|--------------------------------------------------------------------------
*/

$container = new Container();

$router = new Router(
    $container
);

$kernel = new Kernel(
    $container,
    $router
);;

$response = $kernel->handle($request);

$response->send();