<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Models\Classroom;
use SchoolERP\Models\Subject;
use SchoolERP\Models\Teacher;
use SchoolERP\Models\TeacherAssignment;

echo "TEACHER MODEL RELATIONSHIP TEST\n";
echo "===============================\n\n";

/*
|--------------------------------------------------------------------------
| Teacher
|--------------------------------------------------------------------------
*/

$teacher = new Teacher();

$teacher->fill([
    'id' => 1,
    'employee_number' => 'TEST-001',
    'first_name' => 'Test',
    'last_name' => 'Teacher',
    'employment_status' => 'active',
]);

echo $teacher->assignments() instanceof \SchoolERP\ORM\Relations\HasMany
    ? "Teacher HasMany Assignments: PASSED\n"
    : "Teacher HasMany Assignments: FAILED\n";

/*
|--------------------------------------------------------------------------
| Classroom
|--------------------------------------------------------------------------
*/

$classroom = new Classroom();

$classroom->fill([
    'id' => 1,
    'name' => 'JSS 1A',
]);

echo $classroom->teacherAssignments()
    instanceof \SchoolERP\ORM\Relations\HasMany
    ? "Classroom HasMany Teacher Assignments: PASSED\n"
    : "Classroom HasMany Teacher Assignments: FAILED\n";

/*
|--------------------------------------------------------------------------
| Subject
|--------------------------------------------------------------------------
*/

$subject = new Subject();

$subject->fill([
    'id' => 1,
    'name' => 'Mathematics',
    'code' => 'MATH',
    'status' => 'active',
]);

echo $subject->teacherAssignments()
    instanceof \SchoolERP\ORM\Relations\HasMany
    ? "Subject HasMany Teacher Assignments: PASSED\n"
    : "Subject HasMany Teacher Assignments: FAILED\n";

/*
|--------------------------------------------------------------------------
| Assignment relationships
|--------------------------------------------------------------------------
*/

$assignment = new TeacherAssignment();

$assignment->fill([
    'id' => 1,
    'teacher_id' => 1,
    'classroom_id' => 1,
    'subject_id' => 1,
    'is_active' => 1,
]);

echo $assignment->teacher()
    instanceof \SchoolERP\ORM\Relations\BelongsTo
    ? "Assignment BelongsTo Teacher: PASSED\n"
    : "Assignment BelongsTo Teacher: FAILED\n";

echo $assignment->classroom()
    instanceof \SchoolERP\ORM\Relations\BelongsTo
    ? "Assignment BelongsTo Classroom: PASSED\n"
    : "Assignment BelongsTo Classroom: FAILED\n";

echo $assignment->subject()
    instanceof \SchoolERP\ORM\Relations\BelongsTo
    ? "Assignment BelongsTo Subject: PASSED\n"
    : "Assignment BelongsTo Subject: FAILED\n";

echo PHP_EOL;
echo "TEACHER MODEL RELATIONSHIP TEST COMPLETE\n";
