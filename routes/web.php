<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get('/', function () {
    return 'Welcome to SchoolERP Framework!';
});

$router->get('/student/{id}', function ($request, string $id) {
    return "Student ID: {$id}";
});