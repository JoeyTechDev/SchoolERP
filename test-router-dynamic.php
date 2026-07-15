<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Http\Request;
use SchoolERP\Routing\Router;

$request = Request::capture();

$router = new Router();

$router->get('/test-router-dynamic.php', function () {
    return 'Home Page';
});

$router->get('/student/{id}', function ($request, $id) {
    return "Student ID: {$id}";
});

$router->dispatch($request);