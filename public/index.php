<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * SchoolERP
 * --------------------------------------------------------------------------
 * Front Controller
 *
 * This is the application's main entry point.
 * Every request should pass through this file.
 *
 * Responsibilities:
 * - Load configuration
 * - Register global error handlers
 * - Start secure session
 * - Redirect authenticated users
 * - Redirect guests to login
 * --------------------------------------------------------------------------
 */

require_once __DIR__ . '/../config/app.php';

require_once __DIR__ . '/../app/Helpers/Session.php';

require_once __DIR__ . '/../app/Exceptions/ErrorHandler.php';

/*
|--------------------------------------------------------------------------
| Register Global Error Handlers
|--------------------------------------------------------------------------
*/

ErrorHandler::registerGlobalHandlers();

/*
|--------------------------------------------------------------------------
| Start Secure Session
|--------------------------------------------------------------------------
*/

startSecureSession();

/*
|--------------------------------------------------------------------------
| Redirect User
|--------------------------------------------------------------------------
*/

if (isLoggedIn()) {

    header('Location: ' . BASE_URL . '/dashboard/');
    exit;
}

header('Location: ' . BASE_URL . '/auth/login.php');
exit;