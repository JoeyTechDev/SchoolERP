<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SchoolERP Bootstrap
|--------------------------------------------------------------------------
|
| Initializes the application.
|
| Responsibilities:
| - Load Composer autoloader
| - Register global error handler
| - Load configuration
| - Validate configuration
| - Load helper files
| - Start secure session
|
*/

$rootPath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Composer Autoloader
|--------------------------------------------------------------------------
*/

$autoload = $rootPath . '/vendor/autoload.php';

if (!is_file($autoload)) {
    throw new RuntimeException(
        'Composer autoload not found. Run: composer install'
    );
}

require_once $autoload;

/*
|--------------------------------------------------------------------------
| Error Handler
|--------------------------------------------------------------------------
*/

\SchoolERP\Exceptions\ErrorHandler::registerGlobalHandlers();

/*
|--------------------------------------------------------------------------
| Application Configuration
|--------------------------------------------------------------------------
*/

$config = $rootPath . '/config/app.php';

if (!is_file($config)) {
    throw new RuntimeException(
        'config/app.php was not found.'
    );
}

require_once $config;

/*
|--------------------------------------------------------------------------
| Validate Configuration
|--------------------------------------------------------------------------
*/

$requiredConstants = [
    'APP_NAME',
    'BASE_URL'
];

foreach ($requiredConstants as $constant) {

    if (!defined($constant)) {

        throw new RuntimeException(
            sprintf(
                'Required configuration constant "%s" is missing.',
                $constant
            )
        );

    }

}

/*
|--------------------------------------------------------------------------
| Helper Files
|--------------------------------------------------------------------------
*/

$helpers = [
    '/app/Helpers/Session.php'
];

foreach ($helpers as $helper) {

    $file = $rootPath . $helper;

    if (!is_file($file)) {

        throw new RuntimeException(
            sprintf(
                'Required helper "%s" was not found.',
                $helper
            )
        );

    }

    require_once $file;

}

/*
|--------------------------------------------------------------------------
| Start Secure Session
|--------------------------------------------------------------------------
*/

startSecureSession();

/*
|--------------------------------------------------------------------------
| Bootstrap Complete
|--------------------------------------------------------------------------
*/

return true;