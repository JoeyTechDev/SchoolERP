<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Config\Database;
use SchoolERP\Http\Request;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Session\SessionManager;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

/**
 * Academic Session Authorization Test
 *
 * Verifies:
 * - Unauthenticated users are rejected.
 * - Administrator can access Academic Session management.
 * - Non-administrative roles are rejected.
 * - Administrator receives 404 for invalid resources.
 * - Authorization happens before resource lookup.
 */

function testResult(
    string $description,
    bool $passed
): void {
    echo ($passed ? 'PASSED: ' : 'FAILED: ') . $description . PHP_EOL;
}

function resetRequest(): void
{
    $reflection = new \ReflectionClass(Request::class);

    $property = $reflection->getProperty('instance');
    $property->setAccessible(true);
    $property->setValue(null);
}

function makeRequest(
    string $method,
    string $uri,
    array $get = [],
    array $post = []
): Request {

    $_GET = $get;
    $_POST = $post;
    $_FILES = [];
    $_COOKIE = [];

    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/SchoolERP/public/index.php';

    resetRequest();

    return Request::capture();
}

function authenticateAs(
    SessionInterface $session,
    int $userId,
    int $roleId
): void {

    $session->flush();

    $session->put(
        'user_id',
        $userId
    );

    $session->put(
        'role_id',
        $roleId
    );
}

function unauthenticate(
    SessionInterface $session
): void {
    $session->flush();
}

/*
|--------------------------------------------------------------------------
| Start session before any output
|--------------------------------------------------------------------------
*/

$session = new SessionManager();
$session->start();

/*
|--------------------------------------------------------------------------
| Dependencies
|--------------------------------------------------------------------------
*/

$views = new ViewFactory(
    __DIR__ . '/app/Views'
);

$repository = new AcademicSessionRepository();

$invalidId = 999999999;

/*
|--------------------------------------------------------------------------
| Controller
|--------------------------------------------------------------------------
*/

$controller = new \SchoolERP\Controllers\AcademicSessionController(
    $views,
    $session,
    $repository
);

/*
|--------------------------------------------------------------------------
| Test counters
|--------------------------------------------------------------------------
*/

$passed = 0;
$failed = 0;

/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
*/

function assertStatus(
    string $description,
    \SchoolERP\Http\Response $response,
    int $expectedStatus,
    int &$passed,
    int &$failed
): void {

    $actualStatus = $response->getStatus();

    $success = $actualStatus === $expectedStatus;

    testResult(
        $description . ' [' . $actualStatus . ']',
        $success
    );

    if ($success) {
        $passed++;
    } else {
        $failed++;
    }
}

/*
|--------------------------------------------------------------------------
| Header
|--------------------------------------------------------------------------
*/

echo PHP_EOL;
echo "ACADEMIC SESSION AUTHORIZATION TEST" . PHP_EOL;
echo "==================================" . PHP_EOL;
echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| 1. Unauthenticated access
|--------------------------------------------------------------------------
*/

unauthenticate($session);

$request = makeRequest(
    'GET',
    '/academic-sessions'
);

$response = $controller->index();

assertStatus(
    'Unauthenticated user receives 403 for Academic Session index',
    $response,
    403,
    $passed,
    $failed
);


/*
|--------------------------------------------------------------------------
| 4. Administrator receives 404 for invalid resources
|--------------------------------------------------------------------------
*/

authenticateAs(
    $session,
    1,
    1
);

$adminInvalidTests = [
    [
        'GET',
        '/academic-sessions/' . $invalidId . '/edit',
        'Administrator receives 404 for invalid Academic Session edit'
    ],
    [
        'POST',
        '/academic-sessions/' . $invalidId . '/update',
        'Administrator receives 404 for invalid Academic Session update'
    ],
    [
        'POST',
        '/academic-sessions/' . $invalidId . '/current',
        'Administrator receives 404 for invalid Academic Session set-current'
    ],
    [
        'POST',
        '/academic-sessions/' . $invalidId . '/activate',
        'Administrator receives 404 for invalid Academic Session activate'
    ],
    [
        'POST',
        '/academic-sessions/' . $invalidId . '/deactivate',
        'Administrator receives 404 for invalid Academic Session deactivate'
    ],
];

foreach ($adminInvalidTests as [$method, $uri, $description]) {

    $request = makeRequest(
        $method,
        $uri
    );

    try {

        $response = match (true) {

            str_contains($uri, '/edit')
                => $controller->edit($invalidId),

            str_contains($uri, '/update')
                => $controller->update(
                    $request,
                    $invalidId
                ),

            str_contains($uri, '/current')
                => $controller->setCurrent($invalidId),

            str_contains($uri, '/activate')
                => $controller->activate($invalidId),

            str_contains($uri, '/deactivate')
                => $controller->deactivate($invalidId),

            default => throw new \RuntimeException(
                'Unknown Academic Session test route.'
            ),
        };

        assertStatus(
            $description,
            $response,
            404,
            $passed,
            $failed
        );

    } catch (\Throwable $e) {

        testResult(
            $description,
            false
        );

        echo '  ERROR: ' . $e->getMessage() . PHP_EOL;

        $failed++;
    }
}

