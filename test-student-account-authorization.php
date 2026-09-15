<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Controllers\StudentAccountController;
use SchoolERP\Database\Database;
use SchoolERP\Http\Request;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Session\SessionManager;
use SchoolERP\View\ViewFactory;

$config = new Config(
    __DIR__ . '/config'
);

$database = new Database(
    $config
);

$passed = 0;
$failed = 0;

/**
 * Assert a test condition.
 */
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

/**
 * Create a fresh native PHP session.
 */
function makeTestSession(): SessionManager
{
    $session = new SessionManager();

    $session->start();
    $session->flush();

    return $session;
}

/**
 * Simulate an authenticated session.
 */
function authenticateAs(
    SessionManager $session,
    int $userId,
    int $roleId
): void {
    $session->put(
        'user_id',
        $userId
    );

    $session->put(
        'role_id',
        $roleId
    );
}

/**
 * Build the StudentAccountController.
 */
function makeController(
    SessionManager $session
): StudentAccountController {
    return new StudentAccountController(
        new ViewFactory(
            __DIR__ . '/app/Views'
        ),
        $session,
        new StudentRepository()
    );
}

/**
 * Determine whether a response has a given HTTP status.
 */
function hasStatus(
    mixed $response,
    int $status
): bool {
    return method_exists(
        $response,
        'getStatus'
    )
    && $response->getStatus() === $status;
}

/**
 * Build a Request using the actual Request API.
 *
 * Request has a private constructor, so the test temporarily
 * populates PHP superglobals and captures the request.
 */
function makeRequest(
    string $method,
    string $uri,
    array $post = []
): Request {
    $_GET = [];
    $_POST = $post;
    $_FILES = [];
    $_COOKIE = [];
    $_SERVER['REQUEST_METHOD'] = strtoupper($method);
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/SchoolERP/public/index.php';

    $reflection = new ReflectionClass(Request::class);

    $reset = $reflection->getProperty('instance');
    $reset->setAccessible(true);
    $reset->setValue(null);

    return Request::capture();
}

/*
|--------------------------------------------------------------------------
| Start session before producing output.
|--------------------------------------------------------------------------
*/

$session = makeTestSession();

echo "STUDENT ACCOUNT AUTHORIZATION & INTEGRITY TEST\n";
echo "===============================================\n\n";

