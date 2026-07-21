<?php

declare(strict_types=1);

use SchoolERP\Controllers\StudentController;

$router->get('/', function () {
    return 'Welcome to SchoolERP Framework!';
});

$router->get(
    '/students',
    [StudentController::class, 'index']
);

$router->get(
    '/students/{id}',
    [StudentController::class, 'show']
);