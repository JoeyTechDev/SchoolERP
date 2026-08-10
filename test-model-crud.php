<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Models\Student;

echo "MODEL CRUD TEST\n";
echo "===============\n\n";

function testCrud(
    string $name,
    bool $result
): void {
    echo $name . ': ' . (
        $result ? 'PASSED' : 'FAILED'
    ) . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
|
| Create a temporary student record.
|
*/

$studentModel = new Student();

$studentId = $studentModel->create([
    'first_name' => 'CRUD',
    'last_name' => 'Test',
]);

testCrud(
    'Create Test',
    $studentId > 0
);

/*
|--------------------------------------------------------------------------
| Find
|--------------------------------------------------------------------------
*/

$student = $studentModel->find($studentId);

testCrud(
    'Find Test',
    $student !== null
    && $student->first_name === 'CRUD'
    && $student->last_name === 'Test'
);

/*
|--------------------------------------------------------------------------
| Where
|--------------------------------------------------------------------------
*/

$found = (new Student())
    ->where(
        'id',
        '=',
        $studentId
    )
    ->first();

testCrud(
    'Where Test',
    $found !== null
    && $found->id === $studentId
);

/*
|--------------------------------------------------------------------------
| Exists
|--------------------------------------------------------------------------
*/

$exists = (new Student())
    ->where(
        'id',
        '=',
        $studentId
    )
    ->exists();

testCrud(
    'Exists Test',
    $exists === true
);

/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

$updated = (new Student())
    ->where(
        'id',
        '=',
        $studentId
    )
    ->update([
        'first_name' => 'Updated',
    ]);

testCrud(
    'Update Test',
    $updated > 0
);

/*
|--------------------------------------------------------------------------
| Verify Update
|--------------------------------------------------------------------------
*/

$updatedStudent = (new Student())
    ->find($studentId);

testCrud(
    'Verify Update Test',
    $updatedStudent !== null
    && $updatedStudent->first_name === 'Updated'
);

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

if ($updatedStudent !== null) {
    $updatedStudent->last_name = 'Saved';

    $saved = $updatedStudent->save();

    testCrud(
        'Save Test',
        $saved === true
    );
} else {
    testCrud(
        'Save Test',
        false
    );
}

/*
|--------------------------------------------------------------------------
| Verify Save
|--------------------------------------------------------------------------
*/

$savedStudent = (new Student())
    ->find($studentId);

testCrud(
    'Verify Save Test',
    $savedStudent !== null
    && $savedStudent->last_name === 'Saved'
);

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

if ($savedStudent !== null) {
    $deleted = $savedStudent->delete();

    testCrud(
        'Delete Test',
        $deleted === true
    );
} else {
    testCrud(
        'Delete Test',
        false
    );
}

/*
|--------------------------------------------------------------------------
| Verify Delete
|--------------------------------------------------------------------------
*/

$deletedStudent = (new Student())
    ->find($studentId);

testCrud(
    'Verify Delete Test',
    $deletedStudent === null
);

echo PHP_EOL;
echo "MODEL CRUD TEST COMPLETE\n";

