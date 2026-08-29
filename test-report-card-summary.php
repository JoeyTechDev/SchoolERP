<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\ReportCardSummaryRepository;

$repository = new ReportCardSummaryRepository();

$studentId = 1;
$sessionId = 1;
$termId = 1;

echo "REPORT CARD SUMMARY TEST\n";
echo "========================\n\n";

/*
 * Clean up an existing test record.
 */
$existing = $repository->findForStudent(
    $studentId,
    $sessionId,
    $termId
);

if ($existing !== null) {
    $existing->delete();
}

/*
 * Create summary.
 */
$summary = $repository->saveForStudent(
    $studentId,
    $sessionId,
    $termId,
    [
        'class_teacher_remark' =>
            'Very hardworking and consistent student.',

        'principal_remark' =>
            'Excellent performance. Keep improving.',

        'promotion_status' =>
            'pending',

        'class_teacher_id' =>
            null,

        'principal_id' =>
            null,
    ]
);

if ($summary->id > 0) {
    echo "Create Summary: PASSED\n";
} else {
    echo "Create Summary: FAILED\n";
}

/*
 * Find summary.
 */
$found = $repository->findForStudent(
    $studentId,
    $sessionId,
    $termId
);

if ($found !== null) {
    echo "Find Summary: PASSED\n";
} else {
    echo "Find Summary: FAILED\n";
    exit(1);
}

/*
 * Verify remarks.
 */
if (
    $found->class_teacher_remark
    === 'Very hardworking and consistent student.'
) {
    echo "Teacher Remark: PASSED\n";
} else {
    echo "Teacher Remark: FAILED\n";
}

if (
    $found->principal_remark
    === 'Excellent performance. Keep improving.'
) {
    echo "Principal Remark: PASSED\n";
} else {
    echo "Principal Remark: FAILED\n";
}

/*
 * Update promotion status.
 */
$updated = $repository->saveForStudent(
    $studentId,
    $sessionId,
    $termId,
    [
        'promotion_status' =>
            'promoted',
    ]
);

if (
    $updated->promotion_status
    === 'promoted'
) {
    echo "Update Promotion Status: PASSED\n";
} else {
    echo "Update Promotion Status: FAILED\n";
}

/*
 * Verify there is still exactly one summary.
 */
$verified = $repository->findForStudent(
    $studentId,
    $sessionId,
    $termId
);

echo $verified !== null
    ? "Verify Summary: PASSED\n"
    : "Verify Summary: FAILED\n";

/*
 * Clean up.
 */
if ($verified !== null) {
    $verified->delete();
}

$cleanup = $repository->findForStudent(
    $studentId,
    $sessionId,
    $termId
);

echo $cleanup === null
    ? "Cleanup: PASSED\n"
    : "Cleanup: FAILED\n";

echo PHP_EOL;
echo "REPORT CARD SUMMARY TEST COMPLETE\n";