/*
|--------------------------------------------------------------------------
| 5. Non-administrator roles
|--------------------------------------------------------------------------
|
| Role mapping:
| 2 = Teacher
| 3 = Student
| 4 = Parent
| 5 = Accountant
| 6 = Librarian
|
*/

$nonAdminRoles = [
    2 => 'Teacher',
    3 => 'Student',
    4 => 'Parent',
    5 => 'Accountant',
    6 => 'Librarian',
];

foreach ($nonAdminRoles as $roleId => $roleName) {

    authenticateAs(
        $session,
        999 + $roleId,
        $roleId
    );

    $tests = [
        [
            'GET',
            '/academic-sessions',
            'index'
        ],
        [
            'GET',
            '/academic-sessions/create',
            'create'
        ],
        [
            'POST',
            '/academic-sessions',
            'store'
        ],
        [
            'GET',
            '/academic-sessions/1/edit',
            'edit'
        ],
        [
            'POST',
            '/academic-sessions/1/update',
            'update'
        ],
        [
            'POST',
            '/academic-sessions/1/current',
            'setCurrent'
        ],
        [
            'POST',
            '/academic-sessions/1/activate',
            'activate'
        ],
        [
            'POST',
            '/academic-sessions/1/deactivate',
            'deactivate'
        ],
    ];

    foreach ($tests as [$method, $uri, $action]) {

        $request = makeRequest(
            $method,
            $uri
        );

        try {

            $response = match ($action) {

                'index'
                    => $controller->index(),

                'create'
                    => $controller->create(),

                'store'
                    => $controller->store($request),

                'edit'
                    => $controller->edit(1),

                'update'
                    => $controller->update(
                        $request,
                        1
                    ),

                'setCurrent'
                    => $controller->setCurrent(1),

                'activate'
                    => $controller->activate(1),

                'deactivate'
                    => $controller->deactivate(1),

                default => throw new \RuntimeException(
                    'Unknown Academic Session action.'
                ),
            };

            assertStatus(
                $roleName . ' receives 403 for Academic Session ' . $action,
                $response,
                403,
                $passed,
                $failed
            );

        } catch (\Throwable $e) {

            testResult(
                $roleName . ' receives 403 for Academic Session ' . $action,
                false
            );

            echo '  ERROR: ' . $e->getMessage() . PHP_EOL;

            $failed++;
        }
    }
}

/*
|--------------------------------------------------------------------------
| 6. Administrator invalid IDs
|--------------------------------------------------------------------------
*/

authenticateAs(
    $session,
    1,
    1
);

$invalidIds = [
    0,
    -1,
    999999999,
];

foreach ($invalidIds as $id) {

    $tests = [
        [
            'GET',
            '/academic-sessions/' . $id . '/edit',
            'edit'
        ],
        [
            'POST',
            '/academic-sessions/' . $id . '/update',
            'update'
        ],
        [
            'POST',
            '/academic-sessions/' . $id . '/current',
            'setCurrent'
        ],
        [
            'POST',
            '/academic-sessions/' . $id . '/activate',
            'activate'
        ],
        [
            'POST',
            '/academic-sessions/' . $id . '/deactivate',
            'deactivate'
        ],
    ];

    foreach ($tests as [$method, $uri, $action]) {

        $request = makeRequest(
            $method,
            $uri
        );

        try {

            $response = match ($action) {

                'edit'
                    => $controller->edit($id),

                'update'
                    => $controller->update(
                        $request,
                        $id
                    ),

                'setCurrent'
                    => $controller->setCurrent($id),

                'activate'
                    => $controller->activate($id),

                'deactivate'
                    => $controller->deactivate($id),

                default => throw new \RuntimeException(
                    'Unknown Academic Session action.'
                ),
            };

            assertStatus(
                'Administrator receives 404 for Academic Session ' .
                $action .
                ' with ID ' .
                $id,
                $response,
                404,
                $passed,
                $failed
            );

        } catch (\Throwable $e) {

            testResult(
                'Administrator receives 404 for Academic Session ' .
                $action .
                ' with ID ' .
                $id,
                false
            );

            echo '  ERROR: ' . $e->getMessage() . PHP_EOL;

            $failed++;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

unauthenticate($session);

/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

echo PHP_EOL;
echo "==================================" . PHP_EOL;
echo "PASSED: {$passed}" . PHP_EOL;
echo "FAILED: {$failed}" . PHP_EOL;
echo "==================================" . PHP_EOL;

if ($failed === 0) {

    echo PHP_EOL;
    echo "ACADEMIC SESSION AUTHORIZATION TEST PASSED" . PHP_EOL;

    exit(0);
}

echo PHP_EOL;
echo "ACADEMIC SESSION AUTHORIZATION TEST FAILED" . PHP_EOL;

exit(1);