<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Teacher $teacher
 */

if (!isset($teacher)) {
    return;
}

/*
|--------------------------------------------------------------------------
| Session validation data
|--------------------------------------------------------------------------
*/

$oldInput = $_SESSION['_old_input'] ?? [];
$errors = $_SESSION['_errors'] ?? [];

$oldInput = is_array($oldInput)
    ? $oldInput
    : [];

$errors = is_array($errors)
    ? $errors
    : [];

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
    if (array_key_exists($field, $oldInput)) {
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
| Existing teacher values
|--------------------------------------------------------------------------
*/

$employeeNumber = (string) (
    $teacher->employee_number ?? ''
);

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

$employmentStatus = strtolower(
    (string) (
        $teacher->employment_status
        ?? 'active'
    )
);

$dateEmployed = '';

if (
    $teacher->date_employed
    instanceof \DateTimeInterface
) {
    $dateEmployed =
        $teacher->date_employed->format(
            'Y-m-d'
        );
} elseif (
    $teacher->date_employed !== null
) {
    $dateEmployed =
        (string) $teacher->date_employed;
}

/*
|--------------------------------------------------------------------------
| Old input overrides database values
|--------------------------------------------------------------------------
*/

$employeeNumber =
    $value(
        'employee_number',
        $employeeNumber
    );

$firstName =
    $value(
        'first_name',
        $firstName
    );

$lastName =
    $value(
        'last_name',
        $lastName
    );

$dateOfBirth =
    $value(
        'date_of_birth',
        $dateOfBirth
    );

$gender =
    strtolower(
        $value(
            'gender',
            $gender
        )
    );

$phone =
    $value(
        'phone',
        $phone
    );

$email =
    $value(
        'email',
        $email
    );

$address =
    $value(
        'address',
        $address
    );

$employmentStatus =
    strtolower(
        $value(
            'employment_status',
            $employmentStatus
        )
    );

$dateEmployed =
    $value(
        'date_employed',
        $dateEmployed
    );

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- PAGE HEADER                                                    -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                Edit Teacher
            </h1>

            <p class="text-muted mb-0">
                Update this teacher's profile and employment information.
            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="/SchoolERP/public/teachers/<?= (int) $teacher->id ?>"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Teacher
            </a>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- FORM CARD                                                      -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <h2 class="h5 fw-semibold mb-1">
                Teacher Information
            </h2>

            <p class="text-muted small mb-0">

                Teacher ID:

                <strong>
                    #<?= (int) $teacher->id ?>
                </strong>

            </p>

        </div>


        <div class="card-body p-4">

            <form
                method="POST"
                action="/SchoolERP/public/teachers/<?= (int) $teacher->id ?>/update"
            >

                <?= csrf_field() ?>


                <!-- ================================================= -->
                <!-- EMPLOYMENT INFORMATION                              -->
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
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="employee_number"
                            name="employee_number"
                            class="form-control<?= $fieldClass(
                                'employee_number'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $employeeNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="50"
                            autocomplete="off"
                            required
                        >

                        <?php if (
                            $error('employee_number')
                            !== ''
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

                        <select
                            id="employment_status"
                            name="employment_status"
                            class="form-select<?= $fieldClass(
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
                            $error('employment_status')
                            !== ''
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
                            class="form-control<?= $fieldClass(
                                'date_employed'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $dateEmployed,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <?php if (
                            $error('date_employed')
                            !== ''
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
                            class="form-control<?= $fieldClass(
                                'first_name'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $firstName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="100"
                            autocomplete="given-name"
                            required
                        >

                        <?php if (
                            $error('first_name')
                            !== ''
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
                            class="form-control<?= $fieldClass(
                                'last_name'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $lastName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="100"
                            autocomplete="family-name"
                            required
                        >

                        <?php if (
                            $error('last_name')
                            !== ''
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
                            class="form-control<?= $fieldClass(
                                'date_of_birth'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $dateOfBirth,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                        <?php if (
                            $error('date_of_birth')
                            !== ''
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
                            $error('gender')
                            !== ''
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
                            class="form-control<?= $fieldClass(
                                'phone'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $phone,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="30"
                            autocomplete="tel"
                        >

                        <?php if (
                            $error('phone')
                            !== ''
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
                            class="form-control<?= $fieldClass(
                                'email'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="150"
                            autocomplete="email"
                        >

                        <?php if (
                            $error('email')
                            !== ''
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
                            class="form-control<?= $fieldClass(
                                'address'
                            ) ?>"
                            rows="4"
                            maxlength="500"
                        ><?= htmlspecialchars(
                            $address,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                        <?php if (
                            $error('address')
                            !== ''
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
                    class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-5 pt-4 border-top"
                >

                    <a
                        href="/SchoolERP/public/teachers/<?= (int) $teacher->id ?>"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-save me-1"></i>
                        Save Teacher Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
