<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "Query Builder UPDATE Test\n";
echo "=====================================\n\n";

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

$query = new QueryBuilder($database);

/*
|--------------------------------------------------------------------------
| Insert a temporary record
|--------------------------------------------------------------------------
*/

$id = $query
    ->table('students')
    ->insert([
        'first_name' => 'Old',
        'last_name'  => 'Name',
    ]);

echo "Inserted ID: {$id}\n\n";

/*
|--------------------------------------------------------------------------
| Update it
|--------------------------------------------------------------------------
*/

$affected = $query
    ->table('students')
    ->where('id', '=', $id)
    ->update([
        'first_name' => 'New',
        'last_name'  => 'Student',
    ]);

echo "Updated Rows: {$affected}\n\n";

/*
|--------------------------------------------------------------------------
| Verify
|--------------------------------------------------------------------------
*/

$result = $query
    ->table('students')
    ->where('id', '=', $id)
    ->first();

print_r($result);

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

$database->delete(
    'DELETE FROM students WHERE id = ?',
    [$id]
);

echo "\nTemporary record deleted.\n";