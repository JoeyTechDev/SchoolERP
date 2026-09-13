<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 * @var \SchoolERP\Models\Classroom|null $classroom
 * @var string $email
 * @var array<string,string> $errors
 * @var array<string,mixed> $oldInput
 */

if (!isset($student)) {
    return;
}

$errors =
    is_array($errors ?? null)
        ? $errors
        : [];

$oldInput =
    is_array($oldInput ?? null)
        ? $oldInput
        : [];

$studentId = (int) (
    $student->id ?? 0
);

$admissionNumber = trim(
    (string) (
        $student->admission_number ?? ''
    )
);

$firstName = (string) (
    $oldInput['first_name']
    ?? $student->first_name
    ?? ''
);

$lastName = (string) (
    $oldInput['last_name']
    ?? $student->last_name
    ?? ''
);

$dateOfBirth = (string) (
    $oldInput['date_of_birth']
    ?? $student->date_of_birth
    ?? ''
);

$gender = strtolower(
    (string) (
        $oldInput['gender']
        ?? $student->gender
        ?? ''
    )
);

$classroomName = '';

if (
    is_object($classroom)
) {
    $classroomName = trim(
        (string) (
            $classroom->name ?? ''
        )
    );
}

if ($classroomName === '') {
    $classroomName =
        'Not assigned';
}

$success =
    $success
    ?? null;
?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div
        class="d-flex flex-column flex-md-row
               justify-content-between
               align-items-md-center
               gap-3
               mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                My Profile
            </h1>

            <p class="text-muted mb-0">
                Manage your personal information and account security.
            </p>

        </div>

        <a
            href="/SchoolERP/public/student/dashboard"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- Success Message -->
    <?php if (
        $success !== null
    ): ?>

        <div
            class="alert alert-success"
            role="alert"
        >
            <?= htmlspecialchars(
                (string) $success,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>


    <!-- General Errors -->
    <?php if (
        $errors !== []
    ): ?>

        <?php
        $passwordFields = [
            'current_password',
            'new_password',
            'new_password_confirmation',
        ];

        $profileErrors = array_diff_key(
            $errors,
            array_flip($passwordFields)
        );
        ?>

        <?php if (
            $profileErrors !== []
        ): ?>

            <div
                class="alert alert-danger"
                role="alert"
            >
                <strong>
                    Please correct the following:
                </strong>

                <ul class="mb-0 mt-2">

                    <?php foreach (
                        $profileErrors
                        as $message
                    ): ?>

                        <li>
                            <?= htmlspecialchars(
                                (string) $message,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>

    <?php endif; ?>


    <!-- Personal Information -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-0">
                Personal Information
            </h2>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/student/profile"
            >

                <?= csrf_field() ?>

                <div class="row g-4">

                    <!-- First Name -->
                    <div class="col-12 col-md-6">

                        <label
                            for="first_name"
                            class="form-label fw-semibold"
                        >
                            First Name
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
                            class="form-control"
                            maxlength="100"
                            required
                        >

                        <?php if (
                            isset(
                                $errors['first_name']
                            )
                        ): ?>

                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars(
                                    $errors['first_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Last Name -->
                    <div class="col-12 col-md-6">

                        <label
                            for="last_name"
                            class="form-label fw-semibold"
                        >
                            Last Name
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
                            class="form-control"
                            maxlength="100"
                            required
                        >

                        <?php if (
                            isset(
                                $errors['last_name']
                            )
                        ): ?>

                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars(
                                    $errors['last_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Date of Birth -->
                    <div class="col-12 col-md-6">

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
                            class="form-control"
                        >

                        <?php if (
                            isset(
                                $errors['date_of_birth']
                            )
                        ): ?>

                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars(
                                    $errors['date_of_birth'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Gender -->
                    <div class="col-12 col-md-6">

                        <label
                            for="gender"
                            class="form-label fw-semibold"
                        >
                            Gender
                        </label>

                        <select
                            id="gender"
                            name="gender"
                            class="form-select"
                        >

                            <option value="">
                                Select gender
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
                            isset(
                                $errors['gender']
                            )
                        ): ?>

                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars(
                                    $errors['gender'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- Admission Number -->
                    <div class="col-12 col-md-6">

                        <label
                            class="form-label fw-semibold"
                        >
                            Admission Number
                        </label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $admissionNumber !== ''
                                    ? $admissionNumber
                                    : 'Not provided',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control"
                            readonly
                        >

                        <div class="form-text">
                            Admission number is controlled by the school.
                        </div>

                    </div>


                    <!-- Classroom -->
                    <div class="col-12 col-md-6">

                        <label
                            class="form-label fw-semibold"
                        >
                            Classroom
                        </label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $classroomName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control"
                            readonly
                        >

                        <div class="form-text">
                            Classroom assignment is controlled by the school.
                        </div>

                    </div>


                    <!-- Email -->
                    <div class="col-12">

                        <label
                            class="form-label fw-semibold"
                        >
                            Login Email
                        </label>

                        <input
                            type="email"
                            value="<?= htmlspecialchars(
                                (string) $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control"
                            readonly
                        >

                        <div class="form-text">
                            Login email is managed as part of your account.
                        </div>

                    </div>

                </div>


                <div class="mt-4">

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


    <!-- Password Security -->
    <div
        class="card border-0 shadow-sm"
        id="password-security"
    >

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-0">
                Password &amp; Security
            </h2>

        </div>

        <div class="card-body">

            <?php
            $passwordErrors = [];

            foreach (
                [
                    'current_password',
                    'new_password',
                    'new_password_confirmation',
                ]
                as $field
            ) {
                if (
                    isset($errors[$field])
                ) {
                    $passwordErrors[$field] =
                        $errors[$field];
                }
            }
            ?>

            <?php if (
                $passwordErrors !== []
            ): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >

                    <?php foreach (
                        $passwordErrors
                        as $message
                    ): ?>

                        <div>
                            <?= htmlspecialchars(
                                $message,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                action="/SchoolERP/public/student/profile/password"
            >

                <?= csrf_field() ?>

                <div class="row g-4">

                    <div class="col-12">

                        <label
                            for="current_password"
                            class="form-label fw-semibold"
                        >
                            Current Password
                        </label>

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control"
                            autocomplete="current-password"
                            required
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label
                            for="new_password"
                            class="form-label fw-semibold"
                        >
                            New Password
                        </label>

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            class="form-control"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label
                            for="new_password_confirmation"
                            class="form-label fw-semibold"
                        >
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            class="form-control"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                </div>


                <div class="mt-4">

                    <button
                        type="submit"
                        class="btn btn-outline-primary"
                    >
                        Change Password
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>