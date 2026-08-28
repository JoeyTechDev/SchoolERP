<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\AttendanceRepository;

$repository = new AttendanceRepository();

echo "ATTENDANCE REPOSITORY TEST\n";
echo "==========================\n\n";

$studentId = 1;
$sessionId = 1;
$termId = 1;
$attendanceDate = date('Y-m-d');

/*
 * Remove any previous test record from an earlier failed run.
 */
$existing = $repository->findForStudentDate(
    $studentId,
    $attendanceDate,
    $sessionId,
    $termId
);

if ($existing !== null) {
    $repository->delete(
        (int) $existing->id
    );
}

echo "Test Date: {$attendanceDate}\n\n";

/*
 * Create test attendance.
 */
$created = $repository->create([
    'student_id' => $studentId,
    'academic_session_id' => $sessionId,
    'term_id' => $termId,
    'attendance_date' => $attendanceDate,
    'status' => 'present',
    'remarks' => 'Repository test record',
]);

if ($created === null) {
    echo "Create Attendance: FAILED\n";
    exit(1);
}

echo "Create Attendance: PASSED\n";

/*
 * Do not rely on create() returning the generated ID.
 * Retrieve the record using its unique business key.
 */
$attendance = $repository->findForStudentDate(
    $studentId,
    $attendanceDate,
    $sessionId,
    $termId
);

if ($attendance === null) {
    echo "Find Attendance: FAILED\n";
    exit(1);
}

$attendanceId = (int) $attendance->id;

echo "Actual Attendance ID: {$attendanceId}\n\n";

if ($attendanceId > 0) {
    echo "Generated ID: PASSED\n";
} else {
    echo "Generated ID: FAILED\n";
}

/*
 * Verify initial status.
 */
echo 'Initial Status: '
    . $attendance->status
    . PHP_EOL;

/*
 * Update the record.
 */
$updated = $repository->updateAttendance(
    $attendanceId,
    [
        'status' => 'late',
        'remarks' => 'Updated repository test record',
    ]
);

echo $updated
    ? "Update Attendance: PASSED\n"
    : "Update Attendance: FAILED\n";

/*
 * Verify the update.
 */
$updatedAttendance = $repository->find(
    $attendanceId
);

if (
    $updatedAttendance !== null
    && $updatedAttendance->status === 'late'
) {
    echo "Verify Update: PASSED\n";
} else {
    echo "Verify Update: FAILED\n";
}

/*
 * Student history.
 */
$history = $repository->forStudent(
    $studentId,
    $sessionId,
    $termId
);

echo 'Student History Count: '
    . count($history)
    . PHP_EOL;

/*
 * Date lookup.
 */
$dateRecords = $repository->forDate(
    $attendanceDate,
    $sessionId,
    $termId
);

echo 'Date Record Count: '
    . count($dateRecords)
    . PHP_EOL;

/*
 * Session/term lookup.
 */
$sessionTermRecords = $repository->forSessionAndTerm(
    $sessionId,
    $termId
);

echo 'Session/Term Record Count: '
    . count($sessionTermRecords)
    . PHP_EOL;

/*
 * Delete the test record.
 */
$deleted = $repository->delete(
    $attendanceId
);

echo $deleted
    ? "Delete Attendance: PASSED\n"
    : "Delete Attendance: FAILED\n";

/*
 * Confirm deletion.
 */
$deletedRecord = $repository->find(
    $attendanceId
);

echo $deletedRecord === null
    ? "Verify Delete: PASSED\n"
    : "Verify Delete: FAILED\n";

echo PHP_EOL;
echo "ATTENDANCE REPOSITORY TEST COMPLETE\n";