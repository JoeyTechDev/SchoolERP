<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Controllers\SubjectController;
use SchoolERP\Http\Request;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Session\SessionManager;
use SchoolERP\View\ViewFactory;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

echo "SUBJECT AUTHORIZATION TEST\n";
echo "==========================\n\n";

$passed = 0;
$failed = 0;

/**
 * Assert a condition.
 */
function assertTrue(
    bool $condition,
    string $message
): void {
    global $passed, $failed;

    if ($condition) {
        $passed++;
        echo "PASSED: {$message}\n";
        return;
    }

    $failed++;
    echo "FAILED: {$message}\n";
}

/**
 * Create a controller using the real application dependencies.
 */
function makeController(): SubjectController
{
    $views = new ViewFactory(__DIR__ . '/app/Views');
    $session = makeTestSession();
    $repository = new SubjectRepository();

    return new SubjectController(
        $views,
        $session,
        $repository
    );
}

/**
 * Create a clean test session.
 */
function makeTestSession(): SessionManager
{
    $session = new SessionManager();

    if (session_status() !== PHP_SESSION_ACTIVE) {
        $session->start();
    }

    $session->flush();

    return $session;
}

/**
 * Authenticate the test session as a specific role.
 */
function authenticateAs(
    SessionManager $session,
    int $userId,
    int $roleId
): void {
    $session->put('user_id', $userId);
    $session->put('role_id', $roleId);
}

/**
 * Build a Request using the real Request::capture() implementation.
 *
 * The Request class keeps a private static instance, so reset it
 * between test requests.
 */
function makeRequest(
    string $method,
    array $post = [],
    string $uri = '/SchoolERP/public/subjects'
): Request {
    $_GET = [];
    $_POST = $post;
    $_FILES = [];
    $_COOKIE = [];

    $_SERVER['REQUEST_METHOD'] = strtoupper($method);
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/SchoolERP/public/index.php';

    $reflection = new ReflectionClass(Request::class);

    $property = $reflection->getProperty('instance');
    $property->setAccessible(true);
    $property->setValue(null);

    return Request::capture();
}

/**
 * Get HTTP response status using the real Response API.
 */
function hasStatus(
    mixed $response,
    int $status
): bool {
    return $response->getStatus() === $status;
}

/**
 * Run one controller operation and verify its response status.
 */
function assertForbidden(
    SubjectController $controller,
    string $operation,
    callable $callback
): void {
    $response = $callback();

    assertTrue(
        hasStatus($response, 403),
        "{$operation} is forbidden for this role"
    );
}

/*
|--------------------------------------------------------------------------
| Test 1: Unauthenticated users
|--------------------------------------------------------------------------
*/

$session = makeTestSession();
$controller = makeController();

assertForbidden(
    $controller,
    'Subject index',
    fn () => $controller->index()
);

assertForbidden(
    $controller,
    'Subject create',
    fn () => $controller->create()
);

assertForbidden(
    $controller,
    'Subject store',
    fn () => $controller->store(
        makeRequest(
            'POST',
            [
                'name' => 'Authorization Test Subject',
                'code' => 'AUTH-TEST',
                'description' => 'Authorization test subject.',
            ],
            '/SchoolERP/public/subjects'
        )
    )
);

assertForbidden(
    $controller,
    'Subject edit',
    fn () => $controller->edit(0)
);

assertForbidden(
    $controller,
    'Subject update',
    fn () => $controller->update(
        makeRequest(
            'POST',
            [
                'name' => 'Authorization Test Subject',
                'code' => 'AUTH-TEST',
                'description' => 'Authorization test subject.',
            ],
            '/SchoolERP/public/subjects/0/update'
        ),
        0
    )
);

assertForbidden(
    $controller,
    'Subject activate',
    fn () => $controller->activate(0)
);

assertForbidden(
    $controller,
    'Subject deactivate',
    fn () => $controller->deactivate(0)
);

/*
|--------------------------------------------------------------------------
| Test 2: Administrator can reach protected resources
|--------------------------------------------------------------------------
|
| We use an invalid subject ID for resource-specific operations so the
| controller returns 404 instead of rendering a real edit page or
| modifying database records.
|--------------------------------------------------------------------------
*/

$session = makeTestSession();
authenticateAs($session, 1, 1);

$controller = new SubjectController(
    new ViewFactory(__DIR__ . '/app/Views'),
    $session,
    new SubjectRepository()
);

$response = $controller->edit(0);

assertTrue(
    hasStatus($response, 404),
    'Administrator can reach Subject edit authorization boundary'
);

