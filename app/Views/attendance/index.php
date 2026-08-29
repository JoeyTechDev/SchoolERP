<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var array<int,array<string,mixed>> $classrooms
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var array<int,array<string,mixed>> $students
 * @var array<int,array<string,mixed>> $existingAttendance
 * @var int $classroomId
 * @var int $sessionId
 * @var int $termId
 * @var string $attendanceDate
 */

$classrooms = $classrooms ?? [];
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$students = $students ?? [];
$existingAttendance = $existingAttendance ?? [];

$classroomId = (int) ($classroomId ?? 0);
$sessionId = (int) ($sessionId ?? 0);
$termId = (int) ($termId ?? 0);

$attendanceDate = (string) (
    $attendanceDate ?? date('Y-m-d')
);

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$success = $session->flash('success');
$error = $session->flash('error');

$selectedClassroomName = '';

foreach ($classrooms as $classroom) {
    if ((int) $classroom['id'] === $classroomId) {
        $selectedClassroomName = (string) (
            $classroom['name'] ?? ''
        );

        break;
    }
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Daily Attendance
            </h1>

            <p class="text-muted mb-0">
                Record attendance for an entire classroom.
            </p>

        </div>

    </div>

    <?php if (
        is_string($success)
        && $success !== ''
    ): ?>

        <div class="alert alert-success alert-dismissible fade show">
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

        <div class="alert alert-danger alert-dismissible fade show">

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

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="/SchoolERP/public/attendance"
            >

                <div class="row g-3 align-items-end">

                    <div class="col-md-3">

                        <label
                            for="classroom_id"
                            class="form-label"
                        >
                            Classroom
                        </label>

                        <select
                            id="classroom_id"
                            name="classroom_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Select Classroom --
                            </option>

                            <?php foreach ($classrooms as $classroom): ?>

                                <?php
                                $id = (int) (
                                    $classroom['id'] ?? 0
                                );
                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $classroomId === $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) (
                                            $classroom['name'] ?? ''
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

                        <label
                            for="attendance_date"
                            class="form-label"
                        >
                            Date
                        </label>

                        <input
                            type="date"
                            id="attendance_date"
                            name="attendance_date"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $attendanceDate,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            required
                        >

                    </div>

                    <div class="col-md-1">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            title="Load Classroom"
                        >
                            Load
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <?php if ($classroomId > 0): ?>

        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                $selectedClassroomName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                        <div class="small text-muted">
                            <?= htmlspecialchars(
                                $attendanceDate,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                    <?php if ($students !== []): ?>

                        <span class="badge text-bg-primary">
                            <?= count($students) ?>
                            student<?= count($students) === 1 ? '' : 's' ?>
                        </span>

                    <?php endif; ?>

                </div>

            </div>

            <?php if ($students === []): ?>

                <div class="card-body">

                    <div class="alert alert-info mb-0">
                        No students are currently assigned to this classroom.
                    </div>

                </div>

            <?php else: ?>

                <form
                    method="POST"
                    action="/SchoolERP/public/attendance"
                >

                    <?= csrf_field() ?>

                    <input
                        type="hidden"
                        name="classroom_id"
                        value="<?= $classroomId ?>"
                    >

                    <input
                        type="hidden"
                        name="academic_session_id"
                        value="<?= $sessionId ?>"
                    >

                    <input
                        type="hidden"
                        name="term_id"
                        value="<?= $termId ?>"
                    >

                    <input
                        type="hidden"
                        name="attendance_date"
                        value="<?= htmlspecialchars(
                            $attendanceDate,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th style="width: 50px;">
                                        #
                                    </th>

                                    <th>
                                        Student
                                    </th>

                                    <th class="text-center">
                                        Attendance Status
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

                                <?php foreach ($students as $student): ?>

                                    <?php
                                    $studentId = (int) (
                                        $student['id'] ?? 0
                                    );

                                    $studentName = trim(
                                        (string) (
                                            $student['first_name'] ?? ''
                                        )
                                        . ' '
                                        . (string) (
                                            $student['last_name'] ?? ''
                                        )
                                    );

                                    $record =
                                        $existingAttendance[
                                            $studentId
                                        ] ?? null;

                                    $currentStatus = (
                                        is_array($record)
                                        && isset($record['status'])
                                    )
                                        ? (string) $record['status']
                                        : 'present';

                                    $currentRemark = (
                                        is_array($record)
                                        && isset($record['remarks'])
                                    )
                                        ? (string) $record['remarks']
                                        : '';
                                    ?>

                                    <tr>

                                        <td>
                                            <?= $counter++ ?>
                                        </td>

                                        <td>

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $studentName,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>

                                        </td>

                                        <td>

                                            <div class="d-flex flex-wrap gap-2 justify-content-center">

                                                <?php
                                                $statuses = [
                                                    'present' => 'Present',
                                                    'absent' => 'Absent',
                                                    'late' => 'Late',
                                                    'excused' => 'Excused',
                                                ];
                                                ?>

                                                <?php foreach (
                                                    $statuses
                                                    as $value => $label
                                                ): ?>

                                                    <div class="form-check">

                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="status[<?= $studentId ?>]"
                                                            id="<?= $value ?>_<?= $studentId ?>"
                                                            value="<?= $value ?>"
                                                            <?= $currentStatus === $value
                                                                ? 'checked'
                                                                : '' ?>
                                                        >

                                                        <label
                                                            class="form-check-label small"
                                                            for="<?= $value ?>_<?= $studentId ?>"
                                                        >
                                                            <?= $label ?>
                                                        </label>

                                                    </div>

                                                <?php endforeach; ?>

                                            </div>

                                        </td>

                                        <td>

                                            <input
                                                type="text"
                                                name="remarks[<?= $studentId ?>]"
                                                class="form-control form-control-sm"
                                                maxlength="255"
                                                value="<?= htmlspecialchars(
                                                    $currentRemark,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                placeholder="Optional"
                                            >

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="card-footer bg-white">

                        <div class="d-flex justify-content-between align-items-center">

                            <div class="small text-muted">
                                New records default to Present.
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Save Attendance
                            </button>

                        </div>

                    </div>

                </form>

            <?php endif; ?>

        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <h2 class="h5 mb-2">
                    Load a Classroom
                </h2>

                <p class="text-muted mb-0">
                    Select a classroom, session, term, and date
                    to begin recording attendance.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>