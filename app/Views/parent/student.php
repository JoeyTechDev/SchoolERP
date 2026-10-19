<?php

declare(strict_types=1);

/**
 * SchoolERP - Parent Student Details
 *
 * @var array<string,mixed> $student
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var array<int,array<string,mixed>> $subjects
 * @var array<int,array<string,mixed>> $results
 * @var array<int,array<string,mixed>> $attendanceHistory
 * @var array<string,mixed> $attendanceSummary
 * @var int $sessionId
 * @var int $termId
 */

$student = is_array($student ?? null) ? $student : [];
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$subjects = $subjects ?? [];
$results = $results ?? [];
$attendanceHistory = $attendanceHistory ?? [];

$attendanceSummary = is_array($attendanceSummary ?? null)
    ? $attendanceSummary
    : [];

$sessionId = (int) ($sessionId ?? 0);
$termId = (int) ($termId ?? 0);

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

$formatValue = static function (mixed $value) use ($escape): string {
    return trim((string) ($value ?? '')) !== ''
        ? $escape($value)
        : 'Not provided';
};

$fullName = trim(
    (string) ($student['first_name'] ?? '')
    . ' '
    . (string) ($student['last_name'] ?? '')
);

$gender = ucfirst(
    strtolower((string) ($student['gender'] ?? ''))
);

$dateOfBirth = trim(
    (string) ($student['date_of_birth'] ?? '')
);

if ($dateOfBirth !== '') {
    $date = \DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $dateOfBirth
    );

    if (
        $date !== false
        && $date->format('Y-m-d') === $dateOfBirth
    ) {
        $dateOfBirth = $date->format('d M Y');
    }
}

/*
 * Resolve the displayed academic session and term.
 */
$sessionName = 'Not selected';
$termName = 'Not selected';

foreach ($sessions as $session) {
    if ((int) ($session['id'] ?? 0) === $sessionId) {
        $sessionName = (string) ($session['name'] ?? '');
        break;
    }
}

foreach ($terms as $term) {
    if ((int) ($term['id'] ?? 0) === $termId) {
        $termName = (string) ($term['name'] ?? '');
        break;
    }
}

/*
 * Build a subject lookup using the existing subject repository.
 */
$subjectLookup = [];

foreach ($subjects as $subject) {
    $subjectId = (int) ($subject['id'] ?? 0);

    if ($subjectId <= 0) {
        continue;
    }

    $subjectLookup[$subjectId] = [
        'name' => (string) ($subject['name'] ?? ''),
        'code' => (string) ($subject['code'] ?? ''),
    ];
}

/*
 * Calculate result totals.
 */
$resultCount = count($results);
$totalScore = 0.0;

foreach ($results as $result) {
    $totalScore += (float) ($result['total_score'] ?? 0);
}

$averageScore = $resultCount > 0
    ? $totalScore / $resultCount
    : 0.0;

$totalDays = (int) ($attendanceSummary['total_days'] ?? 0);
$present = (int) ($attendanceSummary['present'] ?? 0);
$absent = (int) ($attendanceSummary['absent'] ?? 0);
$late = (int) ($attendanceSummary['late'] ?? 0);
$excused = (int) ($attendanceSummary['excused'] ?? 0);

$attendanceRate = (float) (
    $attendanceSummary['attendance_rate'] ?? 0
);
?>

