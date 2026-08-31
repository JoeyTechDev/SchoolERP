<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\AttendanceRepository;

$repository = new AttendanceRepository();

$studentId = 1;
$sessionId = 1;
$termId = 1;
$testDate = '2099-02-15';

echo "ATTENDANCE UNCHANGED UPDATE TEST\n";
echo "================================\n\n";

/*
 * Clean existing test record.
 */
$existing = $repository->findForStudentDate(
    $studentId,
    $testDate,
    $sessionId,
    $termId
);

if ($existing !== null) {
    $repository->delete(
        (int) $existing->id
    );
}

/*
 * Create the initial record.
 */
$repository->create([
    'student_id' => $studentId,
    'academic_session_id' => $sessionId,
    'term_id' => $termId,
    'attendance_date' => $testDate,
    'status' => 'present',
    'remarks' => null,
]);

$attendance = $repository->findForStudentDate(
    $studentId,
    $testDate,
    $sessionId,
    $termId
);

if ($attendance === null) {
    echo "Create Record: FAILED\n";
    exit(1);
}

echo "Create Record: PASSED\n";

/*
 * Submit exactly the same values.
 *
 * Previously this could return FALSE because the database
 * affected zero rows.
 */
$updated = $repository->updateAttendance(
    (int) $attendance->id,
    [
        'status' => 'present',
        'remarks' => null,
    ]
);

echo $updated
    ? "Unchanged Update: PASSED\n"
    : "Unchanged Update: FAILED\n";

/*
 * Now change the values.
 */
$updated = $repository->updateAttendance(
    (int) $attendance->id,
    [
        'status' => 'absent',
        'remarks' => 'Changed status',
    ]
);

echo $updated
    ? "Changed Update: PASSED\n"
    : "Changed Update: FAILED\n";

/*
 * Verify.
 */
$verified = $repository->find(
    (int) $attendance->id
);

if (
    $verified !== null
    && $verified->status === 'absent'
    && $verified->remarks === 'Changed status'
) {
    echo "Verify Changed Values: PASSED\n";
} else {
    echo "Verify Changed Values: FAILED\n";
}

/*
 * Cleanup.
 */
$repository->delete(
    (int) $attendance->id
);

$cleanup = $repository->find(
    (int) $attendance->id
);

echo $cleanup === null
    ? "Cleanup: PASSED\n"
    : "Cleanup: FAILED\n";

echo PHP_EOL;
echo "ATTENDANCE UNCHANGED UPDATE TEST COMPLETE\n";
