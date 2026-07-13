<?php

declare(strict_types=1);

/**
 * ---------------------------------------------------------------
 * SchoolERP Front Controller
 * ---------------------------------------------------------------
 */

$container = require dirname(__DIR__) . '/bootstrap/app.php';

if (!headers_sent()) {
    header(
        'Location: ' .
        rtrim(BASE_URL, '/') .
        '/auth/login.php'
    );
}

exit;