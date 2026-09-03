<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var \SchoolERP\Models\Teacher $teacher
 */

/*
|--------------------------------------------------------------------------
| Guard
|--------------------------------------------------------------------------
*/

if (!isset($teacher) || !is_object($teacher)) {
    return;
}

/*
|--------------------------------------------------------------------------
| Session / Flash Data
|--------------------------------------------------------------------------
*/

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$successMessage = $session->flash(
    'success'
);

$errorMessage = $session->flash(
    'error'
);

$errors = $session->flash(
    '_errors'
);

$oldInput = $session->flash(
    '_old_input'
);

if (!is_array($errors)) {
    $errors = [];
}

if (!is_array($oldInput)) {
    $oldInput = [];
}

/*
|--------------------------------------------------------------------------
| Error Helper
|--------------------------------------------------------------------------
*/

$getError = static function (
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

/*
|--------------------------------------------------------------------------
| Field Helpers
|--------------------------------------------------------------------------
*/

$getValue = static function (
    string $field,
    mixed $default = ''
) use (
    $oldInput
): string {
    if (
        array_key_exists(
            $field,
            $oldInput
        )
    ) {
        $value = $oldInput[$field];
    } else {
        $value = $default;
    }

    return $value === null
        ? ''
        : (string) $value;
};

$getFieldClass = static function (
    string $field
) use (
    $getError
): string {
    return $getError($field) !== ''
        ? ' is-invalid'
        : '';
};

/*
|--------------------------------------------------------------------------
| Teacher Personal Information
|--------------------------------------------------------------------------
*/

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

$dateOfBirth = '';

if (
    $teacher->date_of_birth
    instanceof \DateTimeInterface
) {
    $dateOfBirth =
        $teacher->date_of_birth->format(
            'Y-m-d'
        );
} elseif (
    $teacher->date_of_birth !== null
    && $teacher->date_of_birth !== ''
) {
    $dateOfBirth =
        (string) $teacher->date_of_birth;
}

$gender = strtolower(
    trim(
        (string) (
            $teacher->gender ?? ''
        )
    )
);

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
| Restore old input after validation failure
|--------------------------------------------------------------------------
*/

$firstName = $getValue(
    'first_name',
    $firstName
);

$lastName = $getValue(
    'last_name',
    $lastName
);

$dateOfBirth = $getValue(
    'date_of_birth',
    $dateOfBirth
);

$gender = strtolower(
    $getValue(
        'gender',
        $gender
    )
);

$phone = $getValue(
    'phone',
    $phone
);

$email = $getValue(
    'email',
    $email
);

$address = $getValue(
    'address',
    $address
);

/*
|--------------------------------------------------------------------------
| Employment Information
|--------------------------------------------------------------------------
|
| These fields are displayed only. Teachers cannot edit them.
|
*/

$employeeNumber = trim(
    (string) (
        $teacher->employee_number ?? ''
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

$statusLabel = match (
    $employmentStatus
) {
    'active' => 'Active',
    'inactive' => 'Inactive',
    'suspended' => 'Suspended',
    'terminated' => 'Terminated',
    default => ucfirst(
        $employmentStatus !== ''
            ? $employmentStatus
            : 'Unknown'
    ),
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
    && $teacher->date_employed !== ''
) {
    $dateEmployed =
        (string) $teacher->date_employed;
}

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- FLASH MESSAGES                                                 -->
    <!-- ============================================================= -->

    <?php if (
        is_string($successMessage)
        && trim($successMessage) !== ''
    ): ?>

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert"
        >

            <?= htmlspecialchars(
                $successMessage,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>

        </div>

    <?php endif; ?>


    <?php if (
        is_string($errorMessage)
        && trim($errorMessage) !== ''
    ): ?>

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
        >

            <?= htmlspecialchars(
                $errorMessage,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>

        </div>

    <?php endif; ?>


    <?php
    /*
     * Display general validation errors that are not tied
     * to a specific field.
     */
    $generalErrors = [];

    foreach (
        $errors as $field => $message
    ) {
        if (
            !is_string($field)
            || !in_array(
                $field,
                [
                    'first_name',
                    'last_name',
                    'date_of_birth',
                    'gender',
                    'email',
                    'current_password',
                    'new_password',
                    'new_password_confirmation',
                ],
                true
            )
        ) {
            if (is_array($message)) {
                foreach ($message as $item) {
                    if (is_string($item)) {
                        $generalErrors[] = $item;
                    }
                }
            } elseif (
                is_string($message)
            ) {
                $generalErrors[] = $message;
            }
        }
    }
    ?>


    <?php if (
        $generalErrors !== []
    ): ?>

        <div
            class="alert alert-danger"
            role="alert"
        >

            <ul class="mb-0">

                <?php foreach (
                    $generalErrors
                    as $generalError
                ): ?>

                    <li>

                        <?= htmlspecialchars(
                            $generalError,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>


    <!-- ============================================================= -->
    <!-- PAGE HEADER                                                    -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                My Profile
            </h1>

            <p class="text-muted mb-0">
                Manage your personal and contact information.
            </p>

        </div>


        <a
            href="/SchoolERP/public/teacher/dashboard"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- ============================================================= -->
    <!-- PERSONAL INFORMATION                                          -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-1">
                Personal Information
            </h2>

            <p class="text-muted small mb-0">
                Update the information you maintain yourself.
            </p>

        </div>


        <div class="card-body p-4">

            <!-- ===================================================== -->
            <!-- PROFILE FORM                                            -->
            <!-- ===================================================== -->

            <form
                method="POST"
                action="/SchoolERP/public/teacher/profile"
            >

                <?= csrf_field() ?>


                <div class="row g-3">

                    <!-- First Name -->

                    <div class="col-md-6">

                        <label
                            for="first_name"
                            class="form-label fw-semibold"
                        >

                            First Name

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?= htmlspecialchars(
                                $firstName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control<?= $getFieldClass(
                                'first_name'
                            ) ?>"
                            maxlength="100"
                            autocomplete="given-name"
                            required
                        >


                        <?php if (
                            $getError(
                                'first_name'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
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

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= htmlspecialchars(
                                $lastName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control<?= $getFieldClass(
                                'last_name'
                            ) ?>"
                            maxlength="100"
                            autocomplete="family-name"
                            required
                        >


                        <?php if (
                            $getError(
                                'last_name'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
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
                            value="<?= htmlspecialchars(
                                $dateOfBirth,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control<?= $getFieldClass(
                                'date_of_birth'
                            ) ?>"
                        >


                        <?php if (
                            $getError(
                                'date_of_birth'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
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


                        <select
                            id="gender"
                            name="gender"
                            class="form-select<?= $getFieldClass(
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
                            $getError(
                                'gender'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
                                        'gender'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


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
                            value="<?= htmlspecialchars(
                                $phone,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control"
                            maxlength="30"
                            autocomplete="tel"
                        >

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
                            value="<?= htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control<?= $getFieldClass(
                                'email'
                            ) ?>"
                            maxlength="150"
                            autocomplete="email"
                        >


                        <?php if (
                            $getError(
                                'email'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
                                        'email'
                                    ),
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
                            class="form-control"
                            rows="4"
                            maxlength="500"
                        ><?= htmlspecialchars(
                            $address,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- EMPLOYMENT INFORMATION                              -->
                <!-- ================================================= -->

                <div
                    class="border-top mt-5 pt-4"
                >

                    <h3 class="h6 fw-semibold mb-3">
                        Employment Information
                    </h3>


                    <div class="row g-3">

                        <!-- Employee Number -->

                        <div class="col-md-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Employee Number
                                </div>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars(
                                        $employeeNumber !== ''
                                            ? $employeeNumber
                                            : 'Not assigned',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                            </div>

                        </div>


                        <!-- Employment Status -->

                        <div class="col-md-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Employment Status
                                </div>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars(
                                        $statusLabel,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                            </div>

                        </div>


                        <!-- Date Employed -->

                        <div class="col-md-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Date Employed
                                </div>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars(
                                        $dateEmployed !== ''
                                            ? $dateEmployed
                                            : 'Not provided',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="form-text mt-3">
                        Employment information can only be changed by an administrator.
                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PROFILE ACTIONS                                     -->
                <!-- ================================================= -->

                <div
                    class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-5 pt-4 border-top"
                >

                    <a
                        href="/SchoolERP/public/teacher/dashboard"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Profile
                    </button>

                </div>

            </form>

            <!-- ===================================================== -->
            <!-- IMPORTANT: PROFILE FORM ENDS HERE                     -->
            <!-- ===================================================== -->

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PASSWORD SECURITY                                              -->
    <!-- ============================================================= -->

    <div
        class="card border-0 shadow-sm mt-4"
        id="password-security"
    >

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-1">
                Password & Account Security
            </h2>

            <p class="text-muted small mb-0">
                Change your portal password to keep your account secure.
            </p>

        </div>


        <div class="card-body p-4">

            <!-- ===================================================== -->
            <!-- PASSWORD FORM                                           -->
            <!-- ===================================================== -->

            <form
                method="POST"
                action="/SchoolERP/public/teacher/profile/password"
                autocomplete="off"
            >

                <?= csrf_field() ?>


                <div class="row g-3">

                    <!-- Current Password -->

                    <div class="col-12">

                        <label
                            for="current_password"
                            class="form-label fw-semibold"
                        >

                            Current Password

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control<?= $getError(
                                'current_password'
                            ) !== ''
                                ? ' is-invalid'
                                : '' ?>"
                            autocomplete="current-password"
                            required
                        >


                        <?php if (
                            $getError(
                                'current_password'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
                                        'current_password'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- New Password -->

                    <div class="col-md-6">

                        <label
                            for="new_password"
                            class="form-label fw-semibold"
                        >

                            New Password

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            class="form-control<?= $getError(
                                'new_password'
                            ) !== ''
                                ? ' is-invalid'
                                : '' ?>"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >


                        <div class="form-text">
                            Use at least 8 characters.
                        </div>


                        <?php if (
                            $getError(
                                'new_password'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
                                        'new_password'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Confirm New Password -->

                    <div class="col-md-6">

                        <label
                            for="new_password_confirmation"
                            class="form-label fw-semibold"
                        >

                            Confirm New Password

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <input
                            type="password"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            class="form-control<?= $getError(
                                'new_password_confirmation'
                            ) !== ''
                                ? ' is-invalid'
                                : '' ?>"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >


                        <?php if (
                            $getError(
                                'new_password_confirmation'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $getError(
                                        'new_password_confirmation'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PASSWORD ACTIONS                                    -->
                <!-- ================================================= -->

                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-4 pt-4 border-top"
                >

                    <div class="small text-muted">

                        Your current session will remain active
                        after changing your password.

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Change Password
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>