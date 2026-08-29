<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var array<int,array<string,mixed>> $students
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var array<int,array<string,mixed>> $history
 * @var array<string,mixed>|null $summary
 * @var \SchoolERP\Models\Student|null $student
 * @var \SchoolERP\Models\Classroom|null $classroom
 * @var int $studentId
 * @var int $sessionId
 * @var int $termId
 */

$students = $students ?? [];
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$history = $history ?? [];
$summary = $summary ?? null;

$student = $student ?? null;
$classroom = $classroom ?? null;

$studentId = (int) ($studentId ?? 0);
$sessionId = (int) ($sessionId ?? 0);
$termId = (int) ($termId ?? 0);

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$error = $session->flash('error');

$studentName = '';

if ($student !== null) {
    $studentName = trim(
        (string) (
            $student->first_name ?? ''
        )
        . ' '
        . (string) (
            $student->last_name ?? ''
        )
    );
}

$sessionName = '';

foreach ($sessions as $academicSession) {
    if (
        (int) (
            $academicSession['id'] ?? 0
        ) === $sessionId
    ) {
        $sessionName = (string) (
            $academicSession['name'] ?? ''
        );

        break;
    }
}

$termName = '';

foreach ($terms as $term) {
    if (
        (int) (
            $term['id'] ?? 0
        ) === $termId
    ) {
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
                Attendance History
            </h1>

            <p class="text-muted mb-0">
                Review a student's attendance for a selected session and term.
            </p>

        </div>

        <a
            href="/SchoolERP/public/attendance"
            class="btn btn-outline-primary"
        >
            Daily Attendance
        </a>

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
                action="/SchoolERP/public/attendance/history"
            >

                <div class="row g-3 align-items-end">

                    <div class="col-md-5">

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

                            <?php foreach ($students as $item): ?>

                                <?php
                                $id = (int) (
                                    $item['id'] ?? 0
                                );

                                $name = trim(
                                    (string) (
                                        $item['first_name'] ?? ''
                                    )
                                    . ' '
                                    . (string) (
                                        $item['last_name'] ?? ''
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
                                    $academicSession['id'] ?? 0
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
                                            $academicSession['name'] ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-2">

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
                            View History
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <?php if ($student !== null): ?>

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <div class="small text-muted">
                            Student
                        </div>

                        <div class="fs-5 fw-semibold">
                            <?= htmlspecialchars(
                                $studentName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="small text-muted">
                            Classroom
                        </div>

                        <div class="fs-5 fw-semibold">
                            <?= htmlspecialchars(
                                $classroom !== null
                                    ? (string) $classroom->name
                                    : 'Not Assigned',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="small text-muted">
                            Academic Period
                        </div>

                        <div class="fs-5 fw-semibold">
                            <?= htmlspecialchars(
                                $sessionName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            <span class="text-muted">
                                •
                            </span>

                            <?= htmlspecialchars(
                                $termName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php if ($summary !== null): ?>

            <div class="row g-3 mb-4">

                <div class="col-md-2">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div class="small text-muted">
                                Total Days
                            </div>

                            <div class="fs-3 fw-bold">
                                <?= (int) (
                                    $summary['total_days'] ?? 0
                                ) ?>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-md-2">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div class="small text-muted">
                                Present
                            </div>

                            <div class="fs-3 fw-bold text-success">
                                <?= (int) (
                                    $summary['present'] ?? 0
                                ) ?>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-md-2">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div class="small text-muted">
                                Absent
                            </div>

                            <div class="fs-3 fw-bold text-danger">
                                <?= (int) (
                                    $summary['absent'] ?? 0
                                ) ?>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-md-2">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div class="small text-muted">
                                Late
                            </div>

                            <div class="fs-3 fw-bold text-warning">
                                <?= (int) (
                                    $summary['late'] ?? 0
                                ) ?>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-md-2">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div class="small text-muted">
                                Excused
                            </div>

                            <div class="fs-3 fw-bold">
                                <?= (int) (
                                    $summary['excused'] ?? 0
                                ) ?>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-md-2">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div class="small text-muted">
                                Attendance Rate
                            </div>

                            <div class="fs-3 fw-bold">
                                <?= number_format(
                                    (float) (
                                        $summary['attendance_rate']
                                        ?? 0
                                    ),
                                    2
                                ) ?>%
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        <?php endif; ?>

        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <div class="fw-semibold">
                    Attendance Records
                </div>

            </div>

            <div class="card-body p-0">

                <?php if ($history === []): ?>

                    <div class="p-4">

                        <div class="alert alert-info mb-0">
                            No attendance records were found
                            for this student, session, and term.
                        </div>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Day
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Remarks
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                $counter = 1;
                                ?>

                                <?php foreach ($history as $record): ?>

                                    <?php
                                    $date = (string) (
                                        $record['attendance_date']
                                        ?? ''
                                    );

                                    $status = strtolower(
                                        (string) (
                                            $record['status']
                                            ?? ''
                                        )
                                    );

                                    $remark = (string) (
                                        $record['remarks']
                                        ?? ''
                                    );

                                    $statusLabels = [
                                        'present' => 'Present',
                                        'absent' => 'Absent',
                                        'late' => 'Late',
                                        'excused' => 'Excused',
                                    ];

                                    $statusLabel =
                                        $statusLabels[$status]
                                        ?? ucfirst($status);

                                    $statusClasses = [
                                        'present' => 'text-bg-success',
                                        'absent' => 'text-bg-danger',
                                        'late' => 'text-bg-warning',
                                        'excused' => 'text-bg-secondary',
                                    ];

                                    $statusClass =
                                        $statusClasses[$status]
                                        ?? 'text-bg-secondary';

                                    $dayName = '';

                                    if ($date !== '') {
                                        $timestamp = strtotime($date);

                                        if ($timestamp !== false) {
                                            $dayName = date(
                                                'l',
                                                $timestamp
                                            );
                                        }
                                    }
                                    ?>

                                    <tr>

                                        <td>
                                            <?= $counter++ ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $date,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $dayName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>

                                            <span class="badge <?= $statusClass ?>">
                                                <?= htmlspecialchars(
                                                    $statusLabel,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </span>

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

            </div>

        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <h2 class="h5 mb-2">
                    Select a Student
                </h2>

                <p class="text-muted mb-0">
                    Select a student, session, and term above
                    to view attendance history.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>