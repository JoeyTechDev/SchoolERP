<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

$title = $title ?? 'Create Term';

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
    $oldInput['name'] ?? ''
);

$sortOrder = (string) (
    $oldInput['sort_order'] ?? '1'
);

$status = (string) (
    $oldInput['status'] ?? 'active'
);

$nameError = $errors['name'] ?? null;
$sortOrderError = $errors['sort_order'] ?? null;

if (is_array($nameError)) {
    $nameError = implode(', ', $nameError);
}

if (is_array($sortOrderError)) {
    $sortOrderError = implode(', ', $sortOrderError);
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
                Add a new academic term.
            </p>

        </div>

        <a
            href="/SchoolERP/public/terms"
            class="btn btn-secondary"
        >
            Back to Terms
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/terms"
            >

                <?= csrf_field() ?>

                <div class="mb-3">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Term Name
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
                        placeholder="e.g. First Term"
                        maxlength="50"
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
                        for="sort_order"
                        class="form-label"
                    >
                        Display Order
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        class="form-control <?= $sortOrderError !== null ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars(
                            $sortOrder,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        min="1"
                        max="99"
                        required
                    >

                    <?php if ($sortOrderError !== null): ?>

                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                (string) $sortOrderError,
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
                        Create Term
                    </button>

                    <a
                        href="/SchoolERP/public/terms"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>