<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var \SchoolERP\Models\AcademicSession $academicSession
 * @var string|null $title
 */

$title = $title ?? 'Edit Academic Session';

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

$name = (string) (
    $oldInput['name']
    ?? $academicSession->name
    ?? ''
);

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
                Update the academic session name.
            </p>

        </div>

        <a
            href="/SchoolERP/public/academic-sessions"
            class="btn btn-secondary"
        >
            Back to Sessions
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/academic-sessions/<?= htmlspecialchars(
                    (string) $academicSession->id,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>/update"
            >

                <?= csrf_field() ?>

                <div class="mb-4">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Academic Session
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control <?= $nameError !== null ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars(
                            $name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="20"
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

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Changes
                    </button>

                    <a
                        href="/SchoolERP/public/academic-sessions"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>