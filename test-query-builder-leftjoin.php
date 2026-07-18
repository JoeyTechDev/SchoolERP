<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "LEFT JOIN Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');
$database = new Database($config);
$query = new QueryBuilder($database);

$result = $query
    ->table('classes')
    ->leftJoin(
        'students_join',
        'classes.id',
        '=',
        'students_join.class_id'
    )
    ->select([
        'classes.name',
        'students_join.first_name'
    ])
    ->orderBy('classes.id')
    ->get();

print_r($result);