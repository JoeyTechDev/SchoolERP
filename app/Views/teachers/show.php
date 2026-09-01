<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Teacher $teacher
 * @var array<int,\SchoolERP\Models\TeacherAssignment> $assignments
 * @var array<int,array<string,mixed>> $classrooms
 * @var array<int,array<string,mixed>> $subjects
 */

$assignments = $assignments ?? [];
$classrooms = $classrooms ?? [];
$subjects = $subjects ?? [];

if (!isset($teacher)) {
    return;
}

/*
|--------------------------------------------------------------------------
| Flash / validation data
|--------------------------------------------------------------------------
*/

$errors = $_SESSION['_errors'] ?? [];

if (!is_array($errors)) {
    $errors = [];
}

$assignmentError = '';

if (isset($errors['assignment'])) {
    if (is_array($errors['assignment'])) {
        $assignmentError = (string) (
            $errors['assignment'][0] ?? ''
        );
    } else {
        $assignmentError = (string) $errors['assignment'];
    }
}

/*
|--------------------------------------------------------------------------
| Classroom lookup
|--------------------------------------------------------------------------
*/

$classroomLookup = [];

foreach ($classrooms as $classroom) {
    $classroomId = (int) (
        $classroom['id'] ?? 0
    );

    if ($classroomId <= 0) {
        continue;
    }

    $classroomLookup[$classroomId] = (string) (
        $classroom['name'] ?? ''
    );
}

/*
|--------------------------------------------------------------------------
| Subject lookup
|--------------------------------------------------------------------------
*/

$subjectLookup = [];

foreach ($subjects as $subject) {
    $subjectId = (int) (
        $subject['id'] ?? 0
    );

    if ($subjectId <= 0) {
        continue;
    }

    $subjectLookup[$subjectId] = [
        'name' => (string) (
            $subject['name'] ?? ''
        ),
        'code' => (string) (
            $subject['code'] ?? ''
        ),
    ];
}

/*
|--------------------------------------------------------------------------
| Teacher data
|--------------------------------------------------------------------------
*/

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

$teacherName = trim(
    $firstName . ' ' . $lastName
);

if ($teacherName === '') {
    $teacherName = 'Unnamed Teacher';
}

/*
|--------------------------------------------------------------------------
| Gender
|--------------------------------------------------------------------------
*/

$genderValue = strtolower(
    trim(
        (string) (
            $teacher->gender ?? ''
        )
    )
);

$gender = 'Not provided';

if ($genderValue === 'male') {
    $gender = 'Male';
} elseif ($genderValue === 'female') {
    $gender = 'Female';
} elseif ($genderValue === 'other') {
    $gender = 'Other';
}

/*
|--------------------------------------------------------------------------
| Employment status
|--------------------------------------------------------------------------
*/

$employmentStatus = strtolower(
    trim(
        (string) (
            $teacher->employment_status
            ?? 'active'
        )
    )
);

$statusLabel = 'Unknown';
$statusClass = 'bg-light text-dark border';

if ($employmentStatus === 'active') {
    $statusLabel = 'Active';
    $statusClass = 'bg-success-subtle text-success';
} elseif ($employmentStatus === 'inactive') {
    $statusLabel = 'Inactive';
    $statusClass = 'bg-secondary-subtle text-secondary';
} elseif ($employmentStatus === 'suspended') {
    $statusLabel = 'Suspended';
    $statusClass = 'bg-warning-subtle text-warning-emphasis';
} elseif ($employmentStatus === 'terminated') {
    $statusLabel = 'Terminated';
    $statusClass = 'bg-danger-subtle text-danger';
} elseif ($employmentStatus !== '') {
    $statusLabel = ucfirst(
        $employmentStatus
    );
}

