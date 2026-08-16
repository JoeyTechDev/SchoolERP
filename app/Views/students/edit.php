<?php

declare(strict_types=1);

/**
 * Existing student data.
 */
$studentId = (int) $student->id;

$firstName = old(
    'first_name',
    $student->first_name ?? ''
);

$lastName = old(
    'last_name',
    $student->last_name ?? ''
);

$classroomId = old(
    'classroom_id',
    $student->classroom_id ?? ''
);
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Edit Student
            </h1>

            <p class="text-muted mb-0">
                Update the student's information.
            </p>
        </div>

        <a
            href="/SchoolERP/public/students/<?= $studentId ?>"
            class="btn btn-secondary"
        >
            Back to Student
        </a>

    </div>

    <?php if (has_errors()): ?>

        <div
            class="alert alert-danger"
            role="alert"
        >

            <h5 class="alert-heading">
                Please correct the following errors:
            </h5>

            <ul class="mb-0">

                <?php foreach (validation_errors() as $messages): ?>

                    <?php foreach ($messages as $message): ?>

                        <li>
                            <?= htmlspecialchars(
                                $message,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </li>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/students/<?= $studentId ?>/update"
            >

                <?= csrf_field() ?>

                <div class="mb-3">

                    <label
                        for="first_name"
                        class="form-label"
                    >
                        First Name
                    </label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="<?= htmlspecialchars(
                            (string) $firstName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="form-control <?= has_error('first_name')
                            ? 'is-invalid'
                            : '' ?>"
                        required
                    >

                    <?php if (has_error('first_name')): ?>

                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                first_error('first_name'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="mb-3">

                    <label
                        for="last_name"
                        class="form-label"
                    >
                        Last Name
                    </label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="<?= htmlspecialchars(
                            (string) $lastName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="form-control <?= has_error('last_name')
                            ? 'is-invalid'
                            : '' ?>"
                        required
                    >

                    <?php if (has_error('last_name')): ?>

                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                first_error('last_name'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="mb-3">

                    <label
                        for="classroom_id"
                        class="form-label"
                    >
                        Classroom ID
                    </label>

                    <input
                        type="number"
                        id="classroom_id"
                        name="classroom_id"
                        value="<?= htmlspecialchars(
                            (string) $classroomId,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="form-control <?= has_error('classroom_id')
                            ? 'is-invalid'
                            : '' ?>"
                        min="1"
                    >

                    <div class="form-text">
                        Enter the ID of the student's classroom.
                    </div>

                    <?php if (has_error('classroom_id')): ?>

                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                first_error('classroom_id'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Student
                    </button>

                    <a
                        href="/SchoolERP/public/students/<?= $studentId ?>"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>