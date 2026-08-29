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

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$error = $session->flash('error');

$studentName = '';

foreach ($students as $student) {
    if ((int) $student['id'] === $studentId) {
        $studentName = trim(
            (string) ($student['first_name'] ?? '')
            . ' '
            . (string) ($student['last_name'] ?? '')
        );

        break;
    }
}

$sessionName = '';

foreach ($sessions as $academicSession) {
    if ((int) $academicSession['id'] === $sessionId) {
        $sessionName = (string) (
            $academicSession['name'] ?? ''
        );

        break;
    }
}

$termName = '';

foreach ($terms as $term) {
    if ((int) $term['id'] === $termId) {
        $termName = (string) (
            $term['name'] ?? ''
        );

        break;
    }
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Student Report Card
            </h1>

            <p class="text-muted mb-0">
                View academic performance by session and term.
            </p>

        </div>

        <?php if ($report !== null): ?>

            <button
                type="button"
                class="btn btn-outline-secondary"
                onclick="window.print()"
            >
                Print
            </button>

        <?php endif; ?>

    </div>

    <?php if (
        is_string($error)
        && $error !== ''
    ): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="/SchoolERP/public/report-card"
            >

                <div class="row g-3 align-items-end">

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

                            <?php foreach ($sessions as $academicSession): ?>

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

                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Generate
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <?php if ($report !== null): ?>

        <?php
        $student = $report['student'];
        $classroom = $report['classroom'] ?? null;
        $academicSession = $report['academic_session'];
        $term = $report['term'];
        $results = $report['results'] ?? [];

        $attendanceSummary =
        $report['attendance_summary'] ?? null;

        $resultCount = (int) (
            $report['result_count'] ?? 0
        );

        $totalScore = (int) (
            $report['total_score'] ?? 0
        );

        $averageScore = (float) (
            $report['average_score'] ?? 0
        );
        ?>

        <div class="card shadow-sm report-card">

            <div class="card-body p-4">

                <div class="text-center border-bottom pb-4 mb-4">

                    <h2 class="h3 fw-bold mb-1">
                        SchoolERP
                    </h2>

                    <p class="text-muted mb-3">
                        Student Academic Report
                    </p>

                    <h3 class="h4 fw-bold mb-1">
                        <?= htmlspecialchars(
                            trim(
                                (string) $student->first_name
                                . ' '
                                . (string) $student->last_name
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h3>

                    <?php if ($classroom !== null): ?>

                    <div class="fw-semibold mt-2">
                        Classroom:
                        <?= htmlspecialchars(
                            (string) $classroom->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                    <?php endif; ?>
                    <div class="text-muted">
                        <?= htmlspecialchars(
                            (string) (
                                $academicSession->name
                                ?? $sessionName
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                        &nbsp;•&nbsp;

                        <?= htmlspecialchars(
                            (string) (
                                $term->name
                                ?? $termName
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                </div>

                <div class="row g-3 mb-4">

                    <div class="col-md-4">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Student
                            </div>

                            <div class="fw-semibold">
                                <?= htmlspecialchars(
                                    trim(
                                        (string) $student->first_name
                                        . ' '
                                        . (string) $student->last_name
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
                                Academic Session
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
                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted">
                                Term
                            </div>

                            <div class="fw-semibold">
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

                <?php if ($results === []): ?>

                    <div class="alert alert-info">
                        No academic results have been recorded
                        for this student, session, and term.
                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

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
                                $position = 1;
                                ?>

                                <?php foreach ($results as $result): ?>

                                    <tr>

                                        <td>
                                            <?= $position++ ?>
                                        </td>

                                        <td>

                                            <strong>
                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $result['subject_name']
                                                        ?? 'Unknown Subject'
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                            <?php if (
                                                !empty(
                                                    $result['subject_code']
                                                )
                                            ): ?>

                                                <div class="small text-muted">
                                                    <?= htmlspecialchars(
                                                        (string) $result[
                                                            'subject_code'
                                                        ],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </div>

                                            <?php endif; ?>

                                        </td>

                                        <td class="text-center">
                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['ca_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td class="text-center">
                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['exam_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td class="text-center fw-semibold">
                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['total_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td class="text-center">

                                            <?php
                                            $grade = (string) (
                                                $result['grade'] ?? ''
                                            );
                                            ?>

                                            <?php if ($grade !== ''): ?>

                                                <span class="badge text-bg-primary">
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
                                                (string) (
                                                    $result['remark']
                                                    ?: '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

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

            <?php
            $position = $report['position'] ?? null;
            $rankedStudents = (int) (
                $report['ranked_students'] ?? 0
            );

            $ordinal = static function (?int $value): string {
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

            <div class="fs-4 fw-bold">
                <?= htmlspecialchars(
                    $ordinal(
                        $position
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

            <?php if ($rankedStudents > 0): ?>

                <div class="small text-muted">
                    of <?= $rankedStudents ?>
                    student<?= $rankedStudents === 1 ? '' : 's' ?>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>    

                <?php endif; ?>

<?php if ($attendanceSummary !== null): ?>

    <div class="mt-5">

        <div class="border-bottom pb-2 mb-3">

            <h3 class="h5 mb-0">
                Attendance Summary
            </h3>

        </div>

        <div class="row g-3">

            <div class="col-md-2 col-6">

                <div class="border rounded p-3 text-center h-100">

                    <div class="small text-muted">
                        School Days
                    </div>

                    <div class="fs-4 fw-bold">
                        <?= (int) (
                            $attendanceSummary['total_days']
                            ?? 0
                        ) ?>
                    </div>

                </div>

            </div>

            <div class="col-md-2 col-6">

                <div class="border rounded p-3 text-center h-100">

                    <div class="small text-muted">
                        Present
                    </div>

                    <div class="fs-4 fw-bold text-success">
                        <?= (int) (
                            $attendanceSummary['present']
                            ?? 0
                        ) ?>
                    </div>

                </div>

            </div>

            <div class="col-md-2 col-6">

                <div class="border rounded p-3 text-center h-100">

                    <div class="small text-muted">
                        Absent
                    </div>

                    <div class="fs-4 fw-bold text-danger">
                        <?= (int) (
                            $attendanceSummary['absent']
                            ?? 0
                        ) ?>
                    </div>

                </div>

            </div>

            <div class="col-md-2 col-6">

                <div class="border rounded p-3 text-center h-100">

                    <div class="small text-muted">
                        Late
                    </div>

                    <div class="fs-4 fw-bold text-warning">
                        <?= (int) (
                            $attendanceSummary['late']
                            ?? 0
                        ) ?>
                    </div>

                </div>

            </div>

            <div class="col-md-2 col-6">

                <div class="border rounded p-3 text-center h-100">

                    <div class="small text-muted">
                        Excused
                    </div>

                    <div class="fs-4 fw-bold">
                        <?= (int) (
                            $attendanceSummary['excused']
                            ?? 0
                        ) ?>
                    </div>

                </div>

            </div>

            <div class="col-md-2 col-6">

                <div class="border rounded p-3 text-center h-100">

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

                <div class="text-center text-muted small mt-5">

                    Generated by SchoolERP

                </div>

            </div>

        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

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

<style>
@media print {
    .navbar,
    .sidebar,
    .btn,
    form,
    .alert,
    .report-card + * {
        display: none !important;
    }

    body {
        background: #fff !important;
    }

    .container-fluid {
        padding: 0 !important;
    }

    .report-card {
        box-shadow: none !important;
        border: 0 !important;
    }
}
</style>