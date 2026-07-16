<?php

declare(strict_types=1);

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

echo "=====================================\n";
echo "Query Builder INSERT Test\n";
echo "=====================================\n\n";

$config = new Config(
    __DIR__ . '/config'
);

$database = new Database($config);

$query = new QueryBuilder($database);

/*
|--------------------------------------------------------------------------
| Insert
|--------------------------------------------------------------------------
*/

$id = $query
    ->table('students')
    ->insert([
        'first_name' => 'Framework',
        'last_name'  => 'Test',
    ]);

echo "Inserted ID: {$id}\n\n";

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