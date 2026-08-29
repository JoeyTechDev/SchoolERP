<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\AttendanceRepository;

$repository = new AttendanceRepository();

$studentId = 1;
$sessionId = 1;
$termId = 1;

echo "ATTENDANCE SUMMARY TEST\n";
echo "=======================\n\n";

/*
 * Use dedicated test dates.
 */
$testDates = [
    '2099-02-01',
    '2099-02-02',
    '2099-02-03',
    '2099-02-04',
    '2099-02-05',
];

$statuses = [
    'present',
    'present',
    'late',
    'absent',
    'excused',
];

/*
 * Remove previous test records.
 */
foreach ($testDates as $date) {
    $existing = $repository->findForStudentDate(
        $studentId,
        $date,
        $sessionId,
        $termId
    );

    if ($existing !== null) {
        $repository->delete(
            (int) $existing->id
        );
    }
}

/*
 * Create controlled test data:
 *
 * Present = 2
 * Late    = 1
 * Absent  = 1
 * Excused = 1
 * Total   = 5
 *
 * Attendance rate:
 *
 * (2 + 1) / 5 * 100 = 60%
 */
foreach ($testDates as $index => $date) {
    $repository->create([
        'student_id' => $studentId,
        'academic_session_id' => $sessionId,
        'term_id' => $termId,
        'attendance_date' => $date,
        'status' => $statuses[$index],
        'remarks' => 'Summary test',
    ]);
}

echo "Test Records: CREATED\n\n";

/*
 * Generate summary.
 */
$summary = $repository->summaryForStudent(
    $studentId,
    $sessionId,
    $termId
);

echo 'Total Days: '
    . $summary['total_days']
    . PHP_EOL;

echo 'Present: '
    . $summary['present']
    . PHP_EOL;

echo 'Absent: '
    . $summary['absent']
    . PHP_EOL;

echo 'Late: '
    . $summary['late']
    . PHP_EOL;

echo 'Excused: '
    . $summary['excused']
    . PHP_EOL;

echo 'Attendance Rate: '
    . number_format(
        $summary['attendance_rate'],
        2
    )
    . '%'
    . PHP_EOL;

echo PHP_EOL;

/*
 * Validate values.
 */
echo "Validation\n";
echo "----------\n";

echo $summary['total_days'] === 5
    ? "Total Days: PASSED\n"
    : "Total Days: FAILED\n";

echo $summary['present'] === 2
    ? "Present Count: PASSED\n"
    : "Present Count: FAILED\n";

echo $summary['absent'] === 1
    ? "Absent Count: PASSED\n"
    : "Absent Count: FAILED\n";

echo $summary['late'] === 1
    ? "Late Count: PASSED\n"
    : "Late Count: FAILED\n";

echo $summary['excused'] === 1
    ? "Excused Count: PASSED\n"
    : "Excused Count: FAILED\n";

echo $summary['attendance_rate'] === 60.0
    ? "Attendance Rate: PASSED\n"
    : "Attendance Rate: FAILED\n";

/*
 * Cleanup.
 */
foreach ($testDates as $date) {
    $existing = $repository->findForStudentDate(
        $studentId,
        $date,
        $sessionId,
        $termId
    );

    if ($existing !== null) {
        $repository->delete(
            (int) $existing->id
        );
    }
}

echo PHP_EOL;

echo "Cleanup: PASSED\n";

echo PHP_EOL;
echo "ATTENDANCE SUMMARY TEST COMPLETE\n";