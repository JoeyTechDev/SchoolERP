<?php

declare(strict_types=1);

/**
 * @var array<string,mixed> $oldInput
 * @var array<string,mixed> $errors
 */

$oldInput = $_SESSION['_old_input']
    ?? [];

$errors = $_SESSION['_errors']
    ?? [];

$oldInput = is_array($oldInput)
    ? $oldInput
    : [];

$errors = is_array($errors)
    ? $errors
    : [];

$error = static function (
    string $field
) use (
    $errors
): string {

    $value = $errors[$field] ?? '';

    if (is_array($value)) {
        $value = $value[0] ?? '';
    }

    return is_string($value)
        ? $value
        : '';
};

$value = static function (
    string $field,
    string $default = ''
) use (
    $oldInput
): string {

    $value = $oldInput[$field]
        ?? $default;

    return $value === null
        ? ''
        : (string) $value;
};

$class = static function (
    string $field
) use (
    $error
): string {
    return $error($field) !== ''
        ? ' is-invalid'
        : '';
};

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
                Create Teacher
            </h1>

            <p class="text-muted mb-0">
                Register a new teacher or member of the teaching staff.
            </p>

        </div>


        <a
            href="/SchoolERP/public/teachers"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Teachers
        </a>

    </div>


    <!-- ============================================================= -->
    <!-- FORM                                                           -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <h2 class="h5 fw-semibold mb-1">
                Teacher Information
            </h2>

            <p class="text-muted small mb-0">
                Complete the teacher's profile information below.
            </p>

        </div>


        <div class="card-body p-4">

            <form
                method="POST"
                action="/SchoolERP/public/teachers"
            >

                <?= csrf_field() ?>


                <!-- ================================================= -->
                <!-- EMPLOYMENT IDENTITY                                 -->
                <!-- ================================================= -->

                <div class="border-bottom pb-2 mb-4">

                    <h3 class="h6 fw-semibold mb-0">
                        Employment Information
                    </h3>

                </div>


                <div class="row g-3">

                    <!-- Employee Number -->
                    <div class="col-md-6">

                        <label
                            for="employee_number"
                            class="form-label fw-semibold"
                        >

                            Employee Number

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            id="employee_number"
                            name="employee_number"
                            class="form-control<?= $class(
                                'employee_number'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $value(
                                    'employee_number'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="50"
                            placeholder="e.g. EMP-001"
                            autocomplete="off"
                            required
                        >


                        <?php if (
                            $error(
                                'employee_number'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error(
                                        'employee_number'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php else: ?>

                            <div class="form-text">
                                This number must be unique.
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Employment Status -->
                    <div class="col-md-6">

                        <label
                            for="employment_status"
                            class="form-label fw-semibold"
                        >
                            Employment Status
                            <span class="text-danger">*</span>
                        </label>


                        <?php
                        $employmentStatus =
                            strtolower(
                                $value(
                                    'employment_status',
                                    'active'
                                )
                            );
                        ?>


                        <select
                            id="employment_status"
                            name="employment_status"
                            class="form-select<?= $class(
                                'employment_status'
                            ) ?>"
                            required
                        >

                            <option
                                value="active"
                                <?= $employmentStatus === 'active'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                <?= $employmentStatus === 'inactive'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Inactive
                            </option>

                            <option
                                value="suspended"
                                <?= $employmentStatus === 'suspended'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Suspended
                            </option>

                            <option
                                value="terminated"
                                <?= $employmentStatus === 'terminated'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Terminated
                            </option>

                        </select>


                        <?php if (
                            $error(
                                'employment_status'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error(
                                        'employment_status'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Date Employed -->
                    <div class="col-md-6">

                        <label
                            for="date_employed"
                            class="form-label fw-semibold"
                        >
                            Date Employed
                        </label>


                        <input
                            type="date"
                            id="date_employed"
                            name="date_employed"
                            class="form-control<?= $class(
                                'date_employed'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $value(
                                    'date_employed'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >


                        <?php if (
                            $error(
                                'date_employed'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error(
                                        'date_employed'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PERSONAL INFORMATION                                -->
                <!-- ================================================= -->

                <div
                    class="border-bottom pb-2 mb-4 mt-5"
                >

                    <h3 class="h6 fw-semibold mb-0">
                        Personal Information
                    </h3>

                </div>


                <div class="row g-3">

                    <!-- First Name -->
                    <div class="col-md-6">

                        <label
                            for="first_name"
                            class="form-label fw-semibold"
                        >
                            First Name
                            <span class="text-danger">*</span>
                        </label>


                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            class="form-control<?= $class(
                                'first_name'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $value(
                                    'first_name'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="100"
                            autocomplete="given-name"
                            required
                        >


                        <?php if (
                            $error(
                                'first_name'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error(
                                        'first_name'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Last Name -->
                    <div class="col-md-6">

                        <label
                            for="last_name"
                            class="form-label fw-semibold"
                        >
                            Last Name
                            <span class="text-danger">*</span>
                        </label>


                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            class="form-control<?= $class(
                                'last_name'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $value(
                                    'last_name'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="100"
                            autocomplete="family-name"
                            required
                        >


                        <?php if (
                            $error(
                                'last_name'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error(
                                        'last_name'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Date of Birth -->
                    <div class="col-md-6">

                        <label
                            for="date_of_birth"
                            class="form-label fw-semibold"
                        >
                            Date of Birth
                        </label>


                        <input
                            type="date"
                            id="date_of_birth"
                            name="date_of_birth"
                            class="form-control<?= $class(
                                'date_of_birth'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $value(
                                    'date_of_birth'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >


                        <?php if (
                            $error(
                                'date_of_birth'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error(
                                        'date_of_birth'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Gender -->
                    <div class="col-md-6">

                        <label
                            for="gender"
                            class="form-label fw-semibold"
                        >
                            Gender
                        </label>


                        <?php
                        $gender =
                            strtolower(
                                $value('gender')
                            );
                        ?>


                        <select
                            id="gender"
                            name="gender"
                            class="form-select<?= $class(
                                'gender'
                            ) ?>"
                        >

                            <option value="">
                                -- Select Gender --
                            </option>

                            <option
                                value="male"
                                <?= $gender === 'male'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Male
                            </option>

                            <option
                                value="female"
                                <?= $gender === 'female'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Female
                            </option>

                            <option
                                value="other"
                                <?= $gender === 'other'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Other
                            </option>

                        </select>


                        <?php if (
                            $error(
                                'gender'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error(
                                        'gender'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- CONTACT INFORMATION                                 -->
                <!-- ================================================= -->

                <div
                    class="border-bottom pb-2 mb-4 mt-5"
                >

                    <h3 class="h6 fw-semibold mb-0">
                        Contact Information
                    </h3>

                </div>


                <div class="row g-3">

                    <!-- Phone -->
                    <div class="col-md-6">

                        <label
                            for="phone"
                            class="form-label fw-semibold"
                        >
                            Phone Number
                        </label>


                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control<?= $class(
                                'phone'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $value('phone'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="30"
                            placeholder="e.g. 08012345678"
                            autocomplete="tel"
                        >


                        <?php if (
                            $error('phone') !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error('phone'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Email -->
                    <div class="col-md-6">

                        <label
                            for="email"
                            class="form-label fw-semibold"
                        >
                            Email Address
                        </label>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control<?= $class(
                                'email'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $value('email'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="150"
                            placeholder="teacher@example.com"
                            autocomplete="email"
                        >


                        <?php if (
                            $error('email') !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error('email'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Address -->
                    <div class="col-12">

                        <label
                            for="address"
                            class="form-label fw-semibold"
                        >
                            Address
                        </label>


                        <textarea
                            id="address"
                            name="address"
                            class="form-control<?= $class(
                                'address'
                            ) ?>"
                            rows="4"
                            maxlength="500"
                            placeholder="Enter residential address..."
                        ><?= htmlspecialchars(
                            $value('address'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>


                        <?php if (
                            $error('address') !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $error('address'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- ACTIONS                                             -->
                <!-- ================================================= -->

                <div
                    class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-5 pt-4 border-top"
                >

                    <a
                        href="/SchoolERP/public/teachers"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-person-plus me-1"></i>
                        Create Teacher
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>