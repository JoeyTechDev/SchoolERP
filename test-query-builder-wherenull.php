<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "WHERE NULL Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');
$database = new Database($config);
$query = new QueryBuilder($database);

$result = $query
    ->table('null_test')
    ->select([
        'id',
        'name',
        'email',
    ])
    ->whereNull('email')
    ->get();

print_r($result);