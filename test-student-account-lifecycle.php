<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;

echo "STUDENT ACCOUNT LIFECYCLE TEST\n";
echo "==============================\n\n";

$config = new Config(
    __DIR__ . '/config'
);

$database = new Database(
    $config
);

$passed = 0;
$failed = 0;

function testResult(
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

try {
    /*
    |--------------------------------------------------------------------------
    | Begin transaction
    |--------------------------------------------------------------------------
    */
    $database->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Confirm required schema exists.
    |--------------------------------------------------------------------------
    */
    $studentColumns = $database->select(
        "SHOW COLUMNS FROM students"
    );

    $studentColumnNames = [];

    foreach ($studentColumns as $column) {
        $studentColumnNames[] =
            (string) ($column['Field'] ?? '');
    }

    testResult(
        'Students.user_id Column',
        in_array(
            'user_id',
            $studentColumnNames,
            true
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Confirm users table contains required fields.
    |--------------------------------------------------------------------------
    */
    $userColumns = $database->select(
        "SHOW COLUMNS FROM users"
    );

    $userColumnNames = [];

    foreach ($userColumns as $column) {
        $userColumnNames[] =
            (string) ($column['Field'] ?? '');
    }

    testResult(
        'Users.role_id Column',
        in_array(
            'role_id',
            $userColumnNames,
            true
        )
    );

    testResult(
        'Users.email Column',
        in_array(
            'email',
            $userColumnNames,
            true
        )
    );

    testResult(
        'Users.password Column',
        in_array(
            'password',
            $userColumnNames,
            true
        )
    );

    testResult(
        'Users.status Column',
        in_array(
            'status',
            $userColumnNames,
            true
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Find a student.
    |--------------------------------------------------------------------------
    */
    $students = $database->select(
        "SELECT id, first_name, last_name, classroom_id, user_id
         FROM students
         ORDER BY id ASC
         LIMIT 1"
    );

    if ($students === []) {
        echo "\nNo student record exists.\n";
        echo "Create at least one student before running this test.\n";

        $database->rollBack();

        exit(0);
    }

    $student = $students[0];

    $studentId = (int) (
        $student['id'] ?? 0
    );

    testResult(
        'Student Record Available',
        $studentId > 0
    );

    /*
    |--------------------------------------------------------------------------
    | Preserve original student account link.
    |--------------------------------------------------------------------------
    */
    $originalUserId =
        $student['user_id'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | Generate temporary credentials.
    |--------------------------------------------------------------------------
    */
    $temporaryEmail =
        'student.lifecycle.'
        . bin2hex(random_bytes(6))
        . '@schoolerp.test';

    $temporaryPassword =
        'Lifecycle@123';

    /*
    |--------------------------------------------------------------------------
    | Create temporary student user.
    |--------------------------------------------------------------------------
    */
    $passwordHash = password_hash(
        $temporaryPassword,
        PASSWORD_DEFAULT
    );

    if ($passwordHash === false) {
        throw new RuntimeException(
            'Unable to create password hash.'
        );
    }

    $inserted = $database->insert(
        "INSERT INTO users
        (
            role_id,
            first_name,
            last_name,
            email,
            password,
            status
        )
        VALUES
        (
            :role_id,
            :first_name,
            :last_name,
            :email,
            :password,
            :status
        )",
        [
            'role_id' =>
                3,

            'first_name' =>
                (string) (
                    $student['first_name'] ?? ''
                ),

            'last_name' =>
                (string) (
                    $student['last_name'] ?? ''
                ),

            'email' =>
                $temporaryEmail,

            'password' =>
                $passwordHash,

            'status' =>
                'active',
        ]
    );

    testResult(
        'Student User Creation',
        $inserted
    );

    $userId = (int) $database->lastInsertId();

    testResult(
        'Created User Has Student Role',
        $userId > 0
        && (int) (
            $database->select(
                "SELECT role_id
                 FROM users
                 WHERE id = :id
                 LIMIT 1",
                ['id' => $userId]
            )[0]['role_id'] ?? 0
        ) === 3
    );

    /*
    |--------------------------------------------------------------------------
    | Link student to user account.
    |--------------------------------------------------------------------------
    */
    $linkedRows = $database->update(
        "UPDATE students
         SET user_id = :user_id
         WHERE id = :student_id",
        [
            'user_id' =>
                $userId,

            'student_id' =>
                $studentId,
        ]
    );

    testResult(
        'Student/User Link',
        $linkedRows === 1
    );

    $linkedStudent = $database->select(
        "SELECT user_id
         FROM students
         WHERE id = :student_id
         LIMIT 1",
        [
            'student_id' =>
                $studentId,
        ]
    );

    testResult(
        'Student Points To Created User',
        isset($linkedStudent[0])
        && (int) (
            $linkedStudent[0]['user_id'] ?? 0
        ) === $userId
    );

    /*
    |--------------------------------------------------------------------------
    | Verify password.
    |--------------------------------------------------------------------------
    */
    $storedUser = $database->select(
        "SELECT password
         FROM users
         WHERE id = :id
         LIMIT 1",
        [
            'id' =>
                $userId,
        ]
    );

    testResult(
        'Temporary Password Verification',
        isset($storedUser[0]['password'])
        && password_verify(
            $temporaryPassword,
            (string) $storedUser[0]['password']
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Suspend account.
    |--------------------------------------------------------------------------
    */
    $suspendedRows = $database->update(
        "UPDATE users
         SET status = 'suspended'
         WHERE id = :id
           AND role_id = 3",
        [
            'id' =>
                $userId,
        ]
    );

    testResult(
        'Suspend Student Account',
        $suspendedRows === 1
    );

    $statusAfterSuspend =
        $database->select(
            "SELECT status
             FROM users
             WHERE id = :id
             LIMIT 1",
            [
                'id' =>
                    $userId,
            ]
        );

    testResult(
        'Suspended Status Stored',
        isset($statusAfterSuspend[0])
        && strtolower(
            (string) (
                $statusAfterSuspend[0]['status']
                ?? ''
            )
        ) === 'suspended'
    );

    /*
    |--------------------------------------------------------------------------
    | Reactivate account.
    |--------------------------------------------------------------------------
    */
    $activatedRows = $database->update(
        "UPDATE users
         SET status = 'active'
         WHERE id = :id
           AND role_id = 3",
        [
            'id' =>
                $userId,
        ]
    );

    testResult(
        'Reactivate Student Account',
        $activatedRows === 1
    );

    $statusAfterActivation =
        $database->select(
            "SELECT status
             FROM users
             WHERE id = :id
             LIMIT 1",
            [
                'id' =>
                    $userId,
            ]
        );

    testResult(
        'Active Status Restored',
        isset($statusAfterActivation[0])
        && strtolower(
            (string) (
                $statusAfterActivation[0]['status']
                ?? ''
            )
        ) === 'active'
    );

    /*
    |--------------------------------------------------------------------------
    | Reset password.
    |--------------------------------------------------------------------------
    */
    $newPassword =
        'LifecycleReset@456';

    $newPasswordHash =
        password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );

    if ($newPasswordHash === false) {
        throw new RuntimeException(
            'Unable to hash reset password.'
        );
    }

    $resetRows = $database->update(
        "UPDATE users
         SET password = :password
         WHERE id = :id
           AND role_id = 3",
        [
            'password' =>
                $newPasswordHash,

            'id' =>
                $userId,
        ]
    );

    testResult(
        'Student Password Reset',
        $resetRows === 1
    );

    $resetUser = $database->select(
        "SELECT password
         FROM users
         WHERE id = :id
         LIMIT 1",
        [
            'id' =>
                $userId,
        ]
    );

    testResult(
        'Reset Password Verification',
        isset($resetUser[0]['password'])
        && password_verify(
            $newPassword,
            (string) $resetUser[0]['password']
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Confirm the student academic identity remains unchanged.
    |--------------------------------------------------------------------------
    */
    $currentStudent =
        $database->select(
            "SELECT id, first_name, last_name, classroom_id
             FROM students
             WHERE id = :id
             LIMIT 1",
            [
                'id' =>
                    $studentId,
            ]
        );

    testResult(
        'Student Academic Record Preserved',
        isset($currentStudent[0])
        && (int) $currentStudent[0]['id'] === $studentId
        && (string) (
            $currentStudent[0]['first_name']
            ?? ''
        ) === (string) (
            $student['first_name']
            ?? ''
        )
        && (string) (
            $currentStudent[0]['last_name']
            ?? ''
        ) === (string) (
            $student['last_name']
            ?? ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Restore original student link before rollback validation.
    |--------------------------------------------------------------------------
    */
    $database->update(
        "UPDATE students
         SET user_id = :user_id
         WHERE id = :student_id",
        [
            'user_id' =>
                $originalUserId,

            'student_id' =>
                $studentId,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | Roll back everything created by this test.
    |--------------------------------------------------------------------------
    */
    $database->rollBack();

    echo PHP_EOL;
    echo "Temporary lifecycle data rolled back.\n";
    echo "PASSED: {$passed}\n";
    echo "FAILED: {$failed}\n";

    echo PHP_EOL;

    echo $failed === 0
        ? "STUDENT ACCOUNT LIFECYCLE TEST PASSED\n"
        : "STUDENT ACCOUNT LIFECYCLE TEST FAILED\n";

} catch (Throwable $exception) {

    if ($database->inTransaction()) {
        $database->rollBack();
    }

    echo PHP_EOL;
    echo "TEST ERROR:\n";
    echo $exception->getMessage();
    echo PHP_EOL;

    exit(1);
}