<div class="container-fluid py-4">

    <!-- Page header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Student Details</h1>
            <p class="text-muted mb-0">
                Profile, academic performance and attendance.
            </p>
        </div>

        <a
            href="/SchoolERP/public/parent/dashboard"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>
    </div>

    <!-- Student profile -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h2 class="h5 fw-semibold mb-0">
                <?= $escape($fullName !== '' ? $fullName : 'Student') ?>
            </h2>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th scope="row" style="width: 35%;">
                                Admission Number
                            </th>
                            <td>
                                <?= $formatValue(
                                    $student['admission_number'] ?? null
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">First Name</th>
                            <td>
                                <?= $formatValue(
                                    $student['first_name'] ?? null
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Last Name</th>
                            <td>
                                <?= $formatValue(
                                    $student['last_name'] ?? null
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Date of Birth</th>
                            <td>
                                <?= $formatValue(
                                    $dateOfBirth !== '' ? $dateOfBirth : null
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Gender</th>
                            <td>
                                <?= $formatValue(
                                    $gender !== '' ? $gender : null
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Classroom</th>
                            <td>
                                <?= $formatValue(
                                    $student['classroom_name'] ?? null
                                ) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
<!-- Academic period -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h5 fw-semibold mb-3">
            Academic Period
        </h2>

        <form
            method="GET"
            action="/SchoolERP/public/parent/students/<?= (int) ($student['id'] ?? 0) ?>"
            class="row g-3 align-items-end mb-4"
        >
            <div class="col-12 col-md-5">
                <label
                    for="academic_session_id"
                    class="form-label"
                >
                    Academic Session
                </label>

                <select
                    name="academic_session_id"
                    id="academic_session_id"
                    class="form-select"
                    required
                >
                    <option value="">
                        Select academic session
                    </option>

                    <?php foreach ($sessions as $session): ?>
                        <?php $optionSessionId = (int) ($session['id'] ?? 0); ?>

                        <option
                            value="<?= $optionSessionId ?>"
                            <?= $optionSessionId === $sessionId ? 'selected' : '' ?>
                        >
                            <?= $escape($session['name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label
                    for="term_id"
                    class="form-label"
                >
                    Term
                </label>

                <select
                    name="term_id"
                    id="term_id"
                    class="form-select"
                    required
                >
                    <option value="">
                        Select term
                    </option>

                    <?php foreach ($terms as $term): ?>
                        <?php $optionTermId = (int) ($term['id'] ?? 0); ?>

                        <option
                            value="<?= $optionTermId ?>"
                            <?= $optionTermId === $termId ? 'selected' : '' ?>
                        >
                            <?= $escape($term['name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-3">
                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    View Records
                </button>
            </div>
        </form>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="small text-muted">
                    Selected Academic Session
                </div>

                <div class="fw-semibold">
                    <?= $escape($sessionName) ?>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="small text-muted">
                    Selected Term
                </div>

                <div class="fw-semibold">
                    <?= $escape($termName) ?>
                </div>
            </div>
        </div>

        <?php if ($sessionId <= 0 || $termId <= 0): ?>
            <p class="text-muted mt-3 mb-0">
                Academic results and attendance will appear
                after an academic session and term are configured.
            </p>
        <?php endif; ?>
    </div>
</div>

    <!-- Academic results -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h2 class="h5 fw-semibold mb-0">Academic Results</h2>
        </div>

        <div class="card-body">

            <?php if ($sessionId <= 0 || $termId <= 0): ?>

                <p class="text-muted mb-0">
                    No academic period is currently available.
                </p>

            <?php elseif ($results === []): ?>

                <div class="text-center py-4">
                    <h3 class="h6 mb-2">No Results Available</h3>
                    <p class="text-muted mb-0">
                        No results have been recorded for this student
                        in the displayed academic period.
                    </p>
                </div>

            <?php else: ?>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="rounded border p-3 h-100">
                            <div class="small text-muted">
                                Subjects Recorded
                            </div>
                            <div class="fs-4 fw-bold">
                                <?= $resultCount ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="rounded border p-3 h-100">
                            <div class="small text-muted">
                                Total Score
                            </div>
                            <div class="fs-4 fw-bold">
                                <?= number_format($totalScore, 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="rounded border p-3 h-100">
                            <div class="small text-muted">
                                Average Score
                            </div>
                            <div class="fs-4 fw-bold">
                                <?= number_format($averageScore, 2) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th class="text-center">CA</th>
                                <th class="text-center">Exam</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Grade</th>
                                <th>Remark</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <?php
                                $subjectId = (int) (
                                    $result['subject_id'] ?? 0
                                );

                                $subject = $subjectLookup[$subjectId] ?? [];

                                $subjectName = (string) (
                                    $subject['name']
                                    ?? $result['subject']
                                    ?? 'Unknown Subject'
                                );

                                $subjectCode = (string) (
                                    $subject['code'] ?? ''
                                );

                                $grade = trim(
                                    (string) ($result['grade'] ?? '')
                                );

                                $remark = trim(
                                    (string) ($result['remark'] ?? '')
                                );
                                ?>

                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            <?= $escape($subjectName) ?>
                                        </div>

                                        <?php if ($subjectCode !== ''): ?>
                                            <div class="small text-muted">
                                                <?= $escape($subjectCode) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?= $formatValue(
                                            $result['ca_score'] ?? null
                                        ) ?>
                                    </td>

                                    <td class="text-center">
                                        <?= $formatValue(
                                            $result['exam_score'] ?? null
                                        ) ?>
                                    </td>

                                    <td class="text-center fw-bold">
                                        <?= $formatValue(
                                            $result['total_score'] ?? null
                                        ) ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($grade !== ''): ?>
                                            <span class="badge text-bg-primary">
                                                <?= $escape($grade) ?>
                                            </span>
                                        <?php else: ?>
                                            &mdash;
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= $remark !== ''
                                            ? $escape($remark)
                                            : '&mdash;' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <!-- Attendance summary -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h2 class="h5 fw-semibold mb-0">Attendance Summary</h2>
        </div>

        <div class="card-body">
            <?php if ($sessionId <= 0 || $termId <= 0): ?>

                <p class="text-muted mb-0">
                    No academic period is currently available.
                </p>

            <?php else: ?>

                <div class="row g-3">
                    <?php
                    $attendanceCards = [
                        ['label' => 'Recorded Days', 'value' => $totalDays],
                        ['label' => 'Present', 'value' => $present],
                        ['label' => 'Absent', 'value' => $absent],
                        ['label' => 'Late', 'value' => $late],
                        ['label' => 'Excused', 'value' => $excused],
                        [
                            'label' => 'Attendance Rate',
                            'value' => number_format($attendanceRate, 2) . '%',
                        ],
                    ];
                    ?>

                    <?php foreach ($attendanceCards as $card): ?>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="rounded border p-3 h-100">
                                <div class="small text-muted">
                                    <?= $escape($card['label']) ?>
                                </div>
                                <div class="fs-4 fw-bold">
                                    <?= $escape($card['value']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <!-- Attendance history -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h2 class="h5 fw-semibold mb-0">Attendance History</h2>
        </div>

        <div class="card-body">

            <?php if ($sessionId <= 0 || $termId <= 0): ?>

                <p class="text-muted mb-0">
                    No academic period is currently available.
                </p>

            <?php elseif ($attendanceHistory === []): ?>

                <div class="text-center py-4">
                    <h3 class="h6 mb-2">No Attendance Records</h3>
                    <p class="text-muted mb-0">
                        No attendance has been recorded for this student
                        in the displayed academic period.
                    </p>
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($attendanceHistory as $record): ?>
                                <?php
                                $status = strtolower(
                                    trim((string) ($record['status'] ?? ''))
                                );

                                $statusLabel = ucfirst(
                                    $status !== '' ? $status : 'Unknown'
                                );

                                $statusClass = match ($status) {
                                    'present' => 'text-bg-success',
                                    'absent' => 'text-bg-danger',
                                    'late' => 'text-bg-warning',
                                    'excused' => 'text-bg-info',
                                    default => 'text-bg-secondary',
                                };

                                $dateValue = trim(
                                    (string) (
                                        $record['attendance_date'] ?? ''
                                    )
                                );

                                $remark = trim(
                                    (string) ($record['remarks'] ?? '')
                                );
                                ?>

                                <tr>
                                    <td>
                                        <?= $formatValue(
                                            $dateValue !== '' ? $dateValue : null
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= $escape($statusLabel) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= $remark !== ''
                                            ? $escape($remark)
                                            : '&mdash;' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>
