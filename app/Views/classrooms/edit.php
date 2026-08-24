<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var \SchoolERP\Models\Classroom $classroom
 * @var string|null $title
 */

$title = $title ?? 'Edit Classroom';

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$oldInput = $session->flash('_old_input') ?? [];
$errors = $session->flash('_errors') ?? [];

$oldInput = is_array($oldInput)
    ? $oldInput
    : [];

$errors = is_array($errors)
    ? $errors
    : [];

$name = $oldInput['name']
    ?? $classroom->name
    ?? '';

$nameError = $errors['name'] ?? null;

if (is_array($nameError)) {
    $nameError = implode(
        ', ',
        $nameError
    );
}
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                <?= htmlspecialchars(
                    $title,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <p class="text-muted mb-0">
                Change the classroom name without changing student assignments.
            </p>
        </div>

        <a
            href="/SchoolERP/public/classrooms"
            class="btn btn-secondary"
        >
            Back to Classrooms
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/classrooms/<?= htmlspecialchars(
                    (string) $classroom->id,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>/update"
            >

                <?= csrf_field() ?>

                <div class="mb-3">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Classroom Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control <?= $nameError !== null ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars(
                            (string) $name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="100"
                        autocomplete="off"
                        required
                    >

                    <?php if ($nameError !== null): ?>

                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                (string) $nameError,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                    <div class="form-text">
                        You can rename a classroom at any time.
                        Students assigned to it will remain assigned to the same classroom.
                    </div>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Changes
                    </button>

                    <a
                        href="/SchoolERP/public/classrooms"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>