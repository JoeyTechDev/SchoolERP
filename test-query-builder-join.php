<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "INNER JOIN Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

$result = $query
    ->table('students_join')
    ->join(
        'classes',
        'students_join.class_id',
        '=',
        'classes.id'
    )
    ->select([
        'students_join.first_name',
        'classes.name'
    ])
    ->get();

print_r($result);