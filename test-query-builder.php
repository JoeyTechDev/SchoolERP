<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Database\Database;
use SchoolERP\Providers\AppServiceProvider;
use SchoolERP\Query\QueryBuilder;

echo "=====================================" . PHP_EOL;
echo "Query Builder Debug Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| Boot the Container
|--------------------------------------------------------------------------
*/

$container = new Container();

$provider = new AppServiceProvider($container);

$provider->register();

/*
|--------------------------------------------------------------------------
| Resolve Database
|--------------------------------------------------------------------------
*/

$database = $container->get(Database::class);

/*
|--------------------------------------------------------------------------
| Create Query Builder
|--------------------------------------------------------------------------
*/

$query = new QueryBuilder($database);

/*
|--------------------------------------------------------------------------
| Display Loaded Class
|--------------------------------------------------------------------------
*/

echo "Loaded Class:" . PHP_EOL;
echo get_class($query) . PHP_EOL . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Display Available Methods
|--------------------------------------------------------------------------
*/

echo "Available Methods:" . PHP_EOL;

$reflection = new ReflectionClass($query);

foreach ($reflection->getMethods() as $method) {
    echo "- " . $method->getName() . PHP_EOL;
}

echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| Test table()
|--------------------------------------------------------------------------
*/

$student = $query
    ->table('students')
    ->where('id', '=', 2)
    ->first();

print_r($student);