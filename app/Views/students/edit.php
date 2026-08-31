<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 * @var array<int,array<string,mixed>> $classrooms
 */

if (!isset($student)) {
    return;
}

$classrooms = $classrooms ?? [];

/*
|--------------------------------------------------------------------------
| Validation / Old Input
|--------------------------------------------------------------------------
*/

$oldInput = [];

$errors = [];

if (isset($_SESSION['_old_input'])) {

    $oldInput = $_SESSION['_old_input'];

    if (!is_array($oldInput)) {
        $oldInput = [];
    }
}

if (isset($_SESSION['_errors'])) {

    $errors = $_SESSION['_errors'];

    if (!is_array($errors)) {
        $errors = [];
    }
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

$fieldError = static function (
    string $field
) use (
    $errors
): string {

    $error = $errors[$field] ?? '';

    if (is_array($error)) {
        $error = $error[0] ?? '';
    }

    return is_string($error)
        ? $error
        : '';
};

$fieldClass = static function (
    string $field
) use (
    $fieldError
): string {

    return $fieldError($field) !== ''
        ? ' is-invalid'
        : '';
};

/*
|--------------------------------------------------------------------------
| Existing Student Values
|--------------------------------------------------------------------------
*/

$admissionNumber = (string) (
    $student->admission_number ?? ''
);

$firstName = (string) (
    $student->first_name ?? ''
);

$lastName = (string) (
    $student->last_name ?? ''
);

$gender = strtolower(
    (string) (
        $student->gender ?? ''
    )
);

$classroomId = (int) (
    $student->classroom_id ?? 0
);

/*
|--------------------------------------------------------------------------
| Date of Birth
|--------------------------------------------------------------------------
*/

$dateOfBirth = '';

if (
    $student->date_of_birth
    instanceof \DateTimeInterface
) {

    $dateOfBirth =
        $student->date_of_birth->format(
            'Y-m-d'
        );

} elseif (
    $student->date_of_birth !== null
) {

    $dateOfBirth =
        (string) $student->date_of_birth;
}

/*
|--------------------------------------------------------------------------
| Use old values after validation failure
|--------------------------------------------------------------------------
*/

if (
    array_key_exists(
        'admission_number',
        $oldInput
    )
) {
    $admissionNumber =
        (string) $oldInput[
            'admission_number'
        ];
}

if (
    array_key_exists(
        'first_name',
        $oldInput
    )
) {
    $firstName =
        (string) $oldInput[
            'first_name'
        ];
}

if (
    array_key_exists(
        'last_name',
        $oldInput
    )
) {
    $lastName =
        (string) $oldInput[
            'last_name'
        ];
}

if (
    array_key_exists(
        'date_of_birth',
        $oldInput
    )
) {
    $dateOfBirth =
        (string) $oldInput[
            'date_of_birth'
        ];
}

if (
    array_key_exists(
        'gender',
        $oldInput
    )
) {
    $gender = strtolower(
        (string) $oldInput['gender']
    );
}

if (
    array_key_exists(
        'classroom_id',
        $oldInput
    )
) {
    $classroomId = (int) (
        $oldInput['classroom_id']
        ?? 0
    );
}

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
                Edit Student
            </h1>

            <p class="text-muted mb-0">

                Update the profile and academic placement
                information for this student.

            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="/SchoolERP/public/students/<?= (int) $student->id ?>"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Student
            </a>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- FORM CARD                                                      -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <h2 class="h5 fw-semibold mb-1">
                Student Information
            </h2>

            <p class="text-muted small mb-0">

                Student ID:
                <strong>
                    #<?= (int) $student->id ?>
                </strong>

            </p>

        </div>


        <div class="card-body p-4">

            <form
                method="POST"
                action="/SchoolERP/public/students/<?= (int) $student->id ?>/update"
            >

                <?= csrf_field() ?>


                <!-- ================================================= -->
                <!-- IDENTIFICATION                                     -->
                <!-- ================================================= -->

                <div class="border-bottom pb-2 mb-4">

                    <h3 class="h6 fw-semibold mb-0">
                        Identification
                    </h3>

                </div>


                <div class="row g-3">


                    <!-- Admission Number -->
                    <div class="col-md-6">

                        <label
                            for="admission_number"
                            class="form-label fw-semibold"
                        >

                            Admission Number

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            id="admission_number"
                            name="admission_number"
                            class="form-control<?= $fieldClass(
                                'admission_number'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $admissionNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="50"
                            autocomplete="off"
                            required
                        >


                        <?php if (
                            $fieldError(
                                'admission_number'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $fieldError(
                                        'admission_number'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php else: ?>

                            <div class="form-text">

                                Admission number must be unique.

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Student ID -->
                    <div class="col-md-6">

                        <label
                            class="form-label fw-semibold"
                        >
                            Student ID
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="#<?= (int) $student->id ?>"
                            disabled
                        >

                        <div class="form-text">
                            This system ID cannot be changed.
                        </div>

                    </div>


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
                            $fieldError(
                                'first_name'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $fieldError(
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
                            $fieldError(
                                'last_name'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $fieldError(
                                        'last_name'
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
                            $fieldError(
                                'date_of_birth'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $fieldError(
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
                            $fieldError(
                                'gender'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $fieldError(
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
                <!-- ACADEMIC PLACEMENT                                  -->
                <!-- ================================================= -->

                <div
                    class="border-bottom pb-2 mb-4 mt-5"
                >

                    <h3 class="h6 fw-semibold mb-0">
                        Academic Placement
                    </h3>

                </div>


                <div class="row g-3">


                    <!-- Classroom -->
                    <div class="col-md-6">

                        <label
                            for="classroom_id"
                            class="form-label fw-semibold"
                        >
                            Classroom
                        </label>


                        <select
                            id="classroom_id"
                            name="classroom_id"
                            class="form-select<?= $fieldClass(
                                'classroom_id'
                            ) ?>"
                        >

                            <option value="">
                                -- Not Assigned --
                            </option>


                            <?php foreach (
                                $classrooms
                                as $classroom
                            ): ?>

                                <?php

                                $itemId = (int) (
                                    $classroom['id']
                                    ?? 0
                                );

                                $itemName = (string) (
                                    $classroom['name']
                                    ?? ''
                                );

                                if ($itemId <= 0) {
                                    continue;
                                }

                                ?>

                                <option
                                    value="<?= $itemId ?>"
                                    <?= $classroomId === $itemId
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $itemName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <?php if (
                            $fieldError(
                                'classroom_id'
                            ) !== ''
                        ): ?>

                            <div class="invalid-feedback">

                                <?= htmlspecialchars(
                                    $fieldError(
                                        'classroom_id'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        <?php else: ?>

                            <div class="form-text">

                                Select the student's current classroom.

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- ACTIONS                                             -->
                <!-- ================================================= -->

                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-5 pt-4 border-top"
                >

                    <a
                        href="/SchoolERP/public/students/<?= (int) $student->id ?>"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        <i class="bi bi-save me-1"></i>

                        Save Student Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
