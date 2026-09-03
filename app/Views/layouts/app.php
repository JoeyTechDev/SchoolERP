<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/*
|--------------------------------------------------------------------------
| Current authenticated role
|--------------------------------------------------------------------------
*/

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$currentRoleId = (int) $session->get(
    'role_id',
    0
);

$isAdmin = $currentRoleId === 1;

$isTeacher = $currentRoleId === 2;

/*
|--------------------------------------------------------------------------
| Portal home
|--------------------------------------------------------------------------
*/

$homeUrl = $isTeacher
    ? '/SchoolERP/public/teacher/dashboard'
    : '/SchoolERP/public/';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="robots"
        content="noindex,nofollow"
    >

    <title>
        <?= htmlspecialchars(
            $title ?? 'SchoolERP',
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <!-- Bootstrap 5 -->
    <link
        href="/SchoolERP/public/assets/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Application Styles -->
    <style>

        body {
            background-color: #f8fafc;
            color: #212529;
        }

        .app-header {
            background: #0d6efd;
        }

        .app-header .navbar-brand {
            font-weight: 700;
        }

        .app-main {
            min-height: calc(100vh - 130px);
        }

        .app-footer {
            background: #ffffff;
            border-top: 1px solid #dee2e6;
        }

        .app-navigation .nav-link {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .app-navigation .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.12);
            border-radius: 0.375rem;
        }

        .app-navigation .nav-link.active {
            background-color: rgba(255, 255, 255, 0.16);
            border-radius: 0.375rem;
            font-weight: 600;
        }

    </style>

</head>

<body>

    <!-- ============================================================= -->
    <!-- NAVIGATION                                                     -->
    <!-- ============================================================= -->

    <header class="app-header">

        <nav class="navbar navbar-expand-lg navbar-dark">

            <div class="container-fluid px-4">

                <a
                    class="navbar-brand"
                    href="<?= htmlspecialchars(
                        $homeUrl,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                    SchoolERP
                </a>


                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNavigation"
                    aria-controls="mainNavigation"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>


                <div
                    class="collapse navbar-collapse"
                    id="mainNavigation"
                >

                    <ul
                        class="navbar-nav ms-auto align-items-lg-center gap-lg-1 app-navigation"
                    >

                        <?php if ($isTeacher): ?>

                            <!-- ===================================== -->
                            <!-- TEACHER PORTAL                        -->
                            <!-- ===================================== -->

                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/teacher/dashboard"
                                >
                                    Dashboard
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/teacher/students"
                                >
                                    My Students
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/attendance"
                                >
                                    Attendance
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/academic-results"
                                >
                                    Academic Results
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/teacher/profile"
                                >
                                    My Profile
                                </a>

                            </li>


                            <li class="nav-item">

                                <form
                                    method="POST"
                                    action="/SchoolERP/public/auth/logout"
                                    class="d-inline"
                                >

                                    <?= csrf_field() ?>

                                <button
                                        type="submit"
                                        class="nav-link btn btn-link border-0"
                                        style="text-decoration: none;"
                                    >
                                     Logout
                                    </button>

                                </form>

                            </li>


                        <?php elseif ($isAdmin): ?>

                            <!-- ===================================== -->
                            <!-- ADMINISTRATOR PORTAL                  -->
                            <!-- ===================================== -->

                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/"
                                >
                                    Dashboard
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/students"
                                >
                                    Students
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/teachers"
                                >
                                    Teachers
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/classrooms"
                                >
                                    Classrooms
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/subjects"
                                >
                                    Subjects
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/academic-sessions"
                                >
                                    Sessions
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/terms"
                                >
                                    Terms
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/attendance"
                                >
                                    Attendance
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/academic-results"
                                >
                                    Results
                                </a>

                            </li>


                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/report-card"
                                >
                                    Report Cards
                                </a>

                            </li>


                            <li class="nav-item">

                                <form
                                    method="POST"
                                    action="/SchoolERP/public/auth/logout"
                                    class="d-inline"
                                >

                                    <?= csrf_field() ?>

                                    <button
                                        type="submit"
                                        class="nav-link btn btn-link border-0"
                                        style="text-decoration: none;"
                                    >
                                        Logout
                                    </button>

                                </form>

                            </li>


                        <?php else: ?>

                            <!-- ===================================== -->
                            <!-- FALLBACK                               -->
                            <!-- ===================================== -->

                            <li class="nav-item">

                                <a
                                    class="nav-link"
                                    href="/SchoolERP/public/auth/logout"
                                >
                                    Logout
                                </a>

                            </li>

                        <?php endif; ?>

                    </ul>

                </div>

            </div>

        </nav>

    </header>


    <!-- ============================================================= -->
    <!-- MAIN CONTENT                                                   -->
    <!-- ============================================================= -->

    <main class="app-main">

        <?= $content ?>

    </main>


    <!-- ============================================================= -->
    <!-- FOOTER                                                         -->
    <!-- ============================================================= -->

    <footer class="app-footer py-3 mt-4">

        <div class="container-fluid px-4">

            <div class="text-center text-muted small">

                SchoolERP Framework
                &copy;
                <?= date('Y') ?>

            </div>

        </div>

    </footer>


    <!-- Bootstrap JavaScript -->
    <script
        src="/SchoolERP/public/assets/js/bootstrap.bundle.min.js"
    ></script>

</body>

</html>