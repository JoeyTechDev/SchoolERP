<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

$title = $title ?? 'Create Subject';

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

$name = (string) ($oldInput['name'] ?? '');
$code = (string) ($oldInput['code'] ?? '');
$description = (string) (
    $oldInput['description'] ?? ''
);

$status = (string) (
    $oldInput['status'] ?? 'active'
);

$nameError = $errors['name'] ?? null;
$codeError = $errors['code'] ?? null;
$descriptionError = $errors['description'] ?? null;

if (is_array($nameError)) {
    $nameError = implode(', ', $nameError);
}

if (is_array($codeError)) {
    $codeError = implode(', ', $codeError);
}

if (is_array($descriptionError)) {
    $descriptionError = implode(
        ', ',
        $descriptionError
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
                Add a subject to the school curriculum.
            </p>
        </div>

        <a
            href="/SchoolERP/public/subjects"
            class="btn btn-secondary"
        >
            Back to Subjects
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/subjects"
            >

                <?= csrf_field() ?>

                <div class="mb-3">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Subject Name
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
                        placeholder="e.g. Further Mathematics"
                        maxlength="100"
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

                <div class="mb-3">

                    <label
                        for="code"
                        class="form-label"
                    >
                        Subject Code
                    </label>

                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control <?= $codeError !== null ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars(
                            $code,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="e.g. FMATH"
                        maxlength="30"
                    >

                    <?php if ($codeError !== null): ?>

                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                (string) $codeError,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="mb-3">

                    <label
                        for="description"
                        class="form-label"
                    >
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="form-control <?= $descriptionError !== null ? 'is-invalid' : '' ?>"
                        rows="4"
                        placeholder="Optional subject description"
                    ><?= htmlspecialchars(
                        $description,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                    <?php if ($descriptionError !== null): ?>

                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                (string) $descriptionError,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="mb-4">

                    <label
                        for="status"
                        class="form-label"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select"
                    >

                        <option
                            value="active"
                            <?= $status === 'active'
                                ? 'selected'
                                : '' ?>
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            <?= $status === 'inactive'
                                ? 'selected'
                                : '' ?>
                        >
                            Inactive
                        </option>

                    </select>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Create Subject
                    </button>

                    <a
                        href="/SchoolERP/public/subjects"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>