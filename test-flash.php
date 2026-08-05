<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Session\SessionManager;

$session = new SessionManager();

$session->start();

echo "FLASH TEST\n";
echo "==========\n\n";

/*
|--------------------------------------------------------------------------
| Store Test
|--------------------------------------------------------------------------
*/

$session->flash(
    'success',
    'Student saved successfully.'
);

echo 'Store Test: ';
echo $session->hasFlash('success')
    ? "PASSED\n"
    : "FAILED\n";

/*
|--------------------------------------------------------------------------
| Retrieve Test
|--------------------------------------------------------------------------
*/

$message = $session->flash('success');

echo 'Retrieve Test: ';
echo $message === 'Student saved successfully.'
    ? "PASSED\n"
    : "FAILED\n";

/*
|--------------------------------------------------------------------------
| Auto Remove Test
|--------------------------------------------------------------------------
*/

echo 'Auto Remove Test: ';
echo !$session->hasFlash('success')
    ? "PASSED\n"
    : "FAILED\n";

/*
|--------------------------------------------------------------------------
| Clear Test
|--------------------------------------------------------------------------
*/

$session->flash('error', 'Something went wrong.');
$session->flash('warning', 'Check your input.');

$session->clearFlash();

echo 'Clear Test: ';
echo !$session->hasFlash('error')
    && !$session->hasFlash('warning')
    ? "PASSED\n"
    : "FAILED\n";

if (PHP_SAPI === 'cli') {
    $_SESSION = [];
} else {
    $session->destroy();
}