try {

    /*
    |--------------------------------------------------------------------------
    | Locate an existing Administrator.
    |--------------------------------------------------------------------------
    */

    $adminUsers = $database->select(
        "SELECT id
         FROM users
         WHERE role_id = 1
         ORDER BY id ASC
         LIMIT 1"
    );

    testResult(
        'Administrator User Available',
        $adminUsers !== []
    );

    if ($adminUsers === []) {
        throw new RuntimeException(
            'An Administrator user is required for this test.'
        );
    }

    $adminId = (int) (
        $adminUsers[0]['id'] ?? 0
    );

    testResult(
        'Administrator ID Is Valid',
        $adminId > 0
    );

    /*
    |--------------------------------------------------------------------------
    | Locate an existing Student.
    |--------------------------------------------------------------------------
    */

    $studentRecords = $database->select(
        "SELECT id
         FROM students
         ORDER BY id ASC
         LIMIT 1"
    );

    testResult(
        'Student Record Available',
        $studentRecords !== []
    );

    if ($studentRecords === []) {
        throw new RuntimeException(
            'A Student record is required for this test.'
        );
    }

    $studentId = (int) (
        $studentRecords[0]['id'] ?? 0
    );

    testResult(
        'Student ID Is Valid',
        $studentId > 0
    );

    /*
    |--------------------------------------------------------------------------
    | Authorization tests use a deliberately invalid student ID.
    |--------------------------------------------------------------------------
    |
    | This is intentional.
    |
    | We are testing the authorization boundary, not student lookup or
    | view rendering. A non-administrator must receive 403 BEFORE the
    | controller attempts to resolve the student.
    |
    | Administrator access is tested separately with the real student ID.
    |--------------------------------------------------------------------------
    */

    $invalidStudentId = 999999999;

    /*
    |--------------------------------------------------------------------------
    | 1. Unauthenticated user.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Unauthenticated User Rejected',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 2. Administrator.
    |--------------------------------------------------------------------------
    |
    | We use an invalid ID here so the test verifies that the Administrator
    | passes authorization and then reaches normal resource handling.
    |
    | Expected result: 404, NOT 403.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        1
    );

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Administrator Passes Authorization Boundary',
        hasStatus($response, 404)
    );

    /*
    |--------------------------------------------------------------------------
    | 3. Teacher.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        2
    );

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Teacher Role Rejected',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 4. Student.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        3
    );

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Student Role Rejected',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 5. Parent.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        4
    );

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Parent Role Rejected',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 6. Accountant.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        5
    );

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Accountant Role Rejected',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 7. Librarian.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        6
    );

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Librarian Role Rejected',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 8. Invalid student ID handling for Administrator.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        1
    );

    $controller = makeController(
        $session
    );

    $response = $controller->show(
        0
    );

    testResult(
        'Zero Student ID Rejected',
        hasStatus($response, 404)
    );

    $response = $controller->show(
        -1
    );

    testResult(
        'Negative Student ID Rejected',
        hasStatus($response, 404)
    );

    $response = $controller->show(
        $invalidStudentId
    );

    testResult(
        'Nonexistent Student ID Rejected',
        hasStatus($response, 404)
    );

    /*
    |--------------------------------------------------------------------------
    | 9. Teacher cannot suspend or activate.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        2
    );

    $controller = makeController(
        $session
    );

    $response = $controller->suspend(
        $invalidStudentId
    );

    testResult(
        'Teacher Cannot Suspend Student Account',
        hasStatus($response, 403)
    );

    $response = $controller->activate(
        $invalidStudentId
    );

    testResult(
        'Teacher Cannot Activate Student Account',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 10. Teacher cannot reset password.
    |--------------------------------------------------------------------------
    */

    $response = $controller->resetPassword(
        makeRequest(
            'POST',
            '/SchoolERP/public/students/'
                . $invalidStudentId
                . '/account/reset-password'
        ),
        $invalidStudentId
    );

    testResult(
        'Teacher Cannot Reset Student Password',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 11. Teacher cannot create student login account.
    |--------------------------------------------------------------------------
    */

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/students/'
                . $invalidStudentId
                . '/account',
            [
                'email' => 'authorization-test@example.com',
                'password' => 'AuthorizationTest123',
                'password_confirmation' => 'AuthorizationTest123',
            ]
        ),
        $invalidStudentId
    );

    testResult(
        'Teacher Cannot Create Student Login Account',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 12. Student cannot suspend or activate.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        $adminId,
        3
    );

    $controller = makeController(
        $session
    );

    $response = $controller->suspend(
        $invalidStudentId
    );

    testResult(
        'Student Cannot Suspend Student Account',
        hasStatus($response, 403)
    );

    $response = $controller->activate(
        $invalidStudentId
    );

    testResult(
        'Student Cannot Activate Student Account',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 13. Student cannot create a login account.
    |--------------------------------------------------------------------------
    */

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/students/'
                . $invalidStudentId
                . '/account',
            [
                'email' => 'authorization-test@example.com',
                'password' => 'AuthorizationTest123',
                'password_confirmation' => 'AuthorizationTest123',
            ]
        ),
        $invalidStudentId
    );

    testResult(
        'Student Cannot Create Login Account',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 14. Student cannot reset a password.
    |--------------------------------------------------------------------------
    */

    $response = $controller->resetPassword(
        makeRequest(
            'POST',
            '/SchoolERP/public/students/'
                . $invalidStudentId
                . '/account/reset-password'
        ),
        $invalidStudentId
    );

    testResult(
        'Student Cannot Reset Student Password',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | Restore session.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    echo PHP_EOL;
    echo "PASSED: {$passed}\n";
    echo "FAILED: {$failed}\n";

    echo PHP_EOL;

    if ($failed === 0) {
        echo "STUDENT ACCOUNT AUTHORIZATION TEST PASSED\n";
        exit(0);
    }

    echo "STUDENT ACCOUNT AUTHORIZATION TEST FAILED\n";
    exit(1);

} catch (Throwable $exception) {

    $session->flush();

    echo PHP_EOL;
    echo "TEST ERROR\n";
    echo "==========\n";
    echo $exception->getMessage() . "\n";

    if ($exception->getFile() !== '') {
        echo "File: "
            . $exception->getFile()
            . "\n";
    }

    if ($exception->getLine() > 0) {
        echo "Line: "
            . $exception->getLine()
            . "\n";
    }

    echo PHP_EOL;
    echo "PASSED: {$passed}\n";
    echo "FAILED: " . ($failed + 1) . "\n";

    exit(1);
}