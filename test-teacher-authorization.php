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
| Implements the complete SessionInterface.
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
    public function __construct(
        array $data = []
    ) {
        $this->data = $data;
    }

    /**
     * Start the test session.
     */
    public function start(): void
    {
    }

    /**
     * Determine whether the test session is started.
     */
    public function isStarted(): bool
    {
        return true;
    }

    /**
     * Store a value.
     */
    public function put(
        string $key,
        mixed $value
    ): void {
        $this->data[$key] = $value;
    }

    /**
     * Retrieve a value.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->data[$key]
            ?? $default;
    }

    /**
     * Determine whether a key exists.
     */
    public function has(
        string $key
    ): bool {
        return array_key_exists(
            $key,
            $this->data
        );
    }

    /**
     * Remove a value.
     */
    public function forget(
        string $key
    ): void {
        unset(
            $this->data[$key]
        );
    }

    /**
     * Get all session values.
     *
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Regenerate session ID.
     */
    public function regenerate(): bool
    {
        return true;
    }

    /**
     * Destroy the test session.
     */
    public function destroy(): bool
    {
        $this->data = [];
        $this->flashData = [];

        return true;
    }

    /**
     * Clear all session data.
     */
    public function flush(): void
    {
        $this->data = [];
    }

    /**
     * Store or retrieve flash data.
     */
    public function flash(
        string $key,
        mixed $value = null
    ): mixed {
        if (func_num_args() === 2) {
            $this->flashData[$key] =
                $value;

            return null;
        }

        return $this->flashData[$key]
            ?? null;
    }

    /**
     * Determine whether a flash value exists.
     */
    public function hasFlash(
        string $key
    ): bool {
        return array_key_exists(
            $key,
            $this->flashData
        );
    }

    /**
     * Clear flash values.
     */
    public function clearFlash(): void
    {
        $this->flashData = [];
    }
}

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
| Find teacher with a linked user account.
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
    echo "No teacher profile is linked to a user account.\n";
    echo "Link one Teacher record to a users.id first.\n";
    echo "Authorization test cannot continue yet.\n";
    exit(0);
}

$userId = (int) (
    $teacherRecord['user_id']
);

$teacherId = (int) (
    $teacherRecord['id']
);

echo "Teacher/User Link: PASSED\n";

/*
|--------------------------------------------------------------------------
| Find an active assignment.
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
    echo "No active assignment found for this teacher.\n";
    echo "Create one teacher/classroom/subject assignment first.\n";
    exit(0);
}

$classroomId = (int) (
    $activeAssignment->classroom_id
);

$subjectId = (int) (
    $activeAssignment->subject_id
);

echo "Active Assignment: PASSED\n";

/*
|--------------------------------------------------------------------------
| Teacher authorization instance.
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
| Current teacher lookup.
|--------------------------------------------------------------------------
*/

$currentTeacher =
    $authorization->currentTeacher();

echo $currentTeacher !== null
    ? "Current Teacher Lookup: PASSED\n"
    : "Current Teacher Lookup: FAILED\n";

/*
|--------------------------------------------------------------------------
| Current teacher ID.
|--------------------------------------------------------------------------
*/

echo $authorization->currentTeacherId()
    === $teacherId
    ? "Current Teacher ID: PASSED\n"
    : "Current Teacher ID: FAILED\n";

/*
|--------------------------------------------------------------------------
| Assigned classroom.
|--------------------------------------------------------------------------
*/

echo $authorization->canAccessClassroom(
    $classroomId
)
    ? "Assigned Classroom Access: PASSED\n"
    : "Assigned Classroom Access: FAILED\n";

/*
|--------------------------------------------------------------------------
| Assigned subject.
|--------------------------------------------------------------------------
*/

echo $authorization->canManageSubject(
    $classroomId,
    $subjectId
)
    ? "Assigned Subject Access: PASSED\n"
    : "Assigned Subject Access: FAILED\n";

/*
|--------------------------------------------------------------------------
| Wrong subject must fail.
|--------------------------------------------------------------------------
*/

$wrongSubjectId = $subjectId + 99999;

echo !$authorization->canManageSubject(
    $classroomId,
    $wrongSubjectId
)
    ? "Unassigned Subject Protection: PASSED\n"
    : "Unassigned Subject Protection: FAILED\n";

/*
|--------------------------------------------------------------------------
| Administrator bypass.
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

echo $adminAuthorization->canManageSubject(
    $classroomId,
    $wrongSubjectId
)
    ? "Administrator Bypass: PASSED\n"
    : "Administrator Bypass: FAILED\n";

echo PHP_EOL;
echo "TEACHER AUTHORIZATION TEST COMPLETE\n";
