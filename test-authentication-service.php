<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\UserRepository;
use SchoolERP\Services\AuthenticationService;
use SchoolERP\Session\SessionManager;

$session = new SessionManager();

$email = trim(
    (string) readline('Email: ')
);

$password = (string) readline('Password: ');

$authentication = new AuthenticationService(
    new UserRepository(),
    $session
);

ob_start();

$authenticated = $authentication->attempt(
    $email,
    $password
);

ob_end_clean();

echo PHP_EOL;
echo "AUTHENTICATION SERVICE TEST\n";
echo "===========================\n\n";

if (!$authenticated) {
    echo "Authentication: FAILED\n";
    exit(1);
}

echo "Authentication: PASSED\n";
echo 'User ID: ' . $authentication->userId() . PHP_EOL;
echo 'Role ID: ' . $authentication->roleId() . PHP_EOL;

$authentication->logout();

echo "Logout: PASSED\n";
echo "\nAUTHENTICATION SERVICE TEST COMPLETE\n";