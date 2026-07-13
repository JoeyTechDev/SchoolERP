<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SchoolERP Front Controller
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__) . '/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Temporary Redirect
|--------------------------------------------------------------------------
*/

if (!headers_sent()) {

    header(
        'Location: ' . rtrim(BASE_URL, '/') . '/auth/login.php'
    );

    exit;
}

throw new RuntimeException(
    'Headers already sent. Unable to redirect to login page.'
);