<?php

declare(strict_types=1);

ob_start();

require __DIR__ . '/vendor/autoload.php';

use SchoolERP\Session\SessionManager;

$session = new SessionManager();

$results = [];

/*
|--------------------------------------------------------------------------
| Start Session
|--------------------------------------------------------------------------
*/

$session->start();

$results[] = [
    'Session Started',
    $session->isStarted()
];

/*
|--------------------------------------------------------------------------
| Put
|--------------------------------------------------------------------------
*/

$session->put('framework', 'SchoolERP');

$results[] = [
    'Put Test',
    $session->get('framework') === 'SchoolERP'
];

/*
|--------------------------------------------------------------------------
| Has
|--------------------------------------------------------------------------
*/

$results[] = [
    'Has Test',
    $session->has('framework')
];

/*
|--------------------------------------------------------------------------
| Forget
|--------------------------------------------------------------------------
*/

$session->forget('framework');

$results[] = [
    'Forget Test',
    !$session->has('framework')
];

/*
|--------------------------------------------------------------------------
| Flush
|--------------------------------------------------------------------------
*/

$session->put('a', 1);
$session->put('b', 2);

$session->flush();

$results[] = [
    'Flush Test',
    count($session->all()) === 0
];

/*
|--------------------------------------------------------------------------
| Regenerate
|--------------------------------------------------------------------------
*/

$results[] = [
    'Regenerate Test',
    $session->regenerate()
];

/*
|--------------------------------------------------------------------------
| Destroy
|--------------------------------------------------------------------------
*/

$results[] = [
    'Destroy Test',
    $session->destroy()
];

ob_end_clean();

/*
|--------------------------------------------------------------------------
| Display Results
|--------------------------------------------------------------------------
*/

echo PHP_EOL;
echo "SESSION TEST" . PHP_EOL;
echo "============" . PHP_EOL . PHP_EOL;

foreach ($results as [$name, $passed]) {
    echo $name . ': ' . ($passed ? 'PASSED' : 'FAILED') . PHP_EOL;
}

echo PHP_EOL;