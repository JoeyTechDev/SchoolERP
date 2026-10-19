<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Repositories\ParentStudentRepository;
use SchoolERP\Services\ParentAuthorizationService;
use SchoolERP\Session\SessionManager;

$config = new Config(__DIR__ . '/config');
$database = new Database($config);
$parentStudents = new ParentStudentRepository($database);

$passed = 0;
$failed = 0;
$session = new SessionManager();
$transactionStarted = false;

function parentTestResult(
    string $name,
    bool $condition
): void {
    global $passed, $failed;

    if ($condition) {
        echo $name . ": PASSED\n";
        $passed++;
    } else {
        echo $name . ": FAILED\n";
        $failed++;
    }
}

function parentAuthenticateAs(
    SessionManager $session,
    int $userId,
    int $roleId
): void {
    $session->flush();
    $session->put('user_id', $userId);
    $session->put('role_id', $roleId);
}

try {
    $session->start();
    $session->flush();

    echo "SCHOOLERP PARENT AUTHORIZATION TEST\n";
    echo "===================================\n\n";

    /*
     * Locate existing records. Tests must not create permanent
     * users or students.
     */
    $parents = $database->select(
        'SELECT id
         FROM users
         WHERE role_id = 4
         ORDER BY id ASC
         LIMIT 1'
    );

    $administrators = $database->select(
        'SELECT id
         FROM users
         WHERE role_id = 1
         ORDER BY id ASC
         LIMIT 1'
    );

    $students = $database->select(
        'SELECT id
         FROM students
         ORDER BY id ASC
         LIMIT 1'
    );

    parentTestResult(
        'Parent User Available',
        $parents !== []
    );

    parentTestResult(
        'Administrator User Available',
        $administrators !== []
    );

    parentTestResult(
        'Student Record Available',
        $students !== []
    );

    if (
        $parents === []
        || $administrators === []
        || $students === []
    ) {
        throw new RuntimeException(
            'A parent user, administrator user, and student record '
            . 'are required to complete the authorization tests.'
        );
    }

    $parentId = (int) $parents[0]['id'];
    $adminId = (int) $administrators[0]['id'];
    $studentId = (int) $students[0]['id'];
    $unlinkedStudentId = 999999999;

    parentTestResult(
        'Parent User ID Is Valid',
        $parentId > 0
    );

    parentTestResult(
        'Student ID Is Valid',
        $studentId > 0
    );

    /*
     * Start a transaction before creating a temporary relationship.
     * The transaction is always rolled back in the finally block.
     */
    $database->beginTransaction();
    $transactionStarted = true;

    $existingLink = $parentStudents->hasStudent(
        $parentId,
        $studentId
    );

    if (!$existingLink) {
        $database->insert(
            'INSERT INTO parent_student
                (parent_user_id, student_id)
             VALUES
                (:parent_user_id, :student_id)',
            [
                'parent_user_id' => $parentId,
                'student_id' => $studentId,
            ]
        );
    }

    $authorization = new ParentAuthorizationService(
        $session,
        $parentStudents
    );

    /*
     * 1. Parent role and identity.
     */
    parentAuthenticateAs($session, $parentId, 4);

    parentTestResult(
        'Parent Role Is Recognized',
        $authorization->isParent()
    );

    parentTestResult(
        'Current Parent User ID Is Correct',
        $authorization->currentUserId() === $parentId
    );

    parentTestResult(
        'Parent Has No Authorization Error',
        $authorization->authorizationError() === null
    );

    /*
     * 2. Parent can access a linked student only.
     */
    parentTestResult(
        'Linked Student Is Authorized',
        $authorization->canAccessStudent($studentId)
    );

    parentTestResult(
        'Unlinked Student Is Rejected',
        !$authorization->canAccessStudent($unlinkedStudentId)
    );

    parentTestResult(
        'Zero Student ID Is Rejected',
        !$authorization->canAccessStudent(0)
    );

    parentTestResult(
        'Negative Student ID Is Rejected',
        !$authorization->canAccessStudent(-1)
    );

    /*
     * 3. Parent student list.
     */
    $linkedStudents = $authorization->currentStudents();

    $linkedStudentIds = array_map(
        static fn (array $student): int =>
            (int) $student['id'],
        $linkedStudents
    );

    parentTestResult(
        'Parent Student List Is Not Empty',
        $linkedStudents !== []
    );

    parentTestResult(
        'Linked Student Appears In Parent List',
        in_array($studentId, $linkedStudentIds, true)
    );

    /*
     * 4. Non-parent roles must not access parent data.
     */
    parentAuthenticateAs($session, $adminId, 1);

    parentTestResult(
        'Administrator Is Not Recognized As Parent',
        !$authorization->isParent()
    );

    parentTestResult(
        'Administrator Cannot Access Parent Student',
        !$authorization->canAccessStudent($studentId)
    );

    parentTestResult(
        'Non-Parent Receives Authorization Error',
        $authorization->authorizationError() !== null
    );

    parentTestResult(
        'Non-Parent Student List Is Empty',
        $authorization->currentStudents() === []
    );

    /*
     * 5. Invalid or missing session identity.
     */
    parentAuthenticateAs($session, 0, 4);

    parentTestResult(
        'Parent Role With Missing User ID Is Rejected',
        $authorization->authorizationError() !== null
    );

    parentTestResult(
        'Missing User ID Cannot Access Student',
        !$authorization->canAccessStudent($studentId)
    );

    parentTestResult(
        'Missing User ID Returns Empty Student List',
        $authorization->currentStudents() === []
    );

    /*
     * 6. Repository input validation.
     */
    parentTestResult(
        'Repository Rejects Invalid Parent ID',
        $parentStudents->studentsForParent(0) === []
    );

    parentTestResult(
        'Repository Rejects Invalid Student Relationship',
        !$parentStudents->hasStudent(0, $studentId)
    );

    parentTestResult(
        'Repository Rejects Invalid Student ID',
        !$parentStudents->hasStudent($parentId, 0)
    );

} catch (Throwable $exception) {
    echo "\nTEST ERROR\n";
    echo "==========\n";
    echo $exception->getMessage() . "\n";

    echo 'File: ' . $exception->getFile() . "\n";
    echo 'Line: ' . $exception->getLine() . "\n";

    $failed++;

} finally {
    /*
     * Never commit the temporary test relationship.
     */
    if ($transactionStarted && $database->inTransaction()) {
        try {
            $database->rollBack();
            echo "\nTemporary database changes rolled back.\n";
        } catch (Throwable $rollbackException) {
            echo "\nCRITICAL: Database rollback failed: ";
            echo $rollbackException->getMessage() . "\n";
            $failed++;
        }
    }

    $session->flush();
}

echo "\nPASSED: {$passed}\n";
echo "FAILED: {$failed}\n\n";

if ($failed === 0) {
    echo "PARENT AUTHORIZATION TEST PASSED\n";
    exit(0);
}

echo "PARENT AUTHORIZATION TEST FAILED\n";
exit(1);
