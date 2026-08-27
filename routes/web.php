<?php

declare(strict_types=1);

use SchoolERP\Controllers\DashboardController;
use SchoolERP\Controllers\ClassroomController;
use SchoolERP\Controllers\StudentController;
use SchoolERP\Controllers\SubjectController;
use SchoolERP\Controllers\AuthController;
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
| Authentication Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/auth/login',
    [AuthController::class, 'showLogin']
);

$router->post(
    '/auth/login',
    [AuthController::class, 'login']
);

$router->post(
    '/auth/logout',
    [AuthController::class, 'logout']
);

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/dashboard',
    [DashboardController::class, 'index']
);

// Classroom list
$router->get(
    '/classrooms',
    [ClassroomController::class, 'index']
);

// Create classroom form
$router->get(
    '/classrooms/create',
    [ClassroomController::class, 'create']
);

// Store classroom
$router->post(
    '/classrooms',
    [ClassroomController::class, 'store']
);

// Edit classroom form
$router->get(
    '/classrooms/{id}/edit',
    [ClassroomController::class, 'edit']
);

// Update classroom
$router->post(
    '/classrooms/{id}/update',
    [ClassroomController::class, 'update']
);

// Delete classroom
$router->post(
    '/classrooms/{id}/delete',
    [ClassroomController::class, 'destroy']
);

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
| Classroom Routes
|--------------------------------------------------------------------------
*/

// Classroom list
$router->get(
    '/classrooms',
    [ClassroomController::class, 'index']
);

// Create classroom form
$router->get(
    '/classrooms/create',
    [ClassroomController::class, 'create']
);

// Store classroom
$router->post(
    '/classrooms',
    [ClassroomController::class, 'store']
);

// Edit classroom form
$router->get(
    '/classrooms/{id}/edit',
    [ClassroomController::class, 'edit']
);

// Update classroom
$router->post(
    '/classrooms/{id}/update',
    [ClassroomController::class, 'update']
);

// Delete classroom
$router->post(
    '/classrooms/{id}/delete',
    [ClassroomController::class, 'destroy']
);

/*
|--------------------------------------------------------------------------
| Subject Routes
|--------------------------------------------------------------------------
*/

// Subject list
$router->get(
    '/subjects',
    [SubjectController::class, 'index']
);

// Create subject form
$router->get(
    '/subjects/create',
    [SubjectController::class, 'create']
);

// Store subject
$router->post(
    '/subjects',
    [SubjectController::class, 'store']
);

// Edit subject form
$router->get(
    '/subjects/{id}/edit',
    [SubjectController::class, 'edit']
);

// Update subject
$router->post(
    '/subjects/{id}/update',
    [SubjectController::class, 'update']
);

// Activate subject
$router->post(
    '/subjects/{id}/activate',
    [SubjectController::class, 'activate']
);

// Deactivate subject
$router->post(
    '/subjects/{id}/deactivate',
    [SubjectController::class, 'deactivate']
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