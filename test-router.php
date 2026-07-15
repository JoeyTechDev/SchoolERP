<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Routing\Router;

$request = Request::capture();

$router = new Router();

$router->get('/home', function (Request $request): Response {

    return Response::make()
        ->content('Welcome to SchoolERP Framework!');
});

$router->dispatch($request);