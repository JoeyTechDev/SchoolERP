<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Teacher $teacher
 */

if (!isset($teacher)) {
    return;
}

$errors = $_SESSION['_errors'] ?? [];

if (!is_array($errors)) {
    $errors = [];
}

$oldInput = $_SESSION['_old_input'] ?? [];

if (!is_array($oldInput)) {
    $oldInput = [];
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

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

$fieldClass = static function (
    string $field
) use (
    $error
): string {
    return $error($field) !== ''
        ? ' is-invalid'
        : '';
};

/*
|--------------------------------------------------------------------------
| Existing values
|--------------------------------------------------------------------------
*/

$firstName = (string) (
    $teacher->first_name ?? ''
);

$lastName = (string) (
    $teacher->last_name ?? ''
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
) {
    $dateOfBirth =
        (string) $teacher->date_of_birth;
}

$gender = strtolower(
    (string) (
        $teacher->gender ?? ''
    )
);

$phone = (string) (
    $teacher->phone ?? ''
);

$email = (string) (
    $teacher->email ?? ''
);

$address = (string) (
    $teacher->address ?? ''
);

/*
|--------------------------------------------------------------------------
| Override with failed form submission
|--------------------------------------------------------------------------
*/

$firstName = $value(
    'first_name',
    $firstName
);

$lastName = $value(
    'last_name',
    $lastName
);

$dateOfBirth = $value(
    'date_of_birth',
    $dateOfBirth
);

$gender = strtolower(
    $value(
        'gender',
        $gender
    )
);

$phone = $value(
    'phone',
    $phone
);

$email = $value(
    'email',
    $email
);

$address = $value(
    'address',
    $address
);

/*
|--------------------------------------------------------------------------
| Administrator-controlled fields
|--------------------------------------------------------------------------
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
) {
    $dateEmployed =
        (string) $teacher->date_employed;
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
    <!-- PROFILE FORM                                                   -->
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
                            <span class="text-danger">*</span>
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
                            class="form-control<?= $fieldClass(
                                'first_name'
                            ) ?>"
                            maxlength="100"
                            autocomplete="given-name"
                            required
                        >


                        <?php if (
                            $error('first_name') !== ''
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
                            value="<?= htmlspecialchars(
                                $lastName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control<?= $fieldClass(
                                'last_name'
                            ) ?>"
                            maxlength="100"
                            autocomplete="family-name"
                            required
                        >


                        <?php if (
                            $error('last_name') !== ''
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
                            value="<?= htmlspecialchars(
                                $dateOfBirth,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control<?= $fieldClass(
                                'date_of_birth'
                            ) ?>"
                        >


                        <?php if (
                            $error('date_of_birth') !== ''
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


                        <select
                            id="gender"
                            name="gender"
                            class="form-select<?= $fieldClass(
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
                            $error('gender') !== ''
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
                            class="form-control<?= $fieldClass(
                                'email'
                            ) ?>"
                            maxlength="150"
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
                <!-- ADMINISTRATOR CONTROLLED INFORMATION               -->
                <!-- ================================================= -->

                <div
                    class="border-top mt-5 pt-4"
                >

                    <h3 class="h6 fw-semibold mb-3">
                        Employment Information
                    </h3>


                    <div class="row g-3">

                        <div class="col-md-4">

                            <div class="border rounded p-3">

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


                        <div class="col-md-4">

                            <div class="border rounded p-3">

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


                        <div class="col-md-4">

                            <div class="border rounded p-3">

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
                <!-- BUTTONS                                             -->
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

        </div>

    </div>

</div>