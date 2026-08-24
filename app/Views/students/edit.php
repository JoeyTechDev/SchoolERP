<?php

declare(strict_types=1);

/**
 * @var object $student
 * @var array<int,array<string,mixed>> $classrooms
 */

$classrooms = $classrooms ?? [];

$oldInput = $GLOBALS['container']
    ->make(\SchoolERP\Session\SessionInterface::class)
    ->get('_old_input', []);

$errors = $GLOBALS['container']
    ->make(\SchoolERP\Session\SessionInterface::class)
    ->get('_errors', []);

$oldInput = is_array($oldInput) ? $oldInput : [];
$errors = is_array($errors) ? $errors : [];

$currentClassroomId = $oldInput['classroom_id']
    ?? $student->classroom_id
    ?? '';
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Edit Student
            </h1>

            <p class="text-muted mb-0">
                Update student information.
            </p>
        </div>

        <a
            href="/SchoolERP/public/students/<?= htmlspecialchars(
                (string) $student->id,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="btn btn-secondary"
        >
            Back to Student
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/students/<?= htmlspecialchars(
                    (string) $student->id,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>/update"
            >

                <?= csrf_field() ?>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label
                            for="first_name"
                            class="form-label"
                        >
                            First Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="first_name"
                            name="first_name"
                            value="<?= htmlspecialchars(
                                (string) (
                                    $oldInput['first_name']
                                    ?? $student->first_name
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            required
                        >

                        <?php if (isset($errors['first_name'])): ?>

                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars(
                                    (string) $errors['first_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label
                            for="last_name"
                            class="form-label"
                        >
                            Last Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="last_name"
                            name="last_name"
                            value="<?= htmlspecialchars(
                                (string) (
                                    $oldInput['last_name']
                                    ?? $student->last_name
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            required
                        >

                        <?php if (isset($errors['last_name'])): ?>

                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars(
                                    (string) $errors['last_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="mb-3">

                    <label
                        for="classroom_id"
                        class="form-label"
                    >
                        Classroom
                    </label>

                    <select
                        class="form-select"
                        id="classroom_id"
                        name="classroom_id"
                    >

                        <option value="">
                            -- Select Classroom --
                        </option>

                        <?php foreach ($classrooms as $classroom): ?>

                            <option
                                value="<?= htmlspecialchars(
                                    (string) $classroom['id'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                <?= (string) $currentClassroomId ===
                                    (string) $classroom['id']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    (string) $classroom['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <?php if (isset($errors['classroom_id'])): ?>

                        <div class="text-danger small mt-1">
                            <?= htmlspecialchars(
                                (string) $errors['classroom_id'],
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
                        href="/SchoolERP/public/students/<?= htmlspecialchars(
                            (string) $student->id,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>
