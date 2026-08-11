<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Models\Classroom;
use SchoolERP\Models\Student;

function assertTest(
    string $name,
    bool $condition
): void {
    echo $name . ': '
        . ($condition ? 'PASSED' : 'FAILED')
        . PHP_EOL;

    if (!$condition) {
        throw new RuntimeException(
            "Test failed: {$name}"
        );
    }
}

echo "MODEL RELATIONSHIP TEST\n";
echo "=======================\n\n";

/*
|--------------------------------------------------------------------------
| Create test data
|--------------------------------------------------------------------------
*/

$classroom = new Classroom();

$classroomId = $classroom->create([
    'name' => 'Relationship Test Classroom',
]);

assertTest(
    'Classroom Create Test',
    $classroomId > 0
);

$student = new Student();

$studentId = $student->create([
    'first_name' => 'Relationship',
    'last_name' => 'Student',
    'classroom_id' => $classroomId,
]);

assertTest(
    'Student Create Test',
    $studentId > 0
);

/*
|--------------------------------------------------------------------------
| Find Student
|--------------------------------------------------------------------------
*/

$foundStudent = $student->find($studentId);

assertTest(
    'Student Find Test',
    $foundStudent instanceof Student
);

/*
|--------------------------------------------------------------------------
| BelongsTo
|--------------------------------------------------------------------------
*/

$studentClassroom = $foundStudent
    ->classroom()
    ->get();

assertTest(
    'BelongsTo Returns Classroom',
    $studentClassroom instanceof Classroom
);

assertTest(
    'BelongsTo Classroom ID Test',
    $studentClassroom !== null
    && $studentClassroom->id === $classroomId
);

/*
|--------------------------------------------------------------------------
| Find Classroom
|--------------------------------------------------------------------------
*/

$foundClassroom = $classroom->find($classroomId);

assertTest(
    'Classroom Find Test',
    $foundClassroom instanceof Classroom
);

/*
|--------------------------------------------------------------------------
| HasMany
|--------------------------------------------------------------------------
*/

$students = $foundClassroom
    ->students()
    ->get();

assertTest(
    'HasMany Returns Array',
    is_array($students)
);

assertTest(
    'HasMany Contains Student',
    count($students) >= 1
);

assertTest(
    'HasMany Returns Student Model',
    isset($students[0])
    && $students[0] instanceof Student
);

assertTest(
    'HasMany Student ID Test',
    isset($students[0])
    && $students[0]->id === $studentId
);

/*
|--------------------------------------------------------------------------
| Missing BelongsTo
|--------------------------------------------------------------------------
*/

$missingStudent = new Student();

$missingStudent->fill([
    'id' => 999999,
    'first_name' => 'Missing',
    'last_name' => 'Classroom',
    'classroom_id' => 999999,
]);

$missingClassroom = $missingStudent
    ->classroom()
    ->get();

assertTest(
    'Missing BelongsTo Returns Null',
    $missingClassroom === null
);

/*
|--------------------------------------------------------------------------
| Missing HasMany
|--------------------------------------------------------------------------
*/

$emptyClassroom = new Classroom();

$emptyClassroom->fill([
    'id' => 999999,
    'name' => 'Empty Classroom',
]);

$emptyStudents = $emptyClassroom
    ->students()
    ->get();

assertTest(
    'Empty HasMany Returns Array',
    is_array($emptyStudents)
);

assertTest(
    'Empty HasMany Returns Empty',
    $emptyStudents === []
);

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

$student->find($studentId)?->delete();
$classroom->find($classroomId)?->delete();

echo PHP_EOL;
echo "MODEL RELATIONSHIP TEST COMPLETE\n";