$response = $controller->update(
    makeRequest(
        'POST',
        [
            'name' => 'Authorization Test Subject',
            'code' => 'AUTH-TEST',
            'description' => 'Authorization test subject.',
        ],
        '/SchoolERP/public/subjects/0/update'
    ),
    0
);

assertTrue(
    hasStatus($response, 404),
    'Administrator can reach Subject update authorization boundary'
);

$response = $controller->activate(0);

assertTrue(
    hasStatus($response, 404),
    'Administrator can reach Subject activate authorization boundary'
);

$response = $controller->deactivate(0);

assertTrue(
    hasStatus($response, 404),
    'Administrator can reach Subject deactivate authorization boundary'
);

/*
|--------------------------------------------------------------------------
| Test 3: Non-administrator roles
|--------------------------------------------------------------------------
*/

$roles = [
    2 => 'Teacher',
    3 => 'Student',
    4 => 'Parent',
    5 => 'Accountant',
    6 => 'Librarian',
];

foreach ($roles as $roleId => $roleName) {
    $session = makeTestSession();

    authenticateAs(
        $session,
        9000 + $roleId,
        $roleId
    );

    $controller = new SubjectController(
        new ViewFactory(__DIR__ . '/app/Views'),
        $session,
        new SubjectRepository()
    );

    $response = $controller->index();

    assertTrue(
        hasStatus($response, 403),
        "{$roleName} cannot access Subject list"
    );

    $response = $controller->create();

    assertTrue(
        hasStatus($response, 403),
        "{$roleName} cannot access Subject create form"
    );

    $response = $controller->store(
        makeRequest(
            'POST',
            [
                'name' => 'Authorization Test Subject',
                'code' => 'AUTH-TEST',
                'description' => 'Authorization test subject.',
            ],
            '/SchoolERP/public/subjects'
        )
    );

    assertTrue(
        hasStatus($response, 403),
        "{$roleName} cannot store Subject"
    );

    $response = $controller->edit(0);

    assertTrue(
        hasStatus($response, 403),
        "{$roleName} cannot edit Subject"
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            [
                'name' => 'Authorization Test Subject',
                'code' => 'AUTH-TEST',
                'description' => 'Authorization test subject.',
            ],
            '/SchoolERP/public/subjects/0/update'
        ),
        0
    );

    assertTrue(
        hasStatus($response, 403),
        "{$roleName} cannot update Subject"
    );

    $response = $controller->activate(0);

    assertTrue(
        hasStatus($response, 403),
        "{$roleName} cannot activate Subject"
    );

    $response = $controller->deactivate(0);

    assertTrue(
        hasStatus($response, 403),
        "{$roleName} cannot deactivate Subject"
    );
}

/*
|--------------------------------------------------------------------------
| Test 4: Administrator receives 404 for invalid subject IDs
|--------------------------------------------------------------------------
*/

$invalidIds = [
    0,
    -1,
    999999999,
];

foreach ($invalidIds as $id) {
    $session = makeTestSession();

    authenticateAs($session, 1, 1);

    $controller = new SubjectController(
        new ViewFactory(__DIR__ . '/app/Views'),
        $session,
        new SubjectRepository()
    );

    $response = $controller->edit($id);

    assertTrue(
        hasStatus($response, 404),
        "Administrator receives 404 for invalid Subject edit ID {$id}"
    );

    $response = $controller->update(
        makeRequest(
            'POST',
            [
                'name' => 'Authorization Test Subject',
                'code' => 'AUTH-TEST',
                'description' => 'Authorization test subject.',
            ],
            "/SchoolERP/public/subjects/{$id}/update"
        ),
        $id
    );

    assertTrue(
        hasStatus($response, 404),
        "Administrator receives 404 for invalid Subject update ID {$id}"
    );

    $response = $controller->activate($id);

    assertTrue(
        hasStatus($response, 404),
        "Administrator receives 404 for invalid Subject activate ID {$id}"
    );

    $response = $controller->deactivate($id);

    assertTrue(
        hasStatus($response, 404),
        "Administrator receives 404 for invalid Subject deactivate ID {$id}"
    );
}

/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

echo PHP_EOL;
echo "PASSED: {$passed}\n";
echo "FAILED: {$failed}\n";

if ($failed > 0) {
    echo "SUBJECT AUTHORIZATION TEST FAILED\n";
    exit(1);
}

echo "SUBJECT AUTHORIZATION TEST PASSED\n";
exit(0);