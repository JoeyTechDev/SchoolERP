<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var array<int,array<string,mixed>> $students
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var array<string,mixed>|null $report
 * @var int $studentId
 * @var int $sessionId
 * @var int $termId
 */

$students = $students ?? [];
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$report = $report ?? null;

$studentId = (int) ($studentId ?? 0);
$sessionId = (int) ($sessionId ?? 0);
$termId = (int) ($termId ?? 0);

/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
*/

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$success = $session->flash('success');
$error = $session->flash('error');

/*
|--------------------------------------------------------------------------
| Selected filter names
|--------------------------------------------------------------------------
*/

$selectedSessionName = '';

foreach ($sessions as $academicSession) {

    if (
        (int) (
            $academicSession['id'] ?? 0
        ) === $sessionId
    ) {
        $selectedSessionName = (string) (
            $academicSession['name'] ?? ''
        );

        break;
    }
}

$selectedTermName = '';

foreach ($terms as $term) {

    if (
        (int) (
            $term['id'] ?? 0
        ) === $termId
    ) {
        $selectedTermName = (string) (
            $term['name'] ?? ''
        );

        break;
    }
}

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- PAGE HEADER                                                    -->
    <!-- ============================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Student Report Card
            </h1>

            <p class="text-muted mb-0">
                View and manage a student's academic report.
            </p>

        </div>

        <?php if ($report !== null): ?>

            <button
                type="button"
                class="btn btn-outline-secondary"
                onclick="window.print()"
            >
                <i class="bi bi-printer me-1"></i>
                Print
            </button>

        <?php endif; ?>

    </div>


    <!-- ============================================================= -->
    <!-- FLASH MESSAGES                                                 -->
    <!-- ============================================================= -->

    <?php if (
        is_string($success)
        && $success !== ''
    ): ?>

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert"
        >

            <i class="bi bi-check-circle me-1"></i>

            <?= htmlspecialchars(
                $success,
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
        is_string($error)
        && $error !== ''
    ): ?>

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
        >

            <i class="bi bi-exclamation-circle me-1"></i>

            <?= htmlspecialchars(
                $error,
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
    <!-- REPORT FILTERS                                                 -->
    <!-- ============================================================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="/SchoolERP/public/report-card"
            >

                <div class="row g-3 align-items-end">

                    <!-- Student -->
                    <div class="col-md-4">

                        <label
                            for="student_id"
                            class="form-label"
                        >
                            Student
                        </label>

                        <select
                            id="student_id"
                            name="student_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Select Student --
                            </option>

                            <?php foreach ($students as $student): ?>

                                <?php

                                $id = (int) (
                                    $student['id'] ?? 0
                                );

                                $name = trim(
                                    (string) (
                                        $student['first_name']
                                        ?? ''
                                    )
                                    . ' '
                                    . (string) (
                                        $student['last_name']
                                        ?? ''
                                    )
                                );

                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $studentId === $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $name,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- Academic Session -->
                    <div class="col-md-3">

                        <label
                            for="academic_session_id"
                            class="form-label"
                        >
                            Academic Session
                        </label>

                        <select
                            id="academic_session_id"
                            name="academic_session_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Select Session --
                            </option>

                            <?php foreach (
                                $sessions
                                as $academicSession
                            ): ?>

                                <?php

                                $id = (int) (
                                    $academicSession['id']
                                    ?? 0
                                );

                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $sessionId === $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) (
                                            $academicSession['name']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- Term -->
                    <div class="col-md-3">

                        <label
                            for="term_id"
                            class="form-label"
                        >
                            Term
                        </label>

                        <select
                            id="term_id"
                            name="term_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Select Term --
                            </option>

                            <?php foreach ($terms as $term): ?>

                                <?php

                                $id = (int) (
                                    $term['id'] ?? 0
                                );

                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $termId === $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) (
                                            $term['name'] ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- Generate -->
                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            <i class="bi bi-file-earmark-text me-1"></i>
                            Generate
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- REPORT CONTENT                                                 -->
    <!-- ============================================================= -->

    <?php if ($report !== null): ?>

        <?php

        $student = $report['student'];

        $classroom = $report['classroom']
            ?? null;

        $academicSession =
            $report['academic_session'];

        $term = $report['term'];

        $results = $report['results']
            ?? [];

        $resultCount = (int) (
            $report['result_count']
            ?? 0
        );

        $totalScore = (int) (
            $report['total_score']
            ?? 0
        );

        $averageScore = (float) (
            $report['average_score']
            ?? 0
        );

        $position = $report['position']
            ?? null;

        $rankedStudents = (int) (
            $report['ranked_students']
            ?? 0
        );

        $attendanceSummary =
            $report['attendance_summary']
            ?? null;

        $reportSummary =
            $report['report_summary']
            ?? null;

        /*
         * Current role.
         */
        $currentRoleId = (int) (
            $session->get(
                'role_id',
                0
            )
        );

        /*
         * Report-card summary values.
         */
        $classTeacherRemark = '';

        $principalRemark = '';

        $promotionStatus = 'pending';

        if ($reportSummary !== null) {

            $classTeacherRemark = (string) (
                $reportSummary->class_teacher_remark
                ?? ''
            );

            $principalRemark = (string) (
                $reportSummary->principal_remark
                ?? ''
            );

            $promotionStatus = (string) (
                $reportSummary->promotion_status
                ?? 'pending'
            );
        }

        /*
         * Convert position to ordinal.
         */
        $ordinal = static function (
            ?int $value
        ): string {

            if ($value === null) {
                return '—';
            }

            $mod100 = $value % 100;

            if (
                $mod100 >= 11
                && $mod100 <= 13
            ) {
                return $value . 'th';
            }

            return match ($value % 10) {
                1 => $value . 'st',
                2 => $value . 'nd',
                3 => $value . 'rd',
                default => $value . 'th',
            };
        };

        ?>


        <!-- ========================================================= -->
        <!-- PRINTABLE REPORT CARD                                      -->
        <!-- ========================================================= -->

        <div class="card shadow-sm report-card">

            <div class="card-body p-4">


                <!-- ================================================= -->
                <!-- SCHOOL / STUDENT HEADER                            -->
                <!-- ================================================= -->

                <div
                    class="text-center border-bottom pb-4 mb-4"
                >

                    <h2 class="h3 fw-bold mb-1">
                        SchoolERP
                    </h2>

                    <p class="text-muted mb-3">
                        Student Academic Report
                    </p>

                    <h3 class="h4 fw-bold mb-1">

                        <?= htmlspecialchars(
                            trim(
                                (string) (
                                    $student->first_name
                                    ?? ''
                                )
                                . ' '
                                . (string) (
                                    $student->last_name
                                    ?? ''
                                )
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </h3>

                    <?php if ($classroom !== null): ?>

                        <div class="fw-semibold mt-2">

                            Classroom:

                            <?= htmlspecialchars(
                                (string) (
                                    $classroom->name
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    <?php endif; ?>

                    <div class="text-muted mt-1">

                        <?= htmlspecialchars(
                            (string) (
                                $academicSession->name
                                ?? $selectedSessionName
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                        <span class="mx-1">
                            •
                        </span>

                        <?= htmlspecialchars(
                            (string) (
                                $term->name
                                ?? $selectedTermName
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- STUDENT INFORMATION                                -->
                <!-- ================================================= -->

                <div class="row g-3 mb-4">

                    <div class="col-md-4">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Student
                            </div>

                            <div class="fw-semibold">

                                <?= htmlspecialchars(
                                    trim(
                                        (string) (
                                            $student->first_name
                                            ?? ''
                                        )
                                        . ' '
                                        . (string) (
                                            $student->last_name
                                            ?? ''
                                        )
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Classroom
                            </div>

                            <div class="fw-semibold">

                                <?= htmlspecialchars(
                                    $classroom !== null
                                        ? (string) (
                                            $classroom->name
                                            ?? ''
                                        )
                                        : 'Not Assigned',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Academic Period
                            </div>

                            <div class="fw-semibold">

                                <?= htmlspecialchars(
                                    (string) (
                                        $academicSession->name
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                                <span class="text-muted">
                                    •
                                </span>

                                <?= htmlspecialchars(
                                    (string) (
                                        $term->name
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- ACADEMIC PERFORMANCE                               -->
                <!-- ================================================= -->

                <div class="border-bottom pb-2 mb-3">

                    <h3 class="h5 mb-0">
                        Academic Performance
                    </h3>

                </div>


                <?php if ($results === []): ?>

                    <div class="alert alert-info">

                        No academic results have been recorded
                        for this student, session, and term.

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table
                            class="table table-bordered align-middle"
                        >

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Subject
                                    </th>

                                    <th class="text-center">
                                        CA
                                        <small class="text-muted">
                                            /30
                                        </small>
                                    </th>

                                    <th class="text-center">
                                        Exam
                                        <small class="text-muted">
                                            /70
                                        </small>
                                    </th>

                                    <th class="text-center">
                                        Total
                                        <small class="text-muted">
                                            /100
                                        </small>
                                    </th>

                                    <th class="text-center">
                                        Grade
                                    </th>

                                    <th>
                                        Remark
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                $counter = 1;
                                ?>

                                <?php foreach (
                                    $results
                                    as $result
                                ): ?>

                                    <?php

                                    $grade = (string) (
                                        $result['grade']
                                        ?? ''
                                    );

                                    $remark = (string) (
                                        $result['remark']
                                        ?? ''
                                    );

                                    ?>

                                    <tr>

                                        <td>
                                            <?= $counter++ ?>
                                        </td>

                                        <td>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $result[
                                                            'subject_name'
                                                        ]
                                                        ?? 'Unknown Subject'
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </strong>

                                            <?php if (
                                                !empty(
                                                    $result[
                                                        'subject_code'
                                                    ]
                                                )
                                            ): ?>

                                                <div class="small text-muted">

                                                    <?= htmlspecialchars(
                                                        (string) (
                                                            $result[
                                                                'subject_code'
                                                            ]
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </div>

                                            <?php endif; ?>

                                        </td>

                                        <td class="text-center">

                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result[
                                                        'ca_score'
                                                    ]
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                        <td class="text-center">

                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result[
                                                        'exam_score'
                                                    ]
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                        <td class="text-center fw-semibold">

                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result[
                                                        'total_score'
                                                    ]
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                        <td class="text-center">

                                            <?php if (
                                                $grade !== ''
                                            ): ?>

                                                <span
                                                    class="badge text-bg-primary"
                                                >
                                                    <?= htmlspecialchars(
                                                        $grade,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>

                                            <?php else: ?>

                                                —

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $remark !== ''
                                                    ? $remark
                                                    : '—',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>


                <!-- ================================================= -->
                <!-- ACADEMIC SUMMARY                                   -->
                <!-- ================================================= -->

                <div class="row g-3 mt-3">

                    <div class="col-md-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Subjects Recorded
                            </div>

                            <div class="fs-4 fw-bold">
                                <?= $resultCount ?>
                            </div>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Total Score
                            </div>

                            <div class="fs-4 fw-bold">
                                <?= $totalScore ?>
                            </div>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Average Score
                            </div>

                            <div class="fs-4 fw-bold">

                                <?= number_format(
                                    $averageScore,
                                    2
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Class Position
                            </div>

                            <div class="fs-4 fw-bold">

                                <?= htmlspecialchars(
                                    $ordinal($position),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                            <?php if (
                                $rankedStudents > 0
                            ): ?>

                                <div class="small text-muted">

                                    of <?= $rankedStudents ?>

                                    student<?= $rankedStudents === 1
                                        ? ''
                                        : 's' ?>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- ATTENDANCE SUMMARY                                 -->
                <!-- ================================================= -->

                <?php if (
                    $attendanceSummary !== null
                ): ?>

                    <div class="mt-5">

                        <div class="border-bottom pb-2 mb-3">

                            <h3 class="h5 mb-0">
                                Attendance Summary
                            </h3>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-2 col-6">

                                <div
                                    class="border rounded p-3 text-center h-100"
                                >

                                    <div class="small text-muted">
                                        School Days
                                    </div>

                                    <div class="fs-4 fw-bold">
                                        <?= (int) (
                                            $attendanceSummary[
                                                'total_days'
                                            ] ?? 0
                                        ) ?>
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-2 col-6">

                                <div
                                    class="border rounded p-3 text-center h-100"
                                >

                                    <div class="small text-muted">
                                        Present
                                    </div>

                                    <div class="fs-4 fw-bold text-success">
                                        <?= (int) (
                                            $attendanceSummary[
                                                'present'
                                            ] ?? 0
                                        ) ?>
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-2 col-6">

                                <div
                                    class="border rounded p-3 text-center h-100"
                                >

                                    <div class="small text-muted">
                                        Absent
                                    </div>

                                    <div class="fs-4 fw-bold text-danger">
                                        <?= (int) (
                                            $attendanceSummary[
                                                'absent'
                                            ] ?? 0
                                        ) ?>
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-2 col-6">

                                <div
                                    class="border rounded p-3 text-center h-100"
                                >

                                    <div class="small text-muted">
                                        Late
                                    </div>

                                    <div class="fs-4 fw-bold text-warning">
                                        <?= (int) (
                                            $attendanceSummary[
                                                'late'
                                            ] ?? 0
                                        ) ?>
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-2 col-6">

                                <div
                                    class="border rounded p-3 text-center h-100"
                                >

                                    <div class="small text-muted">
                                        Excused
                                    </div>

                                    <div class="fs-4 fw-bold">
                                        <?= (int) (
                                            $attendanceSummary[
                                                'excused'
                                            ] ?? 0
                                        ) ?>
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-2 col-6">

                                <div
                                    class="border rounded p-3 text-center h-100"
                                >

                                    <div class="small text-muted">
                                        Attendance Rate
                                    </div>

                                    <div class="fs-4 fw-bold">

                                        <?= number_format(
                                            (float) (
                                                $attendanceSummary[
                                                    'attendance_rate'
                                                ] ?? 0
                                            ),
                                            2
                                        ) ?>%

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- ================================================= -->
                <!-- REMARKS & PROMOTION                                -->
                <!-- ================================================= -->

                <div class="mt-5">

                    <div class="border-bottom pb-2 mb-3">

                        <h3 class="h5 mb-0">
                            Teacher's Remarks & Promotion
                        </h3>

                    </div>

                    <form
                        method="POST"
                        action="/SchoolERP/public/report-card/summary"
                    >

                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="student_id"
                            value="<?= (int) $student->id ?>"
                        >

                        <input
                            type="hidden"
                            name="academic_session_id"
                            value="<?= (int) $academicSession->id ?>"
                        >

                        <input
                            type="hidden"
                            name="term_id"
                            value="<?= (int) $term->id ?>"
                        >


                        <div class="row g-4">


                            <!-- Class Teacher Remark -->
                            <div class="col-md-6">

                                <label
                                    for="class_teacher_remark"
                                    class="form-label fw-semibold"
                                >
                                    Class Teacher's Remark
                                </label>

                                <textarea
                                    id="class_teacher_remark"
                                    name="class_teacher_remark"
                                    class="form-control"
                                    rows="5"
                                    maxlength="2000"
                                    placeholder="Enter class teacher's remark..."
                                ><?= htmlspecialchars(
                                    $classTeacherRemark,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?></textarea>

                            </div>


                            <!-- Principal Remark -->
                            <div class="col-md-6">

                                <label
                                    for="principal_remark"
                                    class="form-label fw-semibold"
                                >
                                    Principal / Administrator Remark
                                </label>

                                <textarea
                                    id="principal_remark"
                                    name="principal_remark"
                                    class="form-control"
                                    rows="5"
                                    maxlength="2000"
                                    <?= $currentRoleId !== 1
                                        ? 'disabled'
                                        : '' ?>
                                    placeholder="Enter principal's remark..."
                                ><?= htmlspecialchars(
                                    $principalRemark,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?></textarea>

                                <?php if (
                                    $currentRoleId !== 1
                                ): ?>

                                    <div class="form-text">
                                        Only an administrator can update this remark.
                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- Promotion Status -->
                            <div class="col-md-4">

                                <label
                                    for="promotion_status"
                                    class="form-label fw-semibold"
                                >
                                    Promotion Status
                                </label>

                                <select
                                    id="promotion_status"
                                    name="promotion_status"
                                    class="form-select"
                                    <?= $currentRoleId !== 1
                                        ? 'disabled'
                                        : '' ?>
                                >

                                    <option
                                        value="pending"
                                        <?= $promotionStatus === 'pending'
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        Pending
                                    </option>

                                    <option
                                        value="promoted"
                                        <?= $promotionStatus === 'promoted'
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        Promoted
                                    </option>

                                    <option
                                        value="not_promoted"
                                        <?= $promotionStatus === 'not_promoted'
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        Not Promoted
                                    </option>

                                </select>

                                <?php if (
                                    $currentRoleId !== 1
                                ): ?>

                                    <div class="form-text">
                                        Only an administrator can change promotion status.
                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- Save -->
                            <div class="col-md-8 d-flex align-items-end">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-save me-1"></i>
                                    Save Report Card Information
                                </button>

                            </div>

                        </div>

                    </form>

                </div>


                <!-- ================================================= -->
                <!-- REPORT FOOTER                                       -->
                <!-- ================================================= -->

                <div
                    class="text-center text-muted small mt-5 pt-4 border-top"
                >

                    Generated by SchoolERP

                </div>

            </div>

        </div>

    <?php else: ?>

        <!-- ========================================================= -->
        <!-- EMPTY STATE                                                -->
        <!-- ========================================================= -->

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <div class="mb-3">

                    <i
                        class="bi bi-file-earmark-text fs-1 text-muted"
                    ></i>

                </div>

                <h2 class="h5 mb-2">
                    Generate a Report Card
                </h2>

                <p class="text-muted mb-0">
                    Select a student, academic session, and term above.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>


<!-- ================================================================= -->
<!-- PRINT STYLES                                                      -->
<!-- ================================================================= -->

<style>

@media print {

    body {
        background: #fff !important;
    }

    /*
     * Hide administrative controls.
     */
    .navbar,
    .sidebar,
    .btn,
    .alert,
    .report-card form,
    .report-card form *,
    .report-card + * {
        display: none !important;
    }

    /*
     * Preserve the actual report.
     */
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }

    .report-card {
        width: 100% !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .report-card .card-body {
        padding: 0 !important;
    }

    .table {
        font-size: 12px;
    }

    .border {
        border-color: #dee2e6 !important;
    }

    /*
     * Make sure printed report sections remain visible.
     */
    .report-card .card-body > * {
        visibility: visible;
    }

}
</style>