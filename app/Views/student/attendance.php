<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var array<int,array<string,mixed>> $history
 * @var array<string,mixed> $summary
 * @var int $sessionId
 * @var int $termId
 */

$student = $student ?? null;
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$history = $history ?? [];

$summary =
    is_array($summary ?? null)
        ? $summary
        : [];

$sessionId = (int) ($sessionId ?? 0);
$termId = (int) ($termId ?? 0);

$studentName = 'Student';

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

    if ($studentName === '') {
        $studentName = 'Student';
    }
}

$sessionName = 'Not selected';
$termName = 'Not selected';

foreach ($sessions as $session) {
    if (
        (int) (
            $session['id'] ?? 0
        ) === $sessionId
    ) {
        $sessionName = (string) (
            $session['name'] ?? ''
        );

        break;
    }
}

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

$totalDays = (int) (
    $summary['total_days'] ?? 0
);

$present = (int) (
    $summary['present'] ?? 0
);

$absent = (int) (
    $summary['absent'] ?? 0
);

$late = (int) (
    $summary['late'] ?? 0
);

$excused = (int) (
    $summary['excused'] ?? 0
);

$attendanceRate = (float) (
    $summary['attendance_rate'] ?? 0
);
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
                My Attendance
            </h1>

            <p class="text-muted mb-0">
                <?= htmlspecialchars(
                    $studentName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
                — attendance history
            </p>

        </div>

        <a
            href="/SchoolERP/public/student/dashboard"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="/SchoolERP/public/student/attendance"
            >

                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md-5">

                        <label
                            for="academic_session_id"
                            class="form-label fw-semibold"
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
                                Select session
                            </option>

                            <?php foreach (
                                $sessions
                                as $session
                            ): ?>

                                <?php
                                $id = (int) (
                                    $session['id'] ?? 0
                                );
                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $id === $sessionId
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) (
                                            $session['name'] ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="col-12 col-md-5">

                        <label
                            for="term_id"
                            class="form-label fw-semibold"
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
                                Select term
                            </option>

                            <?php foreach (
                                $terms
                                as $term
                            ): ?>

                                <?php
                                $id = (int) (
                                    $term['id'] ?? 0
                                );
                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $id === $termId
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


                    <div class="col-12 col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            View Attendance
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <?php if (
        $sessionId > 0
        && $termId > 0
    ): ?>

        <!-- Period -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-12 col-md-6">

                        <div class="small text-muted">
                            Academic Session
                        </div>

                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                $sessionName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                    <div class="col-12 col-md-6">

                        <div class="small text-muted">
                            Term
                        </div>

                        <div class="fw-semibold">
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


        <!-- Summary -->
        <div class="row g-3 mb-4">

            <div class="col-6 col-md-4 col-xl-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Recorded Days
                        </div>

                        <div class="fs-4 fw-bold">
                            <?= $totalDays ?>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-4 col-xl-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Present
                        </div>

                        <div class="fs-4 fw-bold">
                            <?= $present ?>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-4 col-xl-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Absent
                        </div>

                        <div class="fs-4 fw-bold">
                            <?= $absent ?>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-4 col-xl-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Late
                        </div>

                        <div class="fs-4 fw-bold">
                            <?= $late ?>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-4 col-xl-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Excused
                        </div>

                        <div class="fs-4 fw-bold">
                            <?= $excused ?>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-4 col-xl-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Attendance Rate
                        </div>

                        <div class="fs-4 fw-bold">
                            <?= number_format(
                                $attendanceRate,
                                2
                            ) ?>%
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Attendance History -->
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-bottom">

                <h2 class="h5 fw-semibold mb-0">
                    Attendance History
                </h2>

            </div>

            <div class="card-body">

                <?php if (
                    $history === []
                ): ?>

                    <div class="text-center py-5">

                        <h3 class="h5 mb-2">
                            No Attendance Records
                        </h3>

                        <p class="text-muted mb-0">
                            No attendance has been recorded
                            for this session and term.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle mb-0"
                        >

                            <thead>

                                <tr>

                                    <th>
                                        Date
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

                                <?php foreach (
                                    $history
                                    as $record
                                ): ?>

                                    <?php
                                    $status = strtolower(
                                        trim(
                                            (string) (
                                                $record['status']
                                                ?? ''
                                            )
                                        )
                                    );

                                    $statusLabel =
                                        ucfirst(
                                            $status !== ''
                                                ? $status
                                                : 'Unknown'
                                        );

                                    $statusClass =
                                        match ($status) {
                                            'present' =>
                                                'text-bg-success',

                                            'absent' =>
                                                'text-bg-danger',

                                            'late' =>
                                                'text-bg-warning',

                                            'excused' =>
                                                'text-bg-info',

                                            default =>
                                                'text-bg-secondary',
                                        };

                                    $date =
                                        (string) (
                                            $record[
                                                'attendance_date'
                                            ] ?? ''
                                        );

                                    $remark = trim(
                                        (string) (
                                            $record[
                                                'remarks'
                                            ] ?? ''
                                        )
                                    );
                                    ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars(
                                                $date,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>


                                        <td>

                                            <span
                                                class="badge <?= $statusClass ?>"
                                            >
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

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <h2 class="h5 mb-2">
                    Select Your Academic Period
                </h2>

                <p class="text-muted mb-0">
                    Select an academic session and term
                    to view your attendance.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>