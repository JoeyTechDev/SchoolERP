<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

echo "=====================================\n";
echo "DISTINCT Test\n";
echo "=====================================\n\n";

$result = $query
    ->table('students')
    ->distinct()
    ->select(['class_id'])
    ->get();

print_r($result);