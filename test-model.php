<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Models\Student;

echo "MODEL TEST\n";
echo "===========\n\n";

function test(
    string $name,
    bool $result
): void {
    echo $name . ': ' . (
        $result ? 'PASSED' : 'FAILED'
    ) . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| Create model
|--------------------------------------------------------------------------
*/

$student = new Student();

test(
    'Model Instance Test',
    $student instanceof Student
);

/*
|--------------------------------------------------------------------------
| Table
|--------------------------------------------------------------------------
*/

test(
    'Table Configuration Test',
    $student->getQuery()->getTable() === 'students'
);

/*
|--------------------------------------------------------------------------
| Fill Attributes
|--------------------------------------------------------------------------
*/

$student->fill([
    'id' => 1,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'classroom_id' => 2,
]);

test(
    'Fill Attributes Test',
    $student->first_name === 'John'
    && $student->last_name === 'Doe'
    && $student->classroom_id === 2
);

/*
|--------------------------------------------------------------------------
| Attribute Casting
|--------------------------------------------------------------------------
*/

test(
    'Integer Cast Test',
    is_int($student->id)
    && is_int($student->classroom_id)
);

/*
|--------------------------------------------------------------------------
| Attributes Array
|--------------------------------------------------------------------------
*/

$attributes = $student->attributes();

test(
    'Attributes Array Test',
    isset($attributes['first_name'])
    && $attributes['first_name'] === 'John'
);

/*
|--------------------------------------------------------------------------
| JSON Serialization
|--------------------------------------------------------------------------
*/

$json = json_encode($student);

test(
    'JSON Serialization Test',
    $json !== false
);

/*
|--------------------------------------------------------------------------
| Dirty State
|--------------------------------------------------------------------------
*/

$cleanStudent = new Student();

$cleanStudent->fill([
    'id' => 1,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'classroom_id' => 2,
]);

test(
    'Clean Model Test',
    $cleanStudent->isDirty() === false
);

/*
|--------------------------------------------------------------------------
| Change Attribute
|--------------------------------------------------------------------------
*/

$cleanStudent->first_name = 'Jane';

test(
    'Dirty Model Test',
    $cleanStudent->isDirty()
);

/*
|--------------------------------------------------------------------------
| Get Dirty Attributes
|--------------------------------------------------------------------------
*/

$dirty = $cleanStudent->getDirty();

test(
    'Get Dirty Attributes Test',
    isset($dirty['first_name'])
    && $dirty['first_name'] === 'Jane'
);

/*
|--------------------------------------------------------------------------
| Fillable Protection
|--------------------------------------------------------------------------
*/

$fillableStudent = new Student();

$id = $fillableStudent->create([
    'first_name' => 'Test',
    'last_name' => 'Student',
    'classroom_id' => 1,
    'id' => 999999,
]);

test(
    'Fillable Protection Test',
    $id > 0
);

echo PHP_EOL;
echo "MODEL TEST COMPLETE\n";