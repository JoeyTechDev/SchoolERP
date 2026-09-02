<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 */

if (!isset($student)) {
    return;
}

$studentId = (int) (
    $student->id ?? 0
);

$admissionNumber = trim(
    (string) (
        $student->admission_number ?? ''
    )
);

$firstName = trim(
    (string) (
        $student->first_name ?? ''
    )
);

$lastName = trim(
    (string) (
        $student->last_name ?? ''
    )
);

$studentName = trim(
    $firstName
    . ' '
    . $lastName
);

if ($studentName === '') {
    $studentName = 'Unnamed Student';
}

$classroomName = '';

$classroom = $student->classroom
    ?? null;

if (
    is_object($classroom)
) {
    $classroomName = trim(
        (string) (
            $classroom->name ?? ''
        )
    );
}

if ($classroomName === '') {
    $classroomName =
        'Not assigned';
}

$dateOfBirth = '';

if (
    $student->date_of_birth
    instanceof \DateTimeInterface
) {
    $dateOfBirth =
        $student->date_of_birth->format(
            'd M Y'
        );
} elseif (
    $student->date_of_birth !== null
    && $student->date_of_birth !== ''
) {
    $dateOfBirth =
        (string) $student->date_of_birth;
}

$gender = strtolower(
    trim(
        (string) (
            $student->gender ?? ''
        )
    )
);

$genderLabel = match ($gender) {
    'male' => 'Male',
    'female' => 'Female',
    'other' => 'Other',
    default => 'Not provided',
};

$createdAt = '';

if (
    $student->created_at
    instanceof \DateTimeInterface
) {
    $createdAt =
        $student->created_at->format(
            'd M Y, h:i A'
        );
} elseif (
    $student->created_at !== null
) {
    $createdAt =
        (string) $student->created_at;
}

$updatedAt = '';

if (
    $student->updated_at
    instanceof \DateTimeInterface
) {
    $updatedAt =
        $student->updated_at->format(
            'd M Y, h:i A'
        );
} elseif (
    $student->updated_at !== null
) {
    $updatedAt =
        (string) $student->updated_at;
}

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- HEADER                                                         -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                Student Details
            </h1>

            <p class="text-muted mb-0">
                View student information from your assigned classroom.
            </p>

        </div>


        <a
            href="/SchoolERP/public/teacher/students"
            class="btn btn-outline-secondary"
        >
            Back to My Students
        </a>

    </div>


    <!-- ============================================================= -->
    <!-- PROFILE                                                        -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4">

            <div>

                <div class="text-muted small mb-1">
                    Student
                </div>

                <h2 class="h3 fw-bold mb-2">

                    <?= htmlspecialchars(
                        $studentName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </h2>

                <?php if (
                    $admissionNumber !== ''
                ): ?>

                    <div class="text-muted">

                        Admission Number:

                        <strong>

                            <?= htmlspecialchars(
                                $admissionNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </strong>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- ACADEMIC INFORMATION                                           -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h3 class="h5 fw-semibold mb-0">
                Academic Information
            </h3>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Classroom
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $classroomName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Admission Number
                    </div>

                    <div class="fw-semibold">

                        <?= $admissionNumber !== ''
                            ? htmlspecialchars(
                                $admissionNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                            : 'Not provided' ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PERSONAL INFORMATION                                           -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h3 class="h5 fw-semibold mb-0">
                Personal Information
            </h3>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        First Name
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $firstName !== ''
                                ? $firstName
                                : 'Not provided',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Last Name
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $lastName !== ''
                                ? $lastName
                                : 'Not provided',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Date of Birth
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $dateOfBirth !== ''
                                ? $dateOfBirth
                                : 'Not provided',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Gender
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $genderLabel,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- RECORD INFORMATION                                             -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h3 class="h5 fw-semibold mb-0">
                Record Information
            </h3>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Student ID
                    </div>

                    <div class="fw-semibold">
                        #<?= $studentId ?>
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Created At
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $createdAt !== ''
                                ? $createdAt
                                : '—',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Last Updated
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $updatedAt !== ''
                                ? $updatedAt
                                : '—',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- QUICK ACTIONS                                                  -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom">

            <h3 class="h5 fw-semibold mb-0">
                Quick Actions
            </h3>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <a
                        href="/SchoolERP/public/academic-results?student_id=<?= $studentId ?>"
                        class="btn btn-outline-primary w-100"
                    >
                        View Academic Results
                    </a>

                </div>


                <div class="col-md-6">

                    <a
                        href="/SchoolERP/public/attendance-history?student_id=<?= $studentId ?>"
                        class="btn btn-outline-secondary w-100"
                    >
                        View Attendance History
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>