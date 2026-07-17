<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "Query Builder DELETE Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

/*
|--------------------------------------------------------------------------
| Insert temporary record
|--------------------------------------------------------------------------
*/

$id = $query
    ->table('students')
    ->insert([
        'first_name' => 'Delete',
        'last_name'  => 'Me',
    ]);

echo "Inserted ID: {$id}\n\n";

/*
|--------------------------------------------------------------------------
| Delete record
|--------------------------------------------------------------------------
*/

$deleted = $query
    ->table('students')
    ->where('id', '=', $id)
    ->delete();

echo "Deleted Rows: {$deleted}\n\n";

/*
|--------------------------------------------------------------------------
| Verify
|--------------------------------------------------------------------------
*/

$result = $query
    ->table('students')
    ->where('id', '=', $id)
    ->first();

var_dump($result);