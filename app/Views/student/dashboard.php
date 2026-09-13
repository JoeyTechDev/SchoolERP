<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 * @var \SchoolERP\Models\Classroom|null $classroom
 */

if (!isset($student)) {
    return;
}

$studentId = (int) (
    $student->id ?? 0
);

$admissionNumber = trim(
    (string) (
        $student->admission_number ?? ''
    )
);

$firstName = trim(
    (string) (
        $student->first_name ?? ''
    )
);

$lastName = trim(
    (string) (
        $student->last_name ?? ''
    )
);

$studentName = trim(
    $firstName . ' ' . $lastName
);

if ($studentName === '') {
    $studentName = 'Student';
}

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
    $classroomName = 'Not assigned';
}

$email = trim(
    (string) (
        $_SESSION['email'] ?? ''
    )
);
?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- HEADER                                                         -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row
               justify-content-between
               align-items-md-center
               gap-3
               mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                Student Dashboard
            </h1>

            <p class="text-muted mb-0">
                Welcome back,
                <?= htmlspecialchars(
                    $studentName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>.
            </p>

        </div>


        <!-- Logout -->
        <form
            method="POST"
            action="/SchoolERP/public/auth/logout"
            class="d-inline"
        >

            <?= csrf_field() ?>

            <button
                type="submit"
                class="btn btn-outline-danger"
            >
                Logout
            </button>

        </form>

    </div>


    <!-- ============================================================= -->
    <!-- STUDENT SUMMARY                                                -->
    <!-- ============================================================= -->

    <div class="row g-4 mb-4">


        <!-- Student -->
        <div class="col-12 col-md-6 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Student
                    </div>

                    <h2 class="h5 fw-bold mb-1">

                        <?= htmlspecialchars(
                            $studentName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </h2>

                    <div class="text-muted small">

                        Student ID:
                        <?= $studentId ?>

                    </div>

                </div>

            </div>

        </div>


        <!-- Admission Number -->
        <div class="col-12 col-md-6 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Admission Number
                    </div>

                    <h2 class="h5 fw-bold mb-1">

                        <?php if (
                            $admissionNumber !== ''
                        ): ?>

                            <?= htmlspecialchars(
                                $admissionNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        <?php else: ?>

                            Not provided

                        <?php endif; ?>

                    </h2>

                </div>

            </div>

        </div>


        <!-- Classroom -->
        <div class="col-12 col-md-6 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Classroom
                    </div>

                    <h2 class="h5 fw-bold mb-1">

                        <?= htmlspecialchars(
                            $classroomName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </h2>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- ACCOUNT INFORMATION                                            -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-0">
                Account Information
            </h2>

        </div>


        <div class="card-body">

            <div class="row g-4">


                <!-- Email -->
                <div class="col-12 col-md-6">

                    <div class="text-muted small mb-1">
                        Login Email
                    </div>

                    <div class="fw-semibold">

                        <?php if (
                            $email !== ''
                        ): ?>

                            <?= htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        <?php else: ?>

                            Not available

                        <?php endif; ?>

                    </div>

                </div>


                <!-- Role -->
                <div class="col-12 col-md-6">

                    <div class="text-muted small mb-1">
                        Account Role
                    </div>

                    <div class="fw-semibold">
                        Student
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PORTAL SERVICES                                                -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom">

            <h2 class="h5 fw-semibold mb-0">
                Student Portal
            </h2>

        </div>


        <div class="card-body">

            <div class="row g-3">


                <!-- ================================================= -->
                <!-- MY PROFILE                                         -->
                <!-- ================================================= -->

                <div class="col-12 col-md-6 col-xl-4">

                    <a
                        href="/SchoolERP/public/student/profile"
                        class="card h-100 text-decoration-none text-dark border"
                    >

                        <div class="card-body">

                            <h3 class="h6 fw-bold mb-2">
                                My Profile
                            </h3>

                            <p class="text-muted small mb-0">
                                View and update your personal
                                information and account security.
                            </p>

                        </div>

                    </a>

                </div>


                <!-- ================================================= -->
                <!-- MY RESULTS                                         -->
                <!-- ================================================= -->

                <div class="col-12 col-md-6 col-xl-4"> 

                    <a 
                        href="/SchoolERP/public/student/results" 
                        class="card h-100 text-decoration-none text-dark border" 
                    > 

                <div class="card-body"> 
                    <h3 class="h6 fw-bold mb-2"> 
                        My Results 
                    </h3> 

                    <p class="text-muted small mb-3"> 
                        View your academic results, scores, grades, and remarks. 
                    </p> 
                    
                    <span class="btn btn-outline-primary btn-sm"> 
                        View Results 
                    </span> 
                </div> 
            </a> 
        </div>


                <!-- ================================================= -->
                <!-- MY ATTENDANCE                                      -->
                <!-- ================================================= -->

                <div class="col-12 col-md-6 col-xl-4">

                    <a 
                        href="/SchoolERP/public/student/attendance" 
                        class="card h-100 text-decoration-none text-dark border" 
                    > 
                        <div class="card-body"> 
                            <h3 class="h6 fw-bold mb-2"> 
                                My Attendance 
                            </h3> 
                            <p class="text-muted small mb-3"> 
                                View your attendance history and attendance rate. 
                            </p> 
                            <span class="btn btn-outline-secondary btn-sm"> 
                                View Attendance 
                            </span> 
                        </div> 
                    </a> 
                </div>
            </div>  

