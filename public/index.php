<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SchoolERP Front Controller
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__) . '/bootstrap/app.php';

$loginUrl = rtrim(BASE_URL, '/') . '/auth/login.php';

if (!headers_sent()) {

    header('Location: ' . $loginUrl);

    exit;
}

throw new RuntimeException(
    'Headers have already been sent. Unable to redirect to login page.'
);