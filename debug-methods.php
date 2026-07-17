<?php

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

$config = new Config(__DIR__ . '/config');
$db = new Database($config);

$query = new QueryBuilder($db);

echo "Methods:\n\n";

print_r(get_class_methods($query));