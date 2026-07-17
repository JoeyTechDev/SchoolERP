<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "Query Builder WHERE BETWEEN Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

$result = $query
    ->table('students')
    ->select([
        'id',
        'first_name',
    ])
    ->whereBetween('id', [1, 2])
    ->orderBy('id')
    ->get();

print_r($result);