<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Teacher $teacher
 * @var string $teacherName
 * @var array<int,array<string,mixed>> $classroomCards
 * @var array<int,array<string,mixed>> $mySubjects
 * @var int $totalStudents
 * @var int $totalClassrooms
 * @var int $totalSubjects
 */

$teacherName = (string) (
    $teacherName ?? 'Teacher'
);

$classroomCards =
    $classroomCards ?? [];

$mySubjects =
    $mySubjects ?? [];

$totalStudents =
    (int) (
        $totalStudents ?? 0
    );

$totalClassrooms =
    (int) (
        $totalClassrooms ?? 0
    );

$totalSubjects =
    (int) (
        $totalSubjects ?? 0
    );

$firstName = trim(
    (string) (
        $teacher->first_name ?? ''
    )
);

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- WELCOME                                                        -->
    <!-- ============================================================= -->

    <div class="mb-4">

        <h1 class="h3 fw-bold mb-1">

            Welcome,
            <?= htmlspecialchars(
                $firstName !== ''
                    ? $firstName
                    : $teacherName,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </h1>

        <p class="text-muted mb-0">
            Manage your assigned classrooms, subjects, attendance, and academic results.
        </p>

    </div>


    <!-- ============================================================= -->
    <!-- SUMMARY CARDS                                                   -->
    <!-- ============================================================= -->

    <div class="row g-3 mb-4">

        <!-- Classrooms -->

        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        My Classrooms
                    </div>

                    <div class="fs-2 fw-bold">
                        <?= $totalClassrooms ?>
                    </div>

                    <div class="small text-muted">
                        Assigned classrooms
                    </div>

                </div>

            </div>

        </div>


        <!-- Subjects -->

        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        My Subjects
                    </div>

                    <div class="fs-2 fw-bold">
                        <?= $totalSubjects ?>
                    </div>

                    <div class="small text-muted">
                        Subjects you teach
                    </div>

                </div>

            </div>

        </div>


        <!-- Students -->

        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        My Students
                    </div>

                    <div class="fs-2 fw-bold">
                        <?= $totalStudents ?>
                    </div>

                    <div class="small text-muted">
                        Students in assigned classrooms
                    </div>

                </div>

            </div>

        </div>


        <!-- Assignments -->

        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Active Assignments
                    </div>

                    <div class="fs-2 fw-bold">
                        <?= count($assignments ?? []) ?>
                    </div>

                    <div class="small text-muted">
                        Classroom + subject assignments
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- QUICK ACTIONS                                                   -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-0">
                Quick Actions
            </h2>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <a
                        href="/SchoolERP/public/attendance"
                        class="btn btn-outline-primary w-100 py-3"
                    >

                        Daily Attendance

                    </a>

                </div>


                <div class="col-md-6">

                    <a
                        href="/SchoolERP/public/academic-results"
                        class="btn btn-outline-primary w-100 py-3"
                    >

                        Academic Results

                    </a>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- MY CLASSROOMS                                                  -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-1">
                My Classrooms
            </h2>

            <p class="text-muted small mb-0">
                Classrooms available through your active teaching assignments.
            </p>

        </div>


        <div class="card-body">

            <?php if (
                empty($classroomCards)
            ): ?>

                <div class="text-center py-5">

                    <h3 class="h6 fw-semibold">
                        No classroom assignments
                    </h3>

                    <p class="text-muted mb-0">
                        An administrator has not assigned you to a classroom yet.
                    </p>

                </div>

            <?php else: ?>

                <div class="row g-3">

                    <?php foreach (
                        $classroomCards
                        as $classroom
                    ): ?>

                        <?php

                        $classroomId =
                            (int) (
                                $classroom['id']
                                ?? 0
                            );

                        $classroomName =
                            (string) (
                                $classroom['name']
                                ?? 'Classroom'
                            );

                        $studentCount =
                            (int) (
                                $classroom[
                                    'student_count'
                                ]
                                ?? 0
                            );

                        $classSubjects =
                            $classroom[
                                'subjects'
                            ]
                            ?? [];

                        ?>

                        <div class="col-md-6 col-xl-4">

                            <div
                                class="card border h-100"
                            >

                                <div class="card-body">

                                    <div
                                        class="d-flex justify-content-between align-items-start gap-3 mb-3"
                                    >

                                        <div>

                                            <h3
                                                class="h5 fw-bold mb-1"
                                            >

                                                <?= htmlspecialchars(
                                                    $classroomName,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </h3>

                                            <div
                                                class="small text-muted"
                                            >

                                                <?= $studentCount ?>

                                                student<?= $studentCount === 1
                                                    ? ''
                                                    : 's' ?>

                                            </div>

                                        </div>


                                        <span
                                            class="badge bg-success-subtle text-success"
                                        >
                                            Active
                                        </span>

                                    </div>


                                    <div class="mb-3">

                                        <div
                                            class="small text-muted mb-2"
                                        >
                                            Subjects
                                        </div>


                                        <div
                                            class="d-flex flex-wrap gap-1"
                                        >

                                            <?php foreach (
                                                $classSubjects
                                                as $subject
                                            ): ?>

                                                <span
                                                    class="badge bg-light text-dark border"
                                                >

                                                    <?= htmlspecialchars(
                                                        (string) (
                                                            $subject['name']
                                                            ?? ''
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </span>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>


                                    <div
                                        class="d-flex flex-column gap-2 mt-auto"
                                    >

                                        <a
                                            href="/SchoolERP/public/attendance?classroom_id=<?= $classroomId ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Take Attendance
                                        </a>


                                        <a
                                            href="/SchoolERP/public/academic-results"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            Manage Results
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- MY SUBJECTS                                                    -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-1">
                My Subjects
            </h2>

            <p class="text-muted small mb-0">
                Subjects currently assigned to you.
            </p>

        </div>


        <div class="card-body">

            <?php if (
                empty($mySubjects)
            ): ?>

                <div class="text-center py-4">

                    <p class="text-muted mb-0">
                        No active subject assignments.
                    </p>

                </div>

            <?php else: ?>

                <div class="row g-3">

                    <?php foreach (
                        $mySubjects
                        as $subject
                    ): ?>

                        <div class="col-sm-6 col-lg-4">

                            <div
                                class="border rounded p-3 h-100"
                            >

                                <div
                                    class="fw-semibold mb-1"
                                >

                                    <?= htmlspecialchars(
                                        (string) (
                                            $subject['name']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>


                                <?php
                                $subjectCode =
                                    trim(
                                        (string) (
                                            $subject['code']
                                            ?? ''
                                        )
                                    );
                                ?>


                                <?php if (
                                    $subjectCode !== ''
                                ): ?>

                                    <div
                                        class="small text-muted"
                                    >

                                        Code:

                                        <?= htmlspecialchars(
                                            $subjectCode,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>