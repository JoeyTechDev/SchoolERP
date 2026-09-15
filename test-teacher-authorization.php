<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\TeacherAssignmentRepository;
use SchoolERP\Repositories\TeacherRepository;
use SchoolERP\Services\TeacherAuthorizationService;
use SchoolERP\Session\SessionInterface;

/*
|--------------------------------------------------------------------------
| Test session
|--------------------------------------------------------------------------
|
| Lightweight in-memory implementation of SessionInterface.
|
*/

final class TestSession implements SessionInterface
{
    /**
     * Session values.
     *
     * @var array<string,mixed>
     */
    private array $data;

    /**
     * Flash values.
     *
     * @var array<string,mixed>
     */
    private array $flashData = [];

    /**
     * Constructor.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function start(): void
    {
    }

    public function isStarted(): bool
    {
        return true;
    }

    public function put(
        string $key,
        mixed $value
    ): void {
        $this->data[$key] = $value;
    }

    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists(
            $key,
            $this->data
        );
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    public function regenerate(): bool
    {
        return true;
    }

    public function destroy(): bool
    {
        $this->data = [];
        $this->flashData = [];

        return true;
    }

    public function flush(): void
    {
        $this->data = [];
    }

    public function flash(
        string $key,
        mixed $value = null
    ): mixed {
        if (func_num_args() === 2) {
            $this->flashData[$key] = $value;

            return null;
        }

        return $this->flashData[$key] ?? null;
    }

    public function hasFlash(string $key): bool
    {
        return array_key_exists(
            $key,
            $this->flashData
        );
    }

    public function clearFlash(): void
    {
        $this->flashData = [];
    }
}

/*
|--------------------------------------------------------------------------
| Test helpers
|--------------------------------------------------------------------------
*/

$total = 0;
$passed = 0;
$failed = 0;

function check(
    string $name,
    bool $condition
): void {
    global $total;
    global $passed;
    global $failed;

    $total++;

    if ($condition) {
        $passed++;
        echo $name . ': PASSED' . PHP_EOL;

        return;
    }

    $failed++;
    echo $name . ': FAILED' . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| Test header
|--------------------------------------------------------------------------
*/

echo "TEACHER AUTHORIZATION TEST\n";
echo "===========================\n\n";

/*
|--------------------------------------------------------------------------
| Repositories
|--------------------------------------------------------------------------
*/

$teacherRepository =
    new TeacherRepository();

$assignmentRepository =
    new TeacherAssignmentRepository();

$studentRepository =
    new StudentRepository();

/*
|--------------------------------------------------------------------------
| Find teacher with a linked user account
|--------------------------------------------------------------------------
*/

$teacherRecords =
    $teacherRepository->allOrdered();

$teacherRecord = null;

foreach (
    $teacherRecords
    as $candidate
) {
    $candidateUserId = (int) (
        $candidate['user_id'] ?? 0
    );

    if ($candidateUserId > 0) {
        $teacherRecord = $candidate;

        break;
    }
}

if ($teacherRecord === null) {
    echo "No teacher profile is linked to a user account."
        . PHP_EOL;
    echo "Authorization test cannot continue."
        . PHP_EOL;

    exit(1);
}

$userId = (int) (
    $teacherRecord['user_id']
);

$teacherId = (int) (
    $teacherRecord['id']
);

check(
    'Teacher/User Link',
    $userId > 0 && $teacherId > 0
);

/*
|--------------------------------------------------------------------------
| Find active assignments for the teacher
|--------------------------------------------------------------------------
*/

$teacherAssignments =
    $assignmentRepository->forTeacher(
        $teacherId,
        true
    );

$activeAssignment = null;

foreach (
    $teacherAssignments
    as $assignment
) {
    if (
        (int) (
            $assignment->is_active
            ?? 0
        ) === 1
    ) {
        $activeAssignment = $assignment;

        break;
    }
}

if ($activeAssignment === null) {
    echo "No active assignment found for this teacher."
        . PHP_EOL;
    echo "Authorization test cannot continue."
        . PHP_EOL;

    exit(1);
}

$classroomId = (int) (
    $activeAssignment->classroom_id
);

$subjectId = (int) (
    $activeAssignment->subject_id
);

check(
    'Active Assignment',
    $classroomId > 0
        && $subjectId > 0
);

/*
|--------------------------------------------------------------------------
| Teacher authorization instance
|--------------------------------------------------------------------------
*/

$teacherSession = new TestSession([
    'user_id' => $userId,
    'role_id' => 2,
]);

$authorization =
    new TeacherAuthorizationService(
        $teacherSession,
        $teacherRepository,
        $assignmentRepository,
        $studentRepository
    );

/*
|--------------------------------------------------------------------------
| Role checks
|--------------------------------------------------------------------------
*/

check(
    'Teacher Role Detection',
    $authorization->isTeacher()
);

check(
    'Teacher Is Not Administrator',
    !$authorization->isAdmin()
);

/*
|--------------------------------------------------------------------------
| Current teacher lookup
|--------------------------------------------------------------------------
*/

$currentTeacher =
    $authorization->currentTeacher();

check(
    'Current Teacher Lookup',
    $currentTeacher !== null
);

check(
    'Current Teacher ID',
    $authorization->currentTeacherId()
        === $teacherId
);

/*
|--------------------------------------------------------------------------
| Assigned classroom
|--------------------------------------------------------------------------
*/

check(
    'Assigned Classroom Access',
    $authorization->canAccessClassroom(
        $classroomId
    )
);

/*
|--------------------------------------------------------------------------
| Invalid classroom
|--------------------------------------------------------------------------
*/

check(
    'Invalid Classroom Protection',
    !$authorization->canAccessClassroom(
        -1
    )
);

/*
|--------------------------------------------------------------------------
| Assigned subject
|--------------------------------------------------------------------------
*/

check(
    'Assigned Subject Access',
    $authorization->canManageSubject(
        $classroomId,
        $subjectId
    )
);

/*
|--------------------------------------------------------------------------
| Invalid subject
|--------------------------------------------------------------------------
*/

check(
    'Invalid Subject Protection',
    !$authorization->canManageSubject(
        $classroomId,
        -1
    )
);

/*
|--------------------------------------------------------------------------
| Unassigned subject
|--------------------------------------------------------------------------
*/

$wrongSubjectId =
    $subjectId + 99999;

check(
    'Unassigned Subject Protection',
    !$authorization->canManageSubject(
        $classroomId,
        $wrongSubjectId
    )
);

/*
|--------------------------------------------------------------------------
| Find students
|--------------------------------------------------------------------------
*/

$studentRecords =
    $studentRepository->allOrdered();

$assignedStudentId = null;
$outsideStudentId = null;

$assignedClassroomIds = [];

foreach (
    $teacherAssignments
    as $assignment
) {
    if (
        (int) (
            $assignment->is_active
            ?? 0
        ) !== 1
    ) {
        continue;
    }

    $assignedClassroomIds[
        (int) (
            $assignment->classroom_id
        )
    ] = true;
}

foreach (
    $studentRecords
    as $student
) {
    $studentId = (int) (
        $student['id'] ?? 0
    );

    $studentClassroomId = (int) (
        $student['classroom_id'] ?? 0
    );

    if (
        $studentId <= 0
        || $studentClassroomId <= 0
    ) {
        continue;
    }

    if (
        $assignedStudentId === null
        && isset(
            $assignedClassroomIds[
                $studentClassroomId
            ]
        )
    ) {
        $assignedStudentId = $studentId;
    }

    if (
        $outsideStudentId === null
        && !isset(
            $assignedClassroomIds[
                $studentClassroomId
            ]
        )
    ) {
        $outsideStudentId = $studentId;
    }
}

/*
|--------------------------------------------------------------------------
| Assigned student
|--------------------------------------------------------------------------
*/

if ($assignedStudentId !== null) {
    check(
        'Assigned Student Access',
        $authorization->canManageStudent(
            $assignedStudentId
        )
    );
} else {
    echo "Assigned Student Access: SKIPPED"
        . " (no matching student found)"
        . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| Student outside teacher's classrooms
|--------------------------------------------------------------------------
*/

if ($outsideStudentId !== null) {
    check(
        'Unassigned Student Protection',
        !$authorization->canManageStudent(
            $outsideStudentId
        )
    );
} else {
    echo "Unassigned Student Protection: SKIPPED"
        . " (no outside student found)"
        . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| Invalid student
|--------------------------------------------------------------------------
*/

check(
    'Invalid Student Protection',
    !$authorization->canManageStudent(
        -1
    )
);

/*
|--------------------------------------------------------------------------
| Non-teacher protection
|--------------------------------------------------------------------------
*/

$studentSession = new TestSession([
    'user_id' => $userId,
    'role_id' => 3,
]);

$studentAuthorization =
    new TeacherAuthorizationService(
        $studentSession,
        $teacherRepository,
        $assignmentRepository,
        $studentRepository
    );

check(
    'Non-Teacher Classroom Protection',
    !$studentAuthorization->canAccessClassroom(
        $classroomId
    )
);

check(
    'Non-Teacher Subject Protection',
    !$studentAuthorization->canManageSubject(
        $classroomId,
        $subjectId
    )
);

check(
    'Non-Teacher Student Protection',
    !$studentAuthorization->canManageStudent(
        $assignedStudentId ?? -1
    )
);

/*
|--------------------------------------------------------------------------
| Teacher without linked profile
|--------------------------------------------------------------------------
*/

$unlinkedTeacherSession = new TestSession([
    'user_id' => 999999,
    'role_id' => 2,
]);

$unlinkedTeacherAuthorization =
    new TeacherAuthorizationService(
        $unlinkedTeacherSession,
        $teacherRepository,
        $assignmentRepository,
        $studentRepository
    );

check(
    'Unlinked Teacher Profile Protection',
    $unlinkedTeacherAuthorization->currentTeacher()
        === null
);

check(
    'Unlinked Teacher Classroom Protection',
    !$unlinkedTeacherAuthorization->canAccessClassroom(
        $classroomId
    )
);

check(
    'Unlinked Teacher Subject Protection',
    !$unlinkedTeacherAuthorization->canManageSubject(
        $classroomId,
        $subjectId
    )
);

/*
|--------------------------------------------------------------------------
| Administrator bypass
|--------------------------------------------------------------------------
*/

$adminSession = new TestSession([
    'user_id' => $userId,
    'role_id' => 1,
]);

$adminAuthorization =
    new TeacherAuthorizationService(
        $adminSession,
        $teacherRepository,
        $assignmentRepository,
        $studentRepository
    );

check(
    'Administrator Role Detection',
    $adminAuthorization->isAdmin()
);

check(
    'Administrator Classroom Bypass',
    $adminAuthorization->canAccessClassroom(
        999999
    )
);

check(
    'Administrator Subject Bypass',
    $adminAuthorization->canManageSubject(
        999999,
        999999
    )
);

if ($assignedStudentId !== null) {
    check(
        'Administrator Student Bypass',
        $adminAuthorization->canManageStudent(
            $assignedStudentId
        )
    );
}

/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

echo PHP_EOL;
echo "===========================\n";
echo "TOTAL:  {$total}\n";
echo "PASSED: {$passed}\n";
echo "FAILED: {$failed}\n";
echo "===========================\n";

if ($failed > 0) {
    echo "TEACHER AUTHORIZATION TEST FAILED\n";

    exit(1);
}

echo "TEACHER AUTHORIZATION TEST PASSED\n";