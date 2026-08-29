<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\AttendanceRepository;

$repository = new AttendanceRepository();

/*
 * Use a dedicated test date so we do not interfere
 * with normal classroom attendance records.
 */
$studentId = 1;
$sessionId = 1;
$termId = 1;
$testDate = '2099-01-15';

echo "ATTENDANCE INTEGRITY TEST\n";
echo "=========================\n\n";

/*
 * Clean up any previous test record.
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
 * 1. Create a record.
 */
$repository->create([
    'student_id' => $studentId,
    'academic_session_id' => $sessionId,
    'term_id' => $termId,
    'attendance_date' => $testDate,
    'status' => 'present',
    'remarks' => 'Integrity test',
]);

$record = $repository->findForStudentDate(
    $studentId,
    $testDate,
    $sessionId,
    $termId
);

echo $record !== null
    ? "Create Record: PASSED\n"
    : "Create Record: FAILED\n";

if ($record === null) {
    exit(1);
}

$recordId = (int) $record->id;

echo 'Record ID: ' . $recordId . PHP_EOL;

/*
 * 2. Update the existing record.
 */
$updated = $repository->updateAttendance(
    $recordId,
    [
        'status' => 'late',
        'remarks' => 'Updated integrity test',
    ]
);

echo $updated
    ? "Update Existing Record: PASSED\n"
    : "Update Existing Record: FAILED\n";

/*
 * 3. Confirm only one record exists for the
 *    student/date/session/term combination.
 */
$matching = $repository->forDate(
    $testDate,
    $sessionId,
    $termId
);

$studentMatches = array_filter(
    $matching,
    static function (array $item) use ($studentId): bool {
        return (int) (
            $item['student_id'] ?? 0
        ) === $studentId;
    }
);

echo count($studentMatches) === 1
    ? "Unique Daily Record: PASSED\n"
    : "Unique Daily Record: FAILED\n";

/*
 * 4. Verify updated values.
 */
$verified = $repository->find(
    $recordId
);

if (
    $verified !== null
    && $verified->status === 'late'
    && $verified->remarks === 'Updated integrity test'
) {
    echo "Verify Updated Values: PASSED\n";
} else {
    echo "Verify Updated Values: FAILED\n";
}

/*
 * 5. Verify the database unique constraint by
 *    attempting a direct duplicate insert.
 *
 * The application normally avoids this by finding
 * the existing record first, but the database must
 * also protect itself.
 */
$duplicateRejected = false;

try {
    $repository->create([
        'student_id' => $studentId,
        'academic_session_id' => $sessionId,
        'term_id' => $termId,
        'attendance_date' => $testDate,
        'status' => 'absent',
        'remarks' => 'Duplicate test',
    ]);
} catch (PDOException $exception) {
    $duplicateRejected =
        $exception->getCode() === '23000'
        || str_contains(
            strtolower(
                $exception->getMessage()
            ),
            'duplicate'
        );
}

echo $duplicateRejected
    ? "Duplicate Protection: PASSED\n"
    : "Duplicate Protection: FAILED\n";

/*
 * 6. Delete the test record.
 */
$deleted = $repository->delete(
    $recordId
);

echo $deleted
    ? "Cleanup Delete: PASSED\n"
    : "Cleanup Delete: FAILED\n";

/*
 * 7. Confirm cleanup.
 */
$cleanupCheck = $repository->find(
    $recordId
);

echo $cleanupCheck === null
    ? "Cleanup Verification: PASSED\n"
    : "Cleanup Verification: FAILED\n";

echo PHP_EOL;
echo "ATTENDANCE INTEGRITY TEST COMPLETE\n";