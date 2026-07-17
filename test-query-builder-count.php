<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "Query Builder COUNT Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

$count = $query
    ->table('students')
    ->where('id', '>', 1)
    ->count();

echo "Students with ID > 1: {$count}\n";

echo "Total Students: {$count}\n";