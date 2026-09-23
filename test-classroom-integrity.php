<?php

declare(strict_types=1);

use SchoolERP\Models\Classroom;
use SchoolERP\Models\Student;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Repositories\StudentRepository;

require __DIR__ . '/vendor/autoload.php';

echo "CLASSROOM INTEGRITY TEST\n";
echo "========================\n\n";

$passed = 0;
$failed = 0;

function assertTest(
    string $description,
    bool $condition
): void {
    global $passed, $failed;

    if ($condition) {
        $passed++;
        echo "PASSED: {$description}\n";
        return;
    }

    $failed++;
    echo "FAILED: {$description}\n";
}

function uniqueClassroomName(string $prefix): string
{
    return $prefix . ' ' . date('YmdHis') . '-' . random_int(1000, 9999);
}

function uniqueAdmissionNumber(): string
{
    return 'TEST-' . date('YmdHis') . '-' . random_int(1000, 9999);
}

$classroomRepository = new ClassroomRepository();
$studentRepository = new StudentRepository();

$assignedClassroomId = null;
$emptyClassroomId = null;
$studentId = null;

try {
    /*
     * ---------------------------------------------------------------
     * 1. Create a classroom that will have a student assigned to it.
     *
     * Model::create() returns the inserted ID, not a model instance.
     * ---------------------------------------------------------------
     */
    $assignedClassroomId = $classroomRepository->create([
        'name' => uniqueClassroomName('Integrity Assigned'),
    ]);

    assertTest(
        'Assigned test classroom was created',
        is_int($assignedClassroomId) && $assignedClassroomId > 0
    );

    /*
     * ---------------------------------------------------------------
     * 2. Retrieve the created classroom as a model.
     * ---------------------------------------------------------------
     */
    $assignedClassroom = null;

    if (
        is_int($assignedClassroomId)
        && $assignedClassroomId > 0
    ) {
        $assignedClassroom = $classroomRepository->find(
            $assignedClassroomId
        );
    }

    assertTest(
        'Assigned test classroom can be retrieved as a model',
        $assignedClassroom instanceof Classroom
    );

    /*
     * ---------------------------------------------------------------
     * 3. Create a student assigned to that classroom.
     * ---------------------------------------------------------------
     */
    if ($assignedClassroom instanceof Classroom) {
        $studentId = $studentRepository->create([
            'admission_number' => uniqueAdmissionNumber(),
            'first_name' => 'Integrity',
            'last_name' => 'Test',
            'date_of_birth' => '2010-01-01',
            'gender' => 'Male',
            'classroom_id' => $assignedClassroomId,
        ]);
    }

    assertTest(
        'Assigned test student was created',
        is_int($studentId) && $studentId > 0
    );

    /*
     * ---------------------------------------------------------------
     * 4. Verify hasStudents() detects the assigned student.
     * ---------------------------------------------------------------
     */
    $hasStudents = false;

    if (is_int($assignedClassroomId)) {
        $hasStudents = $classroomRepository->hasStudents(
            $assignedClassroomId
        );
    }

    assertTest(
        'Classroom with assigned student reports hasStudents() = true',
        $hasStudents === true
    );

    /*
     * ---------------------------------------------------------------
     * 5. Attempt to delete the classroom while the student remains.
     * ---------------------------------------------------------------
     */
    $deleteAssignedResult = false;

    if (is_int($assignedClassroomId)) {
        $deleteAssignedResult = $classroomRepository->delete(
            $assignedClassroomId
        );
    }

    assertTest(
        'Classroom with assigned students cannot be deleted',
        $deleteAssignedResult === false
    );

    /*
     * ---------------------------------------------------------------
     * 6. Confirm the classroom still exists.
     * ---------------------------------------------------------------
     */
    $assignedClassroomStillExists = false;

    if (is_int($assignedClassroomId)) {
        $remainingClassroom = $classroomRepository->find(
            $assignedClassroomId
        );

        $assignedClassroomStillExists =
            $remainingClassroom instanceof Classroom;
    }

    assertTest(
        'Protected classroom still exists after failed deletion',
        $assignedClassroomStillExists === true
    );

    /*
     * ---------------------------------------------------------------
     * 7. Confirm the student is still assigned to the classroom.
     * ---------------------------------------------------------------
     */
    $studentStillAssigned = false;

    if (
        is_int($studentId)
        && $studentId > 0
        && is_int($assignedClassroomId)
    ) {
        $currentStudent = $studentRepository->find($studentId);

        $studentStillAssigned =
            $currentStudent instanceof Student
            && (int) $currentStudent->classroom_id
                === $assignedClassroomId;
    }

    assertTest(
        'Student remains assigned after protected classroom deletion attempt',
        $studentStillAssigned === true
    );

    /*
     * ---------------------------------------------------------------
     * 8. Create a second classroom with no students.
     * ---------------------------------------------------------------
     */
    $emptyClassroomId = $classroomRepository->create([
        'name' => uniqueClassroomName('Integrity Empty'),
    ]);

    assertTest(
        'Empty test classroom was created',
        is_int($emptyClassroomId) && $emptyClassroomId > 0
    );

    /*
     * ---------------------------------------------------------------
     * 9. Verify the empty classroom can be retrieved.
     * ---------------------------------------------------------------
     */
    $emptyClassroom = null;

    if (
        is_int($emptyClassroomId)
        && $emptyClassroomId > 0
    ) {
        $emptyClassroom = $classroomRepository->find(
            $emptyClassroomId
        );
    }

    assertTest(
        'Empty test classroom can be retrieved as a model',
        $emptyClassroom instanceof Classroom
    );

    /*
     * ---------------------------------------------------------------
     * 10. Verify hasStudents() returns false.
     * ---------------------------------------------------------------
     */
    $emptyHasStudents = true;

    if (is_int($emptyClassroomId)) {
        $emptyHasStudents = $classroomRepository->hasStudents(
            $emptyClassroomId
        );
    }

    assertTest(
        'Empty classroom reports hasStudents() = false',
        $emptyHasStudents === false
    );

    /*
     * ---------------------------------------------------------------
     * 11. Verify an empty classroom can be deleted.
     * ---------------------------------------------------------------
     */
    $deleteEmptyResult = false;

    if (is_int($emptyClassroomId)) {
        $deleteEmptyResult = $classroomRepository->delete(
            $emptyClassroomId
        );
    }

    assertTest(
        'Empty classroom can be deleted',
        $deleteEmptyResult === true
    );

    /*
     * ---------------------------------------------------------------
     * 12. Confirm the empty classroom no longer exists.
     * ---------------------------------------------------------------
     */
    $emptyClassroomRemoved = true;

    if (is_int($emptyClassroomId)) {
        $remainingEmptyClassroom = $classroomRepository->find(
            $emptyClassroomId
        );

        $emptyClassroomRemoved =
            $remainingEmptyClassroom === null;
    }

    assertTest(
        'Deleted empty classroom no longer exists',
        $emptyClassroomRemoved === true
    );

    /*
     * ---------------------------------------------------------------
     * 13. Nonexistent classroom handling.
     * ---------------------------------------------------------------
     */
    $nonexistentId = 999999999;

    assertTest(
        'hasStudents() returns false for nonexistent classroom',
        $classroomRepository->hasStudents($nonexistentId) === false
    );

    assertTest(
        'delete() returns false for nonexistent classroom',
        $classroomRepository->delete($nonexistentId) === false
    );
} finally {
    /*
     * ---------------------------------------------------------------
     * Cleanup
     *
     * Delete the temporary student first because the classroom is
     * intentionally protected while that student is assigned.
     * ---------------------------------------------------------------
     */
    if (is_int($studentId) && $studentId > 0) {
        $student = $studentRepository->find($studentId);

        if ($student instanceof Student) {
            $student->delete();
        }
    }

    /*
     * The assigned classroom can now be deleted because its temporary
     * student has been removed.
     */
    if (
        is_int($assignedClassroomId)
        && $assignedClassroomId > 0
    ) {
        $classroomRepository->delete($assignedClassroomId);
    }

    /*
     * Defensive cleanup for the empty classroom.
     */
    if (
        is_int($emptyClassroomId)
        && $emptyClassroomId > 0
    ) {
        $classroomRepository->delete($emptyClassroomId);
    }
}

echo "\n";
echo "PASSED: {$passed}\n";
echo "FAILED: {$failed}\n";

if ($failed === 0) {
    echo "CLASSROOM INTEGRITY TEST PASSED\n";
    exit(0);
}

echo "CLASSROOM INTEGRITY TEST FAILED\n";
exit(1);
