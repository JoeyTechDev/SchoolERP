<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $classrooms
 */

$classrooms = $classrooms ?? [];

/*
|--------------------------------------------------------------------------
| Old input
|--------------------------------------------------------------------------
|
| Validation failures are redirected back here by StudentController
| with _old_input and _errors stored in the session.
|
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
| Helper
|--------------------------------------------------------------------------
*/

$fieldValue = static function (
    string $field
) use (
    $oldInput
): string {
    $value = $oldInput[$field] ?? '';

    if ($value === null) {
        return '';
    }

    return (string) $value;
};

$fieldError = static function (
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

$fieldClass = static function (
    string $field
) use (
    $fieldError
): string {
    return $fieldError($field) !== ''
        ? ' is-invalid'
        : '';
};

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
                Create Student
            </h1>

            <p class="text-muted mb-0">
                Register a new student in the school.
            </p>

        </div>


        <a
            href="/SchoolERP/public/students"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Students
        </a>

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
                Enter the student's basic identification and classroom
                information.
            </p>

        </div>


        <div class="card-body p-4">

            <form
                method="POST"
                action="/SchoolERP/public/students"
            >

                <?= csrf_field() ?>


                <!-- ================================================= -->
                <!-- IDENTITY                                           -->
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
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="admission_number"
                            name="admission_number"
                            class="form-control<?= $fieldClass(
                                'admission_number'
                            ) ?>"
                            value="<?= htmlspecialchars(
                                $fieldValue(
                                    'admission_number'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="50"
                            placeholder="e.g. STU-001"
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
                                This number must be unique for each student.
                            </div>

                        <?php endif; ?>

                    </div>


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
                                $fieldValue(
                                    'first_name'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="100"
                            placeholder="Enter first name"
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
                                $fieldValue(
                                    'last_name'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="100"
                            placeholder="Enter last name"
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
                                $fieldValue(
                                    'date_of_birth'
                                ),
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

                        <?php
                        $selectedGender =
                            strtolower(
                                $fieldValue('gender')
                            );
                        ?>

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
                                <?= $selectedGender === 'male'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Male
                            </option>

                            <option
                                value="female"
                                <?= $selectedGender === 'female'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Female
                            </option>

                            <option
                                value="other"
                                <?= $selectedGender === 'other'
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
                <!-- CLASSROOM                                           -->
                <!-- ================================================= -->

                <div
                    class="border-bottom pb-2 mb-4 mt-5"
                >

                    <h3 class="h6 fw-semibold mb-0">
                        Academic Placement
                    </h3>

                </div>


                <div class="row g-3">

                    <div class="col-md-6">

                        <label
                            for="classroom_id"
                            class="form-label fw-semibold"
                        >
                            Classroom
                        </label>

                        <?php
                        $selectedClassroomId = (int) (
                            $oldInput['classroom_id']
                            ?? 0
                        );
                        ?>

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
                                $classroomId = (int) (
                                    $classroom['id']
                                    ?? 0
                                );

                                $classroomName =
                                    (string) (
                                        $classroom['name']
                                        ?? ''
                                    );
                                ?>

                                <?php if (
                                    $classroomId <= 0
                                ): ?>

                                    <?php continue; ?>

                                <?php endif; ?>

                                <option
                                    value="<?= $classroomId ?>"
                                    <?= (
                                        $selectedClassroomId
                                        === $classroomId
                                    )
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $classroomName,
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
                                You can leave this blank and assign the
                                student to a classroom later.
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
                        href="/SchoolERP/public/students"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-person-plus me-1"></i>
                        Create Student
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>