<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

try {

    $query
        ->table('students')
        ->delete();

} catch (Throwable $e) {

    echo get_class($e) . PHP_EOL;

    echo $e->getMessage() . PHP_EOL;

}