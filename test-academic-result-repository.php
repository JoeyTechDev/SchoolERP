<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\AcademicResultRepository;

$repository = new AcademicResultRepository();

echo "ACADEMIC RESULT REPOSITORY TEST\n";
echo "===============================\n\n";

/*
 * Existing migrated result:
 * Student 1
 * Subject 1 = Mathematics
 * Session 1 = 2026/2027
 * Term 1 = First Term
 */
$result = $repository->findForStudent(
    1,
    1,
    1,
    1
);

if ($result === null) {
    echo "Find Student Result: FAILED\n";
} else {
    echo "Find Student Result: PASSED\n";
    echo 'Result ID: ' . $result->id . PHP_EOL;
    echo 'Student ID: ' . $result->student_id . PHP_EOL;
    echo 'Subject ID: ' . $result->subject_id . PHP_EOL;
    echo 'Session ID: ' . $result->academic_session_id . PHP_EOL;
    echo 'Term ID: ' . $result->term_id . PHP_EOL;
    echo 'Total Score: ' . $result->total_score . PHP_EOL;
}

echo PHP_EOL;

echo "Student Results\n";
echo "---------------\n";

$results = $repository->forStudent(
    1,
    1,
    1
);

echo 'Count: ' . count($results) . PHP_EOL;

foreach ($results as $item) {
    echo sprintf(
        "Subject ID: %d | Total: %s%s",
        (int) $item['subject_id'],
        (string) (
            $item['total_score'] ?? 'NULL'
        ),
        PHP_EOL
    );
}

echo PHP_EOL;

echo "Subject Results\n";
echo "---------------\n";

$subjectResults = $repository->forSubject(
    1,
    1,
    1
);

echo 'Count: ' . count($subjectResults) . PHP_EOL;

foreach ($subjectResults as $item) {
    echo sprintf(
        "Student ID: %d | Total: %s%s",
        (int) $item['student_id'],
        (string) (
            $item['total_score'] ?? 'NULL'
        ),
        PHP_EOL
    );
}

echo PHP_EOL;

echo "Missing Result Test\n";
echo "-------------------\n";

$missing = $repository->findForStudent(
    999999,
    1,
    1,
    1
);

echo $missing === null
    ? "Missing Result: PASSED\n"
    : "Missing Result: FAILED\n";

echo PHP_EOL;
echo "ACADEMIC RESULT REPOSITORY TEST COMPLETE\n";