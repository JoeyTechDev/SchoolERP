<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Teacher $teacher
 */

if (!isset($teacher)) {
    return;
}

$teacherId = (int) (
    $teacher->id ?? 0
);

$employeeNumber = trim(
    (string) (
        $teacher->employee_number ?? ''
    )
);

$firstName = trim(
    (string) (
        $teacher->first_name ?? ''
    )
);

$lastName = trim(
    (string) (
        $teacher->last_name ?? ''
    )
);

$fullName = trim(
    $firstName
    . ' '
    . $lastName
);

if ($fullName === '') {
    $fullName = 'Unnamed Teacher';
}

$dateOfBirth = '';

if (
    $teacher->date_of_birth
    instanceof \DateTimeInterface
) {
    $dateOfBirth =
        $teacher->date_of_birth->format(
            'd M Y'
        );
} elseif (
    $teacher->date_of_birth !== null
) {
    $dateOfBirth =
        (string) $teacher->date_of_birth;
}

$gender = match (
    strtolower(
        trim(
            (string) (
                $teacher->gender ?? ''
            )
        )
    )
) {
    'male' => 'Male',
    'female' => 'Female',
    'other' => 'Other',
    default => 'Not provided',
};

$phone = trim(
    (string) (
        $teacher->phone ?? ''
    )
);

$email = trim(
    (string) (
        $teacher->email ?? ''
    )
);

$address = trim(
    (string) (
        $teacher->address ?? ''
    )
);

$employmentStatus = strtolower(
    trim(
        (string) (
            $teacher->employment_status
            ?? 'active'
        )
    )
);

$status = match (
    $employmentStatus
) {
    'active' => [
        'label' => 'Active',
        'class' => 'bg-success-subtle text-success',
    ],

    'inactive' => [
        'label' => 'Inactive',
        'class' => 'bg-secondary-subtle text-secondary',
    ],

    'suspended' => [
        'label' => 'Suspended',
        'class' => 'bg-warning-subtle text-warning-emphasis',
    ],

    'terminated' => [
        'label' => 'Terminated',
        'class' => 'bg-danger-subtle text-danger',
    ],

    default => [
        'label' => ucfirst(
            $employmentStatus !== ''
                ? $employmentStatus
                : 'Unknown'
        ),
        'class' => 'bg-light text-dark border',
    ],
};

$dateEmployed = '';

if (
    $teacher->date_employed
    instanceof \DateTimeInterface
) {
    $dateEmployed =
        $teacher->date_employed->format(
            'd M Y'
        );
} elseif (
    $teacher->date_employed !== null
) {
    $dateEmployed =
        (string) $teacher->date_employed;
}

$createdAt = '';

if (
    $teacher->created_at
    instanceof \DateTimeInterface
) {
    $createdAt =
        $teacher->created_at->format(
            'd M Y, h:i A'
        );
} elseif (
    $teacher->created_at !== null
) {
    $createdAt =
        (string) $teacher->created_at;
}

$updatedAt = '';

if (
    $teacher->updated_at
    instanceof \DateTimeInterface
) {
    $updatedAt =
        $teacher->updated_at->format(
            'd M Y, h:i A'
        );
} elseif (
    $teacher->updated_at !== null
) {
    $updatedAt =
        (string) $teacher->updated_at;
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
                Teacher Details
            </h1>

            <p class="text-muted mb-0">
                View the complete teacher profile.
            </p>

        </div>


        <div class="d-flex flex-wrap gap-2">

            <a
                href="/SchoolERP/public/teachers"
                class="btn btn-outline-secondary"
            >
                Back to Teachers
            </a>


            <a
                href="/SchoolERP/public/teachers/<?= $teacherId ?>/edit"
                class="btn btn-primary"
            >
                Edit Teacher
            </a>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PROFILE HEADER                                                 -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4">

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
            >

                <div>

                    <div class="text-muted small mb-1">
                        Teacher
                    </div>

                    <h2 class="h3 fw-bold mb-2">

                        <?= htmlspecialchars(
                            $fullName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </h2>

                    <div class="text-muted">

                        Employee Number:

                        <strong>
                            <?= $employeeNumber !== ''
                                ? htmlspecialchars(
                                    $employeeNumber,
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                                : 'Not assigned' ?>
                        </strong>

                    </div>

                </div>


                <div>

                    <span
                        class="badge <?= $status['class'] ?> fs-6"
                    >
                        <?= htmlspecialchars(
                            $status['label'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- EMPLOYMENT INFORMATION                                         -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h3 class="h5 fw-semibold mb-0">
                Employment Information
            </h3>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Employee Number
                    </div>

                    <div class="fw-semibold">

                        <?= $employeeNumber !== ''
                            ? htmlspecialchars(
                                $employeeNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                            : 'Not assigned' ?>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Employment Status
                    </div>

                    <span
                        class="badge <?= $status['class'] ?>"
                    >
                        <?= htmlspecialchars(
                            $status['label'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </div>


                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Date Employed
                    </div>

                    <div class="fw-semibold">

                        <?= $dateEmployed !== ''
                            ? htmlspecialchars(
                                $dateEmployed,
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
    <!-- PERSONAL INFORMATION                                            -->
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
                            $gender,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- CONTACT INFORMATION                                             -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h3 class="h5 fw-semibold mb-0">
                Contact Information
            </h3>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Phone Number
                    </div>

                    <?php if ($phone !== ''): ?>

                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                $phone,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php else: ?>

                        <div class="text-muted">
                            Not provided
                        </div>

                    <?php endif; ?>

                </div>


                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Email Address
                    </div>

                    <?php if ($email !== ''): ?>

                        <a
                            href="mailto:<?= htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="fw-semibold"
                        >
                            <?= htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </a>

                    <?php else: ?>

                        <div class="text-muted">
                            Not provided
                        </div>

                    <?php endif; ?>

                </div>


                <div class="col-12">

                    <div class="small text-muted mb-1">
                        Address
                    </div>

                    <?php if ($address !== ''): ?>

                        <div class="fw-semibold">
                            <?= nl2br(
                                htmlspecialchars(
                                    $address,
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>
                        </div>

                    <?php else: ?>

                        <div class="text-muted">
                            Not provided
                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- RECORD INFORMATION                                              -->
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
                        Teacher ID
                    </div>

                    <div class="fw-semibold">
                        #<?= $teacherId ?>
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
    <!-- ACTIONS                                                         -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-sm-row justify-content-between gap-2"
    >

        <a
            href="/SchoolERP/public/teachers"
            class="btn btn-outline-secondary"
        >
            Back to Teachers
        </a>


        <div class="d-flex flex-column flex-sm-row gap-2">

            <a
                href="/SchoolERP/public/teachers/<?= $teacherId ?>/edit"
                class="btn btn-primary"
            >
                Edit Teacher
            </a>


            <form
                method="POST"
                action="/SchoolERP/public/teachers/<?= $teacherId ?>/delete"
                onsubmit="return confirm('Are you sure you want to delete this teacher? This action cannot be undone.');"
            >

                <?= csrf_field() ?>

                <button
                    type="submit"
                    class="btn btn-outline-danger"
                >
                    Delete Teacher
                </button>

            </form>

        </div>

    </div>

</div>
