<?php

declare(strict_types=1);

use SchoolERP\Controllers\StudentController;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

$router->get('/', function () {
    return 'Welcome to SchoolERP Framework!';
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

// Student list
$router->get(
    '/students',
    [StudentController::class, 'index']
);

// Create student form
$router->get(
    '/students/create',
    [StudentController::class, 'create']
);

// Store student
$router->post(
    '/students',
    [StudentController::class, 'store']
);

// Edit student form
$router->get(
    '/students/{id}/edit',
    [StudentController::class, 'edit']
);

// Update student
$router->post(
    '/students/{id}/update',
    [StudentController::class, 'update']
);

// Delete student
$router->post(
    '/students/{id}/delete',
    [StudentController::class, 'destroy']
);

// Student details
$router->get(
    '/students/{id}',
    [StudentController::class, 'show']
);

/*
|--------------------------------------------------------------------------
| CSRF Test Routes
|--------------------------------------------------------------------------
*/

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