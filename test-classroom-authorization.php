<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Controllers\ClassroomController;
use SchoolERP\Http\Request;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Session\SessionManager;
use SchoolERP\View\ViewFactory;

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
 * Build the ClassroomController.
 */
function makeController(
    SessionManager $session
): ClassroomController {
    return new ClassroomController(
        new ViewFactory(
            __DIR__ . '/app/Views'
        ),
        $session,
        new ClassroomRepository()
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

    $_SERVER['REQUEST_METHOD'] = strtoupper(
        $method
    );

    $_SERVER['REQUEST_URI'] = $uri;

    $_SERVER['SCRIPT_NAME'] =
        '/SchoolERP/public/index.php';

    $reflection = new \ReflectionClass(
        Request::class
    );

    $reset = $reflection->getProperty(
        'instance'
    );

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

echo "CLASSROOM AUTHORIZATION TEST\n";
echo "============================\n\n";

try {

    /*
    |--------------------------------------------------------------------------
    | 1. Unauthenticated user.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    $controller = makeController(
        $session
    );

    $response = $controller->index();

    testResult(
        'Unauthenticated User Cannot Access Classroom List',
        hasStatus($response, 403)
    );

    $response = $controller->create();

    testResult(
        'Unauthenticated User Cannot Access Classroom Create',
        hasStatus($response, 403)
    );

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms',
            [
                'name' => 'Unauthorized Classroom',
            ]
        )
    );

    testResult(
        'Unauthenticated User Cannot Store Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->edit(
        999999999
    );

    testResult(
        'Unauthenticated User Cannot Edit Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms/999999999',
            [
                'name' => 'Unauthorized Update',
            ]
        ),
        999999999
    );

    testResult(
        'Unauthenticated User Cannot Update Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->destroy(
        999999999
    );

    testResult(
        'Unauthenticated User Cannot Delete Classroom',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 2. Administrator authorization boundary.
    |--------------------------------------------------------------------------
    |
    | The administrator must pass requireRole([1]).
    |
    | We deliberately use a nonexistent classroom ID for lookup-based
    | operations. Therefore a correctly authorized administrator should
    | receive 404 rather than 403.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        1,
        1
    );

    $controller = makeController(
        $session
    );

    $response = $controller->edit(
        999999999
    );

    testResult(
        'Administrator Passes Classroom Edit Authorization',
        hasStatus($response, 404)
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms/999999999',
            [
                'name' => 'Administrator Update',
            ]
        ),
        999999999
    );

    testResult(
        'Administrator Passes Classroom Update Authorization',
        hasStatus($response, 404)
    );

    $response = $controller->destroy(
        999999999
    );

    testResult(
        'Administrator Passes Classroom Delete Authorization',
        hasStatus($response, 404)
    );

    /*
    |--------------------------------------------------------------------------
    | 3. Teacher authorization boundary.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        2,
        2
    );

    $controller = makeController(
        $session
    );

    $response = $controller->index();

    testResult(
        'Teacher Cannot Access Classroom List',
        hasStatus($response, 403)
    );

    $response = $controller->create();

    testResult(
        'Teacher Cannot Access Classroom Create',
        hasStatus($response, 403)
    );

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms',
            [
                'name' => 'Teacher Classroom',
            ]
        )
    );

    testResult(
        'Teacher Cannot Store Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->edit(
        999999999
    );

    testResult(
        'Teacher Cannot Edit Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms/999999999',
            [
                'name' => 'Teacher Update',
            ]
        ),
        999999999
    );

    testResult(
        'Teacher Cannot Update Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->destroy(
        999999999
    );

    testResult(
        'Teacher Cannot Delete Classroom',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 4. Student authorization boundary.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        3,
        3
    );

    $controller = makeController(
        $session
    );

    $response = $controller->index();

    testResult(
        'Student Cannot Access Classroom List',
        hasStatus($response, 403)
    );

    $response = $controller->create();

    testResult(
        'Student Cannot Access Classroom Create',
        hasStatus($response, 403)
    );

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms',
            [
                'name' => 'Student Classroom',
            ]
        )
    );

    testResult(
        'Student Cannot Store Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->edit(
        999999999
    );

    testResult(
        'Student Cannot Edit Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms/999999999',
            [
                'name' => 'Student Update',
            ]
        ),
        999999999
    );

    testResult(
        'Student Cannot Update Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->destroy(
        999999999
    );

    testResult(
        'Student Cannot Delete Classroom',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 5. Parent authorization boundary.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        4,
        4
    );

    $controller = makeController(
        $session
    );

    $response = $controller->index();

    testResult(
        'Parent Cannot Access Classroom List',
        hasStatus($response, 403)
    );

    $response = $controller->create();

    testResult(
        'Parent Cannot Access Classroom Create',
        hasStatus($response, 403)
    );

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms',
            [
                'name' => 'Parent Classroom',
            ]
        )
    );

    testResult(
        'Parent Cannot Store Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->edit(
        999999999
    );

    testResult(
        'Parent Cannot Edit Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms/999999999',
            [
                'name' => 'Parent Update',
            ]
        ),
        999999999
    );

    testResult(
        'Parent Cannot Update Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->destroy(
        999999999
    );

    testResult(
        'Parent Cannot Delete Classroom',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 6. Accountant authorization boundary.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        5,
        5
    );

    $controller = makeController(
        $session
    );

    $response = $controller->index();

    testResult(
        'Accountant Cannot Access Classroom List',
        hasStatus($response, 403)
    );

    $response = $controller->create();

    testResult(
        'Accountant Cannot Access Classroom Create',
        hasStatus($response, 403)
    );

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms',
            [
                'name' => 'Accountant Classroom',
            ]
        )
    );

    testResult(
        'Accountant Cannot Store Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->edit(
        999999999
    );

    testResult(
        'Accountant Cannot Edit Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms/999999999',
            [
                'name' => 'Accountant Update',
            ]
        ),
        999999999
    );

    testResult(
        'Accountant Cannot Update Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->destroy(
        999999999
    );

    testResult(
        'Accountant Cannot Delete Classroom',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 7. Librarian authorization boundary.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        6,
        6
    );

    $controller = makeController(
        $session
    );

    $response = $controller->index();

    testResult(
        'Librarian Cannot Access Classroom List',
        hasStatus($response, 403)
    );

    $response = $controller->create();

    testResult(
        'Librarian Cannot Access Classroom Create',
        hasStatus($response, 403)
    );

    $response = $controller->store(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms',
            [
                'name' => 'Librarian Classroom',
            ]
        )
    );

    testResult(
        'Librarian Cannot Store Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->edit(
        999999999
    );

    testResult(
        'Librarian Cannot Edit Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            '/SchoolERP/public/classrooms/999999999',
            [
                'name' => 'Librarian Update',
            ]
        ),
        999999999
    );

    testResult(
        'Librarian Cannot Update Classroom',
        hasStatus($response, 403)
    );

    $response = $controller->destroy(
        999999999
    );

    testResult(
        'Librarian Cannot Delete Classroom',
        hasStatus($response, 403)
    );

    /*
    |--------------------------------------------------------------------------
    | 8. Invalid classroom ID handling for Administrator.
    |--------------------------------------------------------------------------
    */

    $session->flush();

    authenticateAs(
        $session,
        1,
        1
    );

    $controller = makeController(
        $session
    );

    $invalidIds = [
        0,
        -1,
        999999999,
    ];

    foreach ($invalidIds as $id) {

        $response = $controller->edit(
            $id
        );

        testResult(
            "Administrator Edit ID {$id} Returns 404",
            hasStatus($response, 404)
        );

        $response = $controller->update(
            makeRequest(
                'POST',
                '/SchoolERP/public/classrooms/' . $id,
                [
                    'name' => 'Invalid Classroom Update',
                ]
            ),
            $id
        );

        testResult(
            "Administrator Update ID {$id} Returns 404",
            hasStatus($response, 404)
        );

        $response = $controller->destroy(
            $id
        );

        testResult(
            "Administrator Delete ID {$id} Returns 404",
            hasStatus($response, 404)
        );
    }

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
        echo "CLASSROOM AUTHORIZATION TEST PASSED\n";
        exit(0);
    }

    echo "CLASSROOM AUTHORIZATION TEST FAILED\n";
    exit(1);

} catch (Throwable $exception) {

    $session->flush();

    echo PHP_EOL;
    echo "TEST ERROR\n";
    echo "==========\n";
    echo $exception->getMessage() . "\n";

    echo "File: "
        . $exception->getFile()
        . "\n";

    echo "Line: "
        . $exception->getLine()
        . "\n";

    echo PHP_EOL;
    echo "PASSED: {$passed}\n";
    echo "FAILED: " . ($failed + 1) . "\n";

    exit(1);
}