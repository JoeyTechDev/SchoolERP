<?php

declare(strict_types=1);

use SchoolERP\Controllers\DashboardController;
use SchoolERP\Controllers\ClassroomController;
use SchoolERP\Controllers\StudentController;
use SchoolERP\Controllers\SubjectController;
use SchoolERP\Controllers\AcademicSessionController;
use SchoolERP\Controllers\AcademicResultController;
use SchoolERP\Controllers\ReportCardController;
use SchoolERP\Controllers\TermController;
use SchoolERP\Controllers\AttendanceController;
use SchoolERP\Controllers\AttendanceHistoryController;
use SchoolERP\Controllers\TeacherController;
use SchoolERP\Controllers\TeacherPortalController;
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
| Academic Session Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/academic-sessions',
    [AcademicSessionController::class, 'index']
);

$router->get(
    '/academic-sessions/create',
    [AcademicSessionController::class, 'create']
);

$router->post(
    '/academic-sessions',
    [AcademicSessionController::class, 'store']
);

$router->get(
    '/academic-sessions/{id}/edit',
    [AcademicSessionController::class, 'edit']
);

$router->post(
    '/academic-sessions/{id}/update',
    [AcademicSessionController::class, 'update']
);

$router->post(
    '/academic-sessions/{id}/current',
    [AcademicSessionController::class, 'setCurrent']
);

$router->post(
    '/academic-sessions/{id}/activate',
    [AcademicSessionController::class, 'activate']
);

$router->post(
    '/academic-sessions/{id}/deactivate',
    [AcademicSessionController::class, 'deactivate']
);

/*
|--------------------------------------------------------------------------
| Term Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/terms',
    [TermController::class, 'index']
);

$router->get(
    '/terms/create',
    [TermController::class, 'create']
);

$router->post(
    '/terms',
    [TermController::class, 'store']
);

$router->get(
    '/terms/{id}/edit',
    [TermController::class, 'edit']
);

$router->post(
    '/terms/{id}/update',
    [TermController::class, 'update']
);

$router->post(
    '/terms/{id}/activate',
    [TermController::class, 'activate']
);

$router->post(
    '/terms/{id}/deactivate',
    [TermController::class, 'deactivate']
);

/*
|--------------------------------------------------------------------------
| Academic Result Routes
|--------------------------------------------------------------------------
*/

// Academic result list/filter
$router->get(
    '/academic-results',
    [AcademicResultController::class, 'index']
);

// Create result form
$router->get(
    '/academic-results/create',
    [AcademicResultController::class, 'create']
);

// Store result
$router->post(
    '/academic-results',
    [AcademicResultController::class, 'store']
);

// Edit result form
$router->get(
    '/academic-results/{id}/edit',
    [AcademicResultController::class, 'edit']
);

// Update result
$router->post(
    '/academic-results/{id}/update',
    [AcademicResultController::class, 'update']
);

// Delete result
$router->post(
    '/academic-results/{id}/delete',
    [AcademicResultController::class, 'destroy']
);

/*
|--------------------------------------------------------------------------
| Report Card Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/report-card/print',
    [ReportCardController::class, 'print']
);

$router->get(
    '/report-card',
    [ReportCardController::class, 'index']
);

$router->post(
    '/report-card/summary',
    [ReportCardController::class, 'saveSummary']
);

/*
|--------------------------------------------------------------------------
| Attendance Routes
|--------------------------------------------------------------------------
*/

// Daily attendance
$router->get(
    '/attendance',
    [AttendanceController::class, 'index']
);

// Save classroom attendance
$router->post(
    '/attendance',
    [AttendanceController::class, 'store']
);

/*
|--------------------------------------------------------------------------
| Attendance History Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/attendance/history',
    [AttendanceHistoryController::class, 'index']
);

$router->post(
    '/report-card/summary',
    [ReportCardController::class, 'saveSummary']
);

$router->get(
    '/report-card/print',
    [ReportCardController::class, 'print']
);

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/teachers',
    [TeacherController::class, 'index']
);

$router->get(
    '/teachers/create',
    [TeacherController::class, 'create']
);

$router->post(
    '/teachers',
    [TeacherController::class, 'store']
);

$router->get(
    '/teachers/{id}/edit',
    [TeacherController::class, 'edit']
);

$router->post(
    '/teachers/{id}/update',
    [TeacherController::class, 'update']
);

$router->post(
    '/teachers/{id}/delete',
    [TeacherController::class, 'destroy']
);

$router->get(
    '/teachers/{id}',
    [TeacherController::class, 'show']
);

$router->post(
    '/teachers/{id}/assignments',
    [TeacherController::class, 'storeAssignment']
);

$router->post(
    '/teachers/{id}/assignments/{assignmentId}/delete',
    [TeacherController::class, 'destroyAssignment']
);

/*
|--------------------------------------------------------------------------
| Teacher Portal
|--------------------------------------------------------------------------
*/

$router->get(
    '/teacher',
    [TeacherPortalController::class, 'dashboard']
);

$router->get(
    '/teacher/dashboard',
    [TeacherPortalController::class, 'dashboard']
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