<?php

declare(strict_types=1);

use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Services\StudentAuthorizationService;
use SchoolERP\Session\SessionInterface;

echo "STUDENT AUTHORIZATION TEST\n";
echo "==========================\n\n";

/*
 * This test assumes the project bootstrap/container is
 * available in the same way as your existing authorization
 * test.
 */

$container = require __DIR__
    . '/bootstrap/app.php';

$session = $container->make(
    SessionInterface::class
);

$students = $container->make(
    StudentRepository::class
);

$authorization = new StudentAuthorizationService(
    $session,
    $students
);

echo 'Student Role Detection: ' .
    (
        $authorization->isStudent()
            ? 'PASSED'
            : 'FAILED'
    ) .
    PHP_EOL;