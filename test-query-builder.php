<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use ReflectionClass;
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

$query->table('students');

echo "Current Table:" . PHP_EOL;
echo $query->getTable() . PHP_EOL . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Test get()
|--------------------------------------------------------------------------
*/

echo "Executing get()..." . PHP_EOL;

$result = $query->get();

print_r($result);