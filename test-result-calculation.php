<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Services\GradeService;
use SchoolERP\Services\ResultCalculationService;

$calculator = new ResultCalculationService();
$grader = new GradeService();

echo "RESULT CALCULATION TEST\n";
echo "=======================\n\n";

$testCases = [
    [30, 70],
    [25, 65],
    [20, 45],
    [15, 40],
    [12, 30],
    [10, 20],
];

foreach ($testCases as [$ca, $exam]) {
    $total = $calculator->total(
        $ca,
        $exam
    );

    echo "CA: {$ca}";
    echo " | Exam: {$exam}";
    echo " | Total: {$total}";
    echo " | Grade: ";
    echo $grader->grade($total);
    echo " | Remark: ";
    echo $grader->remark($total);
    echo PHP_EOL;
}

echo PHP_EOL;

echo "Boundary Tests\n";
echo "--------------\n";

$boundaries = [
    100,
    75,
    74,
    65,
    64,
    55,
    54,
    45,
    44,
    40,
    39,
    0,
];

foreach ($boundaries as $score) {
    echo sprintf(
        "%d => %s (%s)%s",
        $score,
        $grader->grade($score),
        $grader->remark($score),
        PHP_EOL
    );
}

echo PHP_EOL;
echo "Invalid Score Test\n";
echo "------------------\n";

try {
    $calculator->total(
        31,
        70
    );

    echo "CA Validation: FAILED\n";
} catch (\InvalidArgumentException $exception) {
    echo "CA Validation: PASSED\n";
}

try {
    $calculator->total(
        30,
        71
    );

    echo "Exam Validation: FAILED\n";
} catch (\InvalidArgumentException $exception) {
    echo "Exam Validation: PASSED\n";
}

echo PHP_EOL;
echo "RESULT CALCULATION TEST COMPLETE\n";