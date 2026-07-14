<?php

declare(strict_types=1);

/**
 * ---------------------------------------------------------------
 * SchoolERP Bootstrap
 * ---------------------------------------------------------------
 *
 * This file boots the entire application.
 * Every request passes through here before
 * reaching controllers or business logic.
 */

use SchoolERP\Container\Container;
use SchoolERP\Container\ContainerInterface;
use SchoolERP\Exceptions\ErrorHandler;

use function SchoolERP\Helpers\startSecureSession;

$rootPath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Composer Autoloader
|--------------------------------------------------------------------------
*/

$autoload = $rootPath . '/vendor/autoload.php';

if (!is_file($autoload)) {
    throw new RuntimeException(
        'Composer autoload file not found. Run: composer install'
    );
}

require_once $autoload;

/*
|--------------------------------------------------------------------------
| Global Error Handler
|--------------------------------------------------------------------------
*/

require_once $rootPath . '/app/Exceptions/ErrorHandler.php';

ErrorHandler::registerGlobalHandlers();

/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
*/

$configFile = $rootPath . '/config/app.php';

if (!is_file($configFile)) {
    throw new RuntimeException('config/app.php not found.');
}

require_once $configFile;

/*
|--------------------------------------------------------------------------
| Validate Required Configuration
|--------------------------------------------------------------------------
*/

foreach (['APP_NAME', 'BASE_URL'] as $constant) {
    if (!defined($constant)) {
        throw new RuntimeException(
            "Missing required configuration constant: {$constant}"
        );
    }
}

/*
|--------------------------------------------------------------------------
| Helper Files
|--------------------------------------------------------------------------
*/

$sessionHelper = $rootPath . '/app/Helpers/Session.php';

if (!is_file($sessionHelper)) {
    throw new RuntimeException(
        'Session helper not found.'
    );
}

require_once $sessionHelper;

/*
|--------------------------------------------------------------------------
| Start Session
|--------------------------------------------------------------------------
*/

startSecureSession();

/*
|--------------------------------------------------------------------------
| Dependency Injection Container
|--------------------------------------------------------------------------
*/

$container = new Container();

/*
|--------------------------------------------------------------------------
| Register Container
|--------------------------------------------------------------------------
*/

$container->instance(
    ContainerInterface::class,
    $container
);

/*
|--------------------------------------------------------------------------
| Return Application Container
|--------------------------------------------------------------------------
*/

return $container;