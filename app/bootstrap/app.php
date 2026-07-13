<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SchoolERP Bootstrap
|--------------------------------------------------------------------------
|
| Bootstraps the application by:
|   • Loading Composer
|   • Registering the global Error Handler
|   • Loading Configuration
|   • Loading Helper Files
|   • Starting the Secure Session
|
| Author: JoeyTech
| PHP Version: 8.2+
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Project Root
|--------------------------------------------------------------------------
*/

$rootPath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Composer Autoload
|--------------------------------------------------------------------------
*/

$autoload = $rootPath . '/vendor/autoload.php';

if (!is_file($autoload)) {
    throw new RuntimeException(
        'Composer autoload file not found. Run "composer install".'
    );
}

require_once $autoload;

/*
|--------------------------------------------------------------------------
| Register Global Error Handler
|--------------------------------------------------------------------------
|
| Register this as early as possible so that any exception thrown
| during bootstrap is handled by our centralized ErrorHandler.
|--------------------------------------------------------------------------
*/

$errorHandler = $rootPath . '/app/Exceptions/ErrorHandler.php';

if (!is_file($errorHandler)) {
    throw new RuntimeException(
        'ErrorHandler.php not found.'
    );
}

require_once $errorHandler;

ErrorHandler::registerGlobalHandlers();

/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/

$configFile = $rootPath . '/config/app.php';

if (!is_file($configFile)) {
    throw new RuntimeException(
        'Application configuration file not found.'
    );
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
            "Required configuration constant '{$constant}' is missing."
        );
    }
}

/*
|--------------------------------------------------------------------------
| Load Helper Files
|--------------------------------------------------------------------------
*/

$helpers = [
    $rootPath . '/app/Helpers/Session.php',
];

foreach ($helpers as $helper) {

    if (!is_file($helper)) {
        throw new RuntimeException(
            "Missing helper file: {$helper}"
        );
    }

    require_once $helper;
}

/*
|--------------------------------------------------------------------------
| Start Secure Session
|--------------------------------------------------------------------------
*/

startSecureSession();

/*
|--------------------------------------------------------------------------
| Application Ready
|--------------------------------------------------------------------------
*/

return true;