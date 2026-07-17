<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "Aggregate Function Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');
$database = new Database($config);
$query = new QueryBuilder($database);

echo "COUNT : " . $query->table('aggregate_test')->count() . PHP_EOL;
echo "SUM   : " . $query->table('aggregate_test')->sum('score') . PHP_EOL;
echo "AVG   : " . $query->table('aggregate_test')->avg('score') . PHP_EOL;
echo "MIN   : " . $query->table('aggregate_test')->min('score') . PHP_EOL;
echo "MAX   : " . $query->table('aggregate_test')->max('score') . PHP_EOL;