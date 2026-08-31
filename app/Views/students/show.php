<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 */

/*
|--------------------------------------------------------------------------
| Classroom
|--------------------------------------------------------------------------
*/

$classroom = null;

try {
    $classroom = $student->classroom()->get();
} catch (\Throwable) {
    $classroom = null;
}

/*
|--------------------------------------------------------------------------
| Student Name
|--------------------------------------------------------------------------
*/

$studentName = trim(
    (string) (
        $student->first_name ?? ''
    )
    . ' '
    . (string) (
        $student->last_name ?? ''
    )
);

/*
|--------------------------------------------------------------------------
| Date of Birth
|--------------------------------------------------------------------------
*/

$dateOfBirth = 'Not provided';

if (
    $student->date_of_birth !== null
) {
    if (
        $student->date_of_birth
        instanceof \DateTimeInterface
    ) {
        $dateOfBirth =
            $student->date_of_birth->format(
                'd M Y'
            );
    } else {
        $dateOfBirth = (string) (
            $student->date_of_birth
        );
    }
}

/*
|--------------------------------------------------------------------------
| Gender
|--------------------------------------------------------------------------
*/

$gender = match (
    strtolower(
        (string) (
            $student->gender ?? ''
        )
    )
) {

    'male' => 'Male',

    'female' => 'Female',

    'other' => 'Other',

    default => 'Not provided',

};

/*
|--------------------------------------------------------------------------
| Admission Number
|--------------------------------------------------------------------------
*/

$admissionNumber = trim(
    (string) (
        $student->admission_number ?? ''
    )
);

if ($admissionNumber === '') {
    $admissionNumber = 'Not assigned';
}

/*
|--------------------------------------------------------------------------
| Classroom
|--------------------------------------------------------------------------
*/

$classroomName = $classroom !== null
    ? trim(
        (string) (
            $classroom->name ?? ''
        )
    )
    : '';

if ($classroomName === '') {
    $classroomName = 'Not assigned';
}

/*
|--------------------------------------------------------------------------
| Created / Updated
|--------------------------------------------------------------------------
*/

$createdAt = '—';

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
    $createdAt = (string) (
        $student->created_at
    );
}

$updatedAt = '—';

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
    $updatedAt = (string) (
        $student->updated_at
    );
}

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- PAGE HEADER                                                    -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                Student Details
            </h1>

            <p class="text-muted mb-0">
                View the complete student profile and enrollment information.
            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="/SchoolERP/public/students"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Students
            </a>

            <a
                href="/SchoolERP/public/students/<?= (int) $student->id ?>/edit"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit Student
            </a>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PROFILE CARD                                                   -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2"
            >

                <div>

                    <h2 class="h5 fw-semibold mb-1">
                        <?= htmlspecialchars(
                            $studentName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p class="text-muted small mb-0">

                        Student ID:

                        <strong>
                            #<?= (int) $student->id ?>
                        </strong>

                    </p>

                </div>


                <?php if (
                    $admissionNumber !== 'Not assigned'
                ): ?>

                    <span class="badge text-bg-primary fs-6">

                        <?= htmlspecialchars(
                            $admissionNumber,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>

                <?php else: ?>

                    <span class="badge text-bg-light border">

                        Admission number not assigned

                    </span>

                <?php endif; ?>

            </div>

        </div>


        <div class="card-body p-4">


            <!-- ===================================================== -->
            <!-- IDENTIFICATION                                         -->
            <!-- ===================================================== -->

            <div class="border-bottom pb-2 mb-4">

                <h3 class="h6 fw-semibold mb-0">
                    Identification
                </h3>

            </div>


            <div class="row g-4">


                <!-- Admission Number -->
                <div class="col-md-6 col-lg-4">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Admission Number
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                $admissionNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>


                <!-- Student ID -->
                <div class="col-md-6 col-lg-4">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Student ID
                        </div>

                        <div class="fw-semibold">
                            #<?= (int) $student->id ?>
                        </div>

                    </div>

                </div>


                <!-- Classroom -->
                <div class="col-md-6 col-lg-4">

                    <div class="border rounded p-3 h-100">

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

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- PERSONAL INFORMATION                                    -->
            <!-- ===================================================== -->

            <div
                class="border-bottom pb-2 mb-4 mt-5"
            >

                <h3 class="h6 fw-semibold mb-0">
                    Personal Information
                </h3>

            </div>


            <div class="row g-4">


                <!-- First Name -->
                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            First Name
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                (string) (
                                    $student->first_name
                                    ?? '—'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>


                <!-- Last Name -->
                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Last Name
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                (string) (
                                    $student->last_name
                                    ?? '—'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>


                <!-- Date of Birth -->
                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Date of Birth
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                $dateOfBirth,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>


                <!-- Gender -->
                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Gender
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                $gender,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- ACADEMIC PLACEMENT                                      -->
            <!-- ===================================================== -->

            <div
                class="border-bottom pb-2 mb-4 mt-5"
            >

                <h3 class="h6 fw-semibold mb-0">
                    Academic Placement
                </h3>

            </div>


            <div class="row g-4">

                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Current Classroom
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                $classroomName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Classroom ID
                        </div>

                        <div class="fw-semibold">

                            <?php if (
                                $student->classroom_id
                                !== null
                            ): ?>

                                #<?= (int) (
                                    $student->classroom_id
                                ) ?>

                            <?php else: ?>

                                Not assigned

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- RECORD INFORMATION                                      -->
            <!-- ===================================================== -->

            <div
                class="border-bottom pb-2 mb-4 mt-5"
            >

                <h3 class="h6 fw-semibold mb-0">
                    Record Information
                </h3>

            </div>


            <div class="row g-4">


                <!-- Created -->
                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Created At
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                $createdAt,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>


                <!-- Updated -->
                <div class="col-md-6">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Last Updated
                        </div>

                        <div class="fw-semibold">

                            <?= htmlspecialchars(
                                $updatedAt,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- ACTIONS                                                 -->
            <!-- ===================================================== -->

            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-5 pt-4 border-top"
            >

                <a
                    href="/SchoolERP/public/students"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Students
                </a>


                <div class="d-flex gap-2">

                    <a
                        href="/SchoolERP/public/students/<?= (int) $student->id ?>/edit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-pencil me-1"></i>
                        Edit Student
                    </a>


                    <form
                        method="POST"
                        action="/SchoolERP/public/students/<?= (int) $student->id ?>/delete"
                        class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.');"
                    >

                        <?= csrf_field() ?>

                        <button
                            type="submit"
                            class="btn btn-outline-danger"
                        >
                            <i class="bi bi-trash me-1"></i>
                            Delete Student
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>