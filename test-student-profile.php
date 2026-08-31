<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Models\Student;

echo "STUDENT PROFILE MODEL TEST\n";
echo "==========================\n\n";

/*
 * Build a test model without saving it.
 */
$student = new Student();

$student->fill([
    'admission_number' => 'TEST-2099-001',
    'first_name' => 'Test',
    'last_name' => 'Student',
    'date_of_birth' => '2010-05-15',
    'gender' => 'male',
    'classroom_id' => 1,
]);

/*
 * Verify values are accessible.
 */
echo $student->admission_number === 'TEST-2099-001'
    ? "Admission Number: PASSED\n"
    : "Admission Number: FAILED\n";

echo $student->first_name === 'Test'
    ? "First Name: PASSED\n"
    : "First Name: FAILED\n";

echo $student->last_name === 'Student'
    ? "Last Name: PASSED\n"
    : "Last Name: FAILED\n";

echo $student->gender === 'male'
    ? "Gender: PASSED\n"
    : "Gender: FAILED\n";

echo (int) $student->classroom_id === 1
    ? "Classroom ID: PASSED\n"
    : "Classroom ID: FAILED\n";

/*
 * Date cast check.
 */
$date = $student->date_of_birth;

if (
    $date !== null
    && (
        is_object($date)
        || is_string($date)
    )
) {
    echo "Date of Birth: PASSED\n";
} else {
    echo "Date of Birth: FAILED\n";
}

echo PHP_EOL;
echo "STUDENT PROFILE MODEL TEST COMPLETE\n";