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

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

$router->get('/csrf-test', function () {

    return Response::make(
        '
        <h2>CSRF Test Form</h2>

        <form method="POST" action="">

            ' . csrf_field() . '

            <input
                type="text"
                name="name"
                placeholder="Your Name"
            >

            <button type="submit">
                Submit
            </button>

        </form>
        '
    );
});

$router->post('/csrf-test', function (Request $request) {

    return Response::make(
        'CSRF Verification Passed!'
    );

});