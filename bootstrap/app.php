<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SchoolERP Bootstrap
|--------------------------------------------------------------------------
|
| This file bootstraps the application and returns the Dependency
| Injection Container.
|
*/

use SchoolERP\Container\Container;
use SchoolERP\Container\ContainerInterface;
use SchoolERP\Core\Config;

$rootPath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Composer Autoloader
|--------------------------------------------------------------------------
*/

require_once $rootPath . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Register Global Error Handler
|--------------------------------------------------------------------------
*/

use SchoolERP\Exceptions\ErrorHandler;

ErrorHandler::registerGlobalHandlers();

/*
|--------------------------------------------------------------------------
| Create Container
|--------------------------------------------------------------------------
*/

$container = new Container();

/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/

$config = new Config();

$config->load(
    'app',
    require $rootPath . '/config/app.php'
);

/*
|--------------------------------------------------------------------------
| Register Core Services
|--------------------------------------------------------------------------
*/

$container->instance(
    Config::class,
    $config
);

$container->instance(
    ContainerInterface::class,
    $container
);

/*
|--------------------------------------------------------------------------
| Return Container
|--------------------------------------------------------------------------
*/

return $container;