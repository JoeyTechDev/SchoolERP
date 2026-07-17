<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "Query Builder EXISTS Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

$result = $query
    ->table('students')
    ->where('first_name', '=', 'Joey')
    ->exists();

echo "Joey Exists? ";

var_dump($result);

$result = $query
    ->table('students')
    ->where('first_name', '=', 'Unknown Student')
    ->exists();

echo "Unknown Exists? ";

var_dump($result);