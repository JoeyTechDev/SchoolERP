<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 * @var \SchoolERP\Models\User|null $user
 */

$studentId = (int) (
    $student->id ?? 0
);

$studentName = trim(
    (string) (
        ($student->first_name ?? '')
        . ' '
        . ($student->last_name ?? '')
    )
);

if ($studentName === '') {
    $studentName = 'Unnamed Student';
}

$admissionNumber = trim(
    (string) (
        $student->admission_number
        ?? ''
    )
);

$classroomName = 'Not assigned';

$classroom = null;

if (
    method_exists(
        $student,
        'getRelation'
    )
) {
    $classroom = $student->getRelation(
        'classroom'
    );
}

if (
    is_object($classroom)
    && isset($classroom->name)
) {
    $classroomName = trim(
        (string) $classroom->name
    );

    if ($classroomName === '') {
        $classroomName = 'Not assigned';
    }
}

$email = '';

$status = '';

$roleId = 0;

if ($user !== null) {
    $email = trim(
        (string) (
            $user->email ?? ''
        )
    );

    $status = strtolower(
        trim(
            (string) (
                $user->status ?? ''
            )
        )
    );

    $roleId = (int) (
        $user->role_id ?? 0
    );
}

/*
|--------------------------------------------------------------------------
| Flash validation state
|--------------------------------------------------------------------------
*/

$oldInput = $_SESSION['_old_input'] ?? [];
$errors = $_SESSION['_errors'] ?? [];

unset(
    $_SESSION['_old_input'],
    $_SESSION['_errors']
);

/*
|--------------------------------------------------------------------------
| General success/error flash messages
|--------------------------------------------------------------------------
*/

$successMessage = $_SESSION['success'] ?? null;
$errorMessage = $_SESSION['error'] ?? null;

