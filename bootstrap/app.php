<?php

declare(strict_types=1);

use SchoolERP\Container\Container;
use SchoolERP\Exceptions\ErrorHandler;
use SchoolERP\Http\Kernel;
use SchoolERP\Http\Request;
use SchoolERP\Middleware\MaintenanceMiddleware;
use SchoolERP\Middleware\SessionMiddleware;
use SchoolERP\Routing\Router;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Session\SessionManager;
use SchoolERP\View\ViewFactory;

require_once __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Register Error Handling
|--------------------------------------------------------------------------
*/

ErrorHandler::registerGlobalHandlers();

/*
|--------------------------------------------------------------------------
| Capture Request
|--------------------------------------------------------------------------
*/

$request = Request::capture();

/*
|--------------------------------------------------------------------------
| Create Container
|--------------------------------------------------------------------------
*/

$container = new Container();

$container->instance(
    Container::class,
    $container
);

/*
|--------------------------------------------------------------------------
| Register Core Services
|--------------------------------------------------------------------------
*/

$container->singleton(
    SessionInterface::class,
    SessionManager::class
);

$container->singleton(
    ViewFactory::class,
    function () {
        return new ViewFactory(
            dirname(__DIR__) . '/app/views'
        );
    }
);

/*
|--------------------------------------------------------------------------
| Create Router
|--------------------------------------------------------------------------
*/

$router = new Router($container);

/*
|--------------------------------------------------------------------------
| Load Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/../routes/web.php';

/*
|--------------------------------------------------------------------------
| Create Kernel
|--------------------------------------------------------------------------
*/

$kernel = new Kernel(
    $container,
    $router
);

/*
|--------------------------------------------------------------------------
| Register Global Middleware
|--------------------------------------------------------------------------
*/

$kernel->middleware([
    SessionMiddleware::class,
    MaintenanceMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Handle Request
|--------------------------------------------------------------------------
*/

$response = $kernel->handle($request);

$response->send();