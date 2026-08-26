<?php

declare(strict_types=1);

/**
 * @var array<string,mixed> $user
 */

$firstName = (string) (
    $user['first_name'] ?? ''
);

$lastName = (string) (
    $user['last_name'] ?? ''
);

$fullName = trim(
    $firstName . ' ' . $lastName
);

$roleNames = [
    1 => 'Administrator',
    2 => 'Teacher',
    3 => 'Student',
    4 => 'Parent',
    5 => 'Accountant',
    6 => 'Librarian',
];

$roleId = (int) (
    $user['role_id'] ?? 0
);

$roleName = $roleNames[$roleId] ?? 'User';
?>

<div class="container-fluid py-4">

    <div class="mb-4">

        <h1 class="h3 fw-bold mb-1">
            Dashboard
        </h1>

        <p class="text-muted mb-0">
            Welcome back,
            <?= htmlspecialchars(
                $fullName !== ''
                    ? $fullName
                    : 'User',
                ENT_QUOTES,
                'UTF-8'
            ) ?>.
        </p>

    </div>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <h2 class="h6 text-muted">
                        Signed In As
                    </h2>

                    <div class="fs-5 fw-semibold">
                        <?= htmlspecialchars(
                            $fullName !== ''
                                ? $fullName
                                : 'User',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                    <div class="text-muted small mt-1">
                        <?= htmlspecialchars(
                            $roleName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <h2 class="h6 text-muted">
                        Email
                    </h2>

                    <div class="fw-semibold">
                        <?= htmlspecialchars(
                            (string) (
                                $user['email'] ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <h2 class="h6 text-muted">
                        Quick Access
                    </h2>

                    <div class="d-flex gap-2 flex-wrap">

                        <a
                            href="/SchoolERP/public/students"
                            class="btn btn-primary btn-sm"
                        >
                            Students
                        </a>

                        <a
                            href="/SchoolERP/public/classrooms"
                            class="btn btn-outline-primary btn-sm"
                        >
                            Classrooms
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card border-0 shadow-sm mt-4">

        <div class="card-body">

            <h2 class="h5 fw-semibold mb-2">
                SchoolERP
            </h2>

            <p class="text-muted mb-0">
                Your school management system is ready for the
                next modules: attendance, results, fees, staff,
                and more.
            </p>

        </div>

    </div>

    <div class="mt-4">

        <form
            method="POST"
            action="/SchoolERP/public/auth/logout"
        >

            <?= csrf_field() ?>

            <button
                type="submit"
                class="btn btn-outline-danger"
            >
                Sign Out
            </button>

        </form>

    </div>

</div>