unset(
    $_SESSION['success'],
    $_SESSION['error']
);
?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- HEADER                                                        -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row
               justify-content-between
               align-items-md-center
               gap-3 mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                Student Login Account
            </h1>

            <p class="text-muted mb-0">
                Create and manage this student's SchoolERP portal account.
            </p>

        </div>

        <div class="d-flex flex-wrap gap-2">

            <a
                href="/SchoolERP/public/students/<?= $studentId ?>"
                class="btn btn-outline-secondary"
            >
                Back to Student
            </a>

            <a
                href="/SchoolERP/public/students"
                class="btn btn-outline-secondary"
            >
                Students
            </a>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- FLASH MESSAGES                                                -->
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


    <!-- ============================================================= -->
    <!-- VALIDATION ERRORS                                             -->
    <!-- ============================================================= -->

    <?php if (
        is_array($errors)
        && $errors !== []
    ): ?>

        <div class="alert alert-danger">

            <div class="fw-semibold mb-2">
                Please correct the following:
            </div>

            <ul class="mb-0">

                <?php foreach (
                    $errors as $message
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


    <!-- ============================================================= -->
    <!-- STUDENT INFORMATION                                           -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-0">
                Student Information
            </h2>

        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Student
                    </div>

                    <div class="fw-semibold fs-5">
                        <?= htmlspecialchars(
                            $studentName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Admission Number
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $admissionNumber !== ''
                                ? $admissionNumber
                                : 'Not assigned',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="small text-muted mb-1">
                        Classroom
                    </div>

                    <div class="fw-semibold">

                        <?= htmlspecialchars(
                            $classroomName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- ACCOUNT STATUS                                                -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <div
                class="d-flex flex-column flex-md-row
                       justify-content-between
                       align-items-md-center
                       gap-2"
            >

                <h2 class="h5 fw-semibold mb-0">
                    Account Status
                </h2>


                <?php if (
                    $user === null
                ): ?>

                    <span class="badge text-bg-secondary">
                        No Account
                    </span>

                <?php elseif (
                    $status === 'active'
                ): ?>

                    <span class="badge text-bg-success">
                        Active
                    </span>

                <?php elseif (
                    $status === 'suspended'
                ): ?>

                    <span class="badge text-bg-danger">
                        Suspended
                    </span>

                <?php elseif (
                    $status === 'inactive'
                ): ?>

                    <span class="badge text-bg-warning">
                        Inactive
                    </span>

                <?php else: ?>

                    <span class="badge text-bg-secondary">
                        <?= htmlspecialchars(
                            ucfirst($status),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                <?php endif; ?>

            </div>

        </div>


        <div class="card-body p-4">

            <?php if (
                $user === null
            ): ?>

                <div class="alert alert-secondary mb-0">

                    This student does not currently have a
                    SchoolERP login account.

                </div>

            <?php else: ?>

                <div class="row g-4">

                    <div class="col-md-4">

                        <div class="small text-muted mb-1">
                            Login Email
                        </div>

                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="small text-muted mb-1">
                            Account Role
                        </div>

                        <div class="fw-semibold">

                            <?php if (
                                $roleId === 3
                            ): ?>

                                Student

                            <?php else: ?>

                                Role ID
                                <?= $roleId ?>

                            <?php endif; ?>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="small text-muted mb-1">
                            Account Status
                        </div>

                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                ucfirst(
                                    $status !== ''
                                        ? $status
                                        : 'Unknown'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                </div>


                <?php if (
                    $roleId !== 3
                ): ?>

                    <div class="alert alert-danger mt-4 mb-0">

                        <strong>Warning:</strong>
                        The linked user account is not assigned
                        the Student role.

                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- CREATE ACCOUNT                                                 -->
    <!-- ============================================================= -->

    <?php if (
        $user === null
    ): ?>

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-bottom">

                <h2 class="h5 fw-semibold mb-0">
                    Create Login Account
                </h2>

            </div>


            <div class="card-body p-4">

                <p class="text-muted mb-4">
                    Create the student's login credentials.
                    The account will automatically use the
                    Student role.
                </p>


                <form
                    method="POST"
                    action="/SchoolERP/public/students/<?= $studentId ?>/account"
                >

                    <?= csrf_field() ?>


                    <!-- Email -->
                    <div class="mb-4">

                        <label
                            for="account_email"
                            class="form-label fw-semibold"
                        >
                            Login Email
                        </label>

                        <input
                            type="email"
                            id="account_email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                (string) (
                                    $oldInput['email']
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="150"
                            autocomplete="username"
                            required
                        >

                        <div class="form-text">
                            The student will use this email
                            address to sign in.
                        </div>

                    </div>


                    <div class="row g-4">

                        <!-- Password -->
                        <div class="col-md-6">

                            <label
                                for="account_password"
                                class="form-label fw-semibold"
                            >
                                Temporary Password
                            </label>

                            <input
                                type="password"
                                id="account_password"
                                name="password"
                                class="form-control"
                                minlength="8"
                                maxlength="255"
                                autocomplete="new-password"
                                required
                            >

                        </div>


                        <!-- Confirmation -->
                        <div class="col-md-6">

                            <label
                                for="account_password_confirmation"
                                class="form-label fw-semibold"
                            >
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                id="account_password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                minlength="8"
                                maxlength="255"
                                autocomplete="new-password"
                                required
                            >

                        </div>

                    </div>


                    <div class="alert alert-warning mt-4">

                        <strong>Important:</strong>
                        Give the temporary password to the student
                        securely. The password is stored as a hash
                        and cannot be retrieved later.

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Create Login Account
                    </button>

                </form>

            </div>

        </div>


    <?php else: ?>


        <!-- ========================================================= -->
        <!-- PASSWORD MANAGEMENT                                       -->
        <!-- ========================================================= -->

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom">

                <h2 class="h5 fw-semibold mb-0">
                    Password Management
                </h2>

            </div>


            <div class="card-body p-4">

                <p class="text-muted mb-4">
                    Reset the student's portal password.
                </p>


                <form
                    method="POST"
                    action="/SchoolERP/public/students/<?= $studentId ?>/account/reset-password"
                >

                    <?= csrf_field() ?>


                    <div class="row g-4">

                        <div class="col-md-6">

                            <label
                                for="reset_password"
                                class="form-label fw-semibold"
                            >
                                New Password
                            </label>

                            <input
                                type="password"
                                id="reset_password"
                                name="password"
                                class="form-control"
                                minlength="8"
                                maxlength="255"
                                autocomplete="new-password"
                                required
                            >

                        </div>


                        <div class="col-md-6">

                            <label
                                for="reset_password_confirmation"
                                class="form-label fw-semibold"
                            >
                                Confirm New Password
                            </label>

                            <input
                                type="password"
                                id="reset_password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                minlength="8"
                                maxlength="255"
                                autocomplete="new-password"
                                required
                            >

                        </div>

                    </div>


                    <div class="mt-4">

                        <button
                            type="submit"
                            class="btn btn-warning"
                        >
                            Reset Student Password
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- ACCOUNT ACCESS                                             -->
        <!-- ========================================================= -->

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom">

                <h2 class="h5 fw-semibold mb-0">
                    Account Access
                </h2>

            </div>


            <div class="card-body p-4">

                <?php if (
                    $status === 'active'
                ): ?>

                    <div
                        class="d-flex flex-column
                               flex-md-row
                               justify-content-between
                               align-items-md-center
                               gap-3"
                    >

                        <div>

                            <h3 class="h6 fw-bold mb-1">
                                Account is active
                            </h3>

                            <p class="text-muted mb-0">
                                The student is currently allowed
                                to sign in to the portal.
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="/SchoolERP/public/students/<?= $studentId ?>/account/suspend"
                            onsubmit="return confirm('Suspend this student account?');"
                        >

                            <?= csrf_field() ?>

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                            >
                                Suspend Account
                            </button>

                        </form>

                    </div>


                <?php elseif (
                    $status === 'suspended'
                ): ?>

                    <div
                        class="d-flex flex-column
                               flex-md-row
                               justify-content-between
                               align-items-md-center
                               gap-3"
                    >

                        <div>

                            <h3 class="h6 fw-bold mb-1">
                                Account is suspended
                            </h3>

                            <p class="text-muted mb-0">
                                The student cannot start a new
                                login session while suspended.
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="/SchoolERP/public/students/<?= $studentId ?>/account/activate"
                            onsubmit="return confirm('Activate this student account?');"
                        >

                            <?= csrf_field() ?>

                            <button
                                type="submit"
                                class="btn btn-outline-success"
                            >
                                Activate Account
                            </button>

                        </form>

                    </div>


                <?php elseif (
                    $status === 'inactive'
                ): ?>

                    <div
                        class="d-flex flex-column
                               flex-md-row
                               justify-content-between
                               align-items-md-center
                               gap-3"
                    >

                        <div>

                            <h3 class="h6 fw-bold mb-1">
                                Account is inactive
                            </h3>

                            <p class="text-muted mb-0">
                                This account is not currently active.
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="/SchoolERP/public/students/<?= $studentId ?>/account/activate"
                            onsubmit="return confirm('Activate this student account?');"
                        >

                            <?= csrf_field() ?>

                            <button
                                type="submit"
                                class="btn btn-outline-success"
                            >
                                Activate Account
                            </button>

                        </form>

                    </div>


                <?php else: ?>

                    <div class="alert alert-secondary mb-0">

                        The current account status does not have
                        a dedicated management action yet.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    <?php endif; ?>


    <!-- ============================================================= -->
    <!-- SECURITY NOTE                                                 -->
    <!-- ============================================================= -->

    <div class="alert alert-info">

        <strong>Security:</strong>
        Student account credentials are managed separately from
        the student's academic record. Changing a password or
        account status does not change the student's admission,
        classroom, results, or attendance data.

    </div>

</div>
