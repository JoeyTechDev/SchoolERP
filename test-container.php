<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Container\Container;

echo "=====================================\n";
echo "SchoolERP Container Test Suite\n";
echo "=====================================\n\n";

$container = new Container();

/*
|--------------------------------------------------------------------------
| Test 1: bind() + make()
|--------------------------------------------------------------------------
*/

echo "Test 1: bind() + make()\n";

class TestService
{
    public function hello(): string
    {
        return 'Hello World';
    }
}

$container->bind(TestService::class);

$service = $container->make(TestService::class);

echo $service->hello() . PHP_EOL;

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 2: has()
|--------------------------------------------------------------------------
*/

echo "Test 2: has()\n";

var_dump(
    $container->has(TestService::class)
);

var_dump(
    $container->has('UnknownService')
);

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 3: instance()
|--------------------------------------------------------------------------
*/

echo "Test 3: instance()\n";

$instance = new TestService();

$container->instance(
    'shared',
    $instance
);

$shared = $container->make('shared');

var_dump($shared === $instance);

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 4: alias()
|--------------------------------------------------------------------------
*/

echo "Test 4: alias()\n";

$container->alias(
    'service',
    TestService::class
);

$alias = $container->make('service');

echo $alias->hello() . PHP_EOL;

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 5: get()
|--------------------------------------------------------------------------
*/

echo "Test 5: get()\n";

$get = $container->get(TestService::class);

echo $get->hello() . PHP_EOL;

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 6: clear()
|--------------------------------------------------------------------------
*/

echo "Test 6: clear()\n";

$container->clear();

var_dump(
    $container->has(TestService::class)
);

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 7: Exception
|--------------------------------------------------------------------------
*/

echo "Test 7: Exception\n";

try {

    $container->make('DoesNotExist');

} catch (Throwable $e) {

    echo get_class($e) . PHP_EOL;

    echo $e->getMessage() . PHP_EOL;
}

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 8: singleton()
|--------------------------------------------------------------------------
*/

echo "Test 8: singleton()\n";

$container = new Container();

$container->singleton(TestService::class);

$one = $container->make(TestService::class);

$two = $container->make(TestService::class);

var_dump($one === $two);

echo "\n";

/*
|--------------------------------------------------------------------------
| Test 9: Reflection Auto Resolution
|--------------------------------------------------------------------------
*/

echo "Test 9: Reflection Auto Resolution\n";

class AutoResolvedService
{
    public function hello(): string
    {
        return 'Auto Resolution Works!';
    }
}

$container = new Container();

$service = $container->make(AutoResolvedService::class);

echo $service->hello() . PHP_EOL;

echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| Test 10: Constructor Dependency Injection
|--------------------------------------------------------------------------
*/

echo "Test 10: Constructor Dependency Injection" . PHP_EOL;

class Logger
{
    public function name(): string
    {
        return "Logger";
    }
}

class UserRepository
{
    public function __construct(
        public Logger $logger
    ) {
    }
}

class UserService
{
    public function __construct(
        public UserRepository $repository
    ) {
    }
}

$container = new Container();

$service = $container->make(UserService::class);

echo $service->repository->logger->name() . PHP_EOL;