/*
|--------------------------------------------------------------------------
| Contact
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Dates
|--------------------------------------------------------------------------
*/

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
    && $teacher->date_of_birth !== ''
) {
    $timestamp = strtotime(
        (string) $teacher->date_of_birth
    );

    if ($timestamp !== false) {
        $dateOfBirth = date(
            'd M Y',
            $timestamp
        );
    } else {
        $dateOfBirth = (string) (
            $teacher->date_of_birth
        );
    }
}

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
    && $teacher->date_employed !== ''
) {
    $timestamp = strtotime(
        (string) $teacher->date_employed
    );

    if ($timestamp !== false) {
        $dateEmployed = date(
            'd M Y',
            $timestamp
        );
    } else {
        $dateEmployed = (string) (
            $teacher->date_employed
        );
    }
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
    && $teacher->created_at !== ''
) {
    $timestamp = strtotime(
        (string) $teacher->created_at
    );

    if ($timestamp !== false) {
        $createdAt = date(
            'd M Y, h:i A',
            $timestamp
        );
    } else {
        $createdAt = (string) (
            $teacher->created_at
        );
    }
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
    && $teacher->updated_at !== ''
) {
    $timestamp = strtotime(
        (string) $teacher->updated_at
    );

    if ($timestamp !== false) {
        $updatedAt = date(
            'd M Y, h:i A',
            $timestamp
        );
    } else {
        $updatedAt = (string) (
            $teacher->updated_at
        );
    }
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
                Teacher Details
            </h1>

            <p class="text-muted mb-0">
                View teacher information and manage teaching assignments.
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
    <!-- PROFILE SUMMARY                                                -->
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
                            $teacherName,
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


                <span
                    class="badge <?= $statusClass ?> fs-6"
                >
                    <?= htmlspecialchars(
                        $statusLabel,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

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
                        class="badge <?= $statusClass ?>"
                    >
                        <?= htmlspecialchars(
                            $statusLabel,
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
    <!-- CONTACT INFORMATION                                            -->
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
                            class="fw-semibold text-decoration-none"
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
    <!-- TEACHING ASSIGNMENTS                                           -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2"
            >

                <div>

                    <h3 class="h5 fw-semibold mb-1">
                        Teaching Assignments
                    </h3>

                    <p class="text-muted small mb-0">
                        Assign this teacher to a classroom and subject.
                    </p>

                </div>


                <span class="badge bg-light text-dark border">

                    <?= count($assignments) ?>

                    assignment<?= count($assignments) === 1
                        ? ''
                        : 's' ?>

                </span>

            </div>

        </div>


        <div class="card-body p-4">

            <?php if ($assignmentError !== ''): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >

                    <?= htmlspecialchars(
                        $assignmentError,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- ===================================================== -->
            <!-- ASSIGNMENT FORM                                         -->
            <!-- ===================================================== -->

            <div class="bg-light border rounded p-4 mb-4">

                <h4 class="h6 fw-semibold mb-3">
                    Assign Classroom & Subject
                </h4>


                <form
                    method="POST"
                    action="/SchoolERP/public/teachers/<?= $teacherId ?>/assignments"
                >

                    <?= csrf_field() ?>


                    <div class="row g-3">

                        <div class="col-md-5">

                            <label
                                for="classroom_id"
                                class="form-label fw-semibold"
                            >
                                Classroom
                            </label>


                            <select
                                id="classroom_id"
                                name="classroom_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- Select Classroom --
                                </option>


                                <?php foreach (
                                    $classrooms
                                    as $classroom
                                ): ?>

                                    <?php
                                    $optionClassroomId =
                                        (int) (
                                            $classroom['id']
                                            ?? 0
                                        );

                                    $optionClassroomName =
                                        trim(
                                            (string) (
                                                $classroom['name']
                                                ?? ''
                                            )
                                        );

                                    if (
                                        $optionClassroomId <= 0
                                        || $optionClassroomName === ''
                                    ) {
                                        continue;
                                    }
                                    ?>

                                    <option
                                        value="<?= $optionClassroomId ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $optionClassroomName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="col-md-5">

                            <label
                                for="subject_id"
                                class="form-label fw-semibold"
                            >
                                Subject
                            </label>


                            <select
                                id="subject_id"
                                name="subject_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- Select Subject --
                                </option>


                                <?php foreach (
                                    $subjects
                                    as $subject
                                ): ?>

                                    <?php
                                    $optionSubjectId =
                                        (int) (
                                            $subject['id']
                                            ?? 0
                                        );

                                    $optionSubjectName =
                                        trim(
                                            (string) (
                                                $subject['name']
                                                ?? ''
                                            )
                                        );

                                    $optionSubjectCode =
                                        trim(
                                            (string) (
                                                $subject['code']
                                                ?? ''
                                            )
                                        );

                                    if (
                                        $optionSubjectId <= 0
                                        || $optionSubjectName === ''
                                    ) {
                                        continue;
                                    }
                                    ?>

                                    <option
                                        value="<?= $optionSubjectId ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $optionSubjectName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                        <?php if (
                                            $optionSubjectCode !== ''
                                        ): ?>

                                            —
                                            <?= htmlspecialchars(
                                                $optionSubjectCode,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        <?php endif; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="col-md-2 d-flex align-items-end">

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Assign
                            </button>

                        </div>

                    </div>

                </form>

            </div>


            <!-- ===================================================== -->
            <!-- ASSIGNMENT LIST                                         -->
            <!-- ===================================================== -->

            <?php if (empty($assignments)): ?>

                <div class="text-center py-4">

                    <p class="text-muted mb-1">
                        No teaching assignments yet.
                    </p>

                    <small class="text-muted">
                        Use the form above to assign a classroom and subject.
                    </small>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Classroom
                                </th>

                                <th>
                                    Subject
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach (
                                $assignments
                                as $assignment
                            ): ?>

                                <?php

                                $assignmentId = (int) (
                                    $assignment->id ?? 0
                                );

                                $assignmentClassroomId = (int) (
                                    $assignment->classroom_id
                                    ?? 0
                                );

                                $assignmentSubjectId = (int) (
                                    $assignment->subject_id
                                    ?? 0
                                );

                                $classroomName =
                                    $classroomLookup[
                                        $assignmentClassroomId
                                    ] ?? '';

                                if (
                                    $classroomName === ''
                                ) {
                                    $classroomName =
                                        $assignmentClassroomId > 0
                                            ? 'Classroom #'
                                                . $assignmentClassroomId
                                            : 'Unknown Classroom';
                                }

                                $subjectName =
                                    $subjectLookup[
                                        $assignmentSubjectId
                                    ]['name'] ?? '';

                                $subjectCode =
                                    $subjectLookup[
                                        $assignmentSubjectId
                                    ]['code'] ?? '';

                                if (
                                    $subjectName === ''
                                ) {
                                    $subjectName =
                                        $assignmentSubjectId > 0
                                            ? 'Subject #'
                                                . $assignmentSubjectId
                                            : 'Unknown Subject';
                                }

                                $assignmentIsActive =
                                    (int) (
                                        $assignment->is_active
                                        ?? 0
                                    ) === 1;

                                ?>

                                <tr>

                                    <td>

                                        <?= $assignmentId ?>

                                    </td>


                                    <td>

                                        <span class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $classroomName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $subjectName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </div>


                                        <?php if (
                                            $subjectCode !== ''
                                        ): ?>

                                            <small class="text-muted">

                                                <?= htmlspecialchars(
                                                    $subjectCode,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </small>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?php if (
                                            $assignmentIsActive
                                        ): ?>

                                            <span
                                                class="badge bg-success-subtle text-success"
                                            >
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span
                                                class="badge bg-secondary-subtle text-secondary"
                                            >
                                                Inactive
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td class="text-end">

                                        <form
                                            method="POST"
                                            action="/SchoolERP/public/teachers/<?= $teacherId ?>/assignments/<?= $assignmentId ?>/delete"
                                            onsubmit="return confirm('Remove this teaching assignment?');"
                                            class="d-inline"
                                        >

                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                            >
                                                Remove
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

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
    <!-- PAGE ACTIONS                                                    -->
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
