<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Database\Database;
use SchoolERP\Providers\AppServiceProvider;
use SchoolERP\Query\QueryBuilder;

echo "=====================================" . PHP_EOL;
echo "Query Builder Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$container = new Container();

$provider = new AppServiceProvider($container);
$provider->register();

$database = $container->get(Database::class);

$query = new QueryBuilder($database);

$query->table('students');

echo "Current Table:" . PHP_EOL;
echo $query->getTable() . PHP_EOL;