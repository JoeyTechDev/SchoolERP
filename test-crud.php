<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Database\Database;
use SchoolERP\Providers\AppServiceProvider;

echo "=====================================" . PHP_EOL;
echo "SchoolERP CRUD Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$container = new Container();

$provider = new AppServiceProvider($container);
$provider->register();

$db = $container->get(Database::class);

/*
|--------------------------------------------------------------------------
| Clean table
|--------------------------------------------------------------------------
*/

$db->delete("DELETE FROM framework_test");

/*
|--------------------------------------------------------------------------
| Insert
|--------------------------------------------------------------------------
*/

$success = $db->insert(
    "INSERT INTO framework_test (name) VALUES (?)",
    ['Joey']
);

echo "Insert: ";
var_dump($success);

echo "Last ID: ";
echo $db->lastInsertId();
echo PHP_EOL . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Select
|--------------------------------------------------------------------------
*/

print_r(
    $db->select(
        "SELECT * FROM framework_test"
    )
);

/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

echo PHP_EOL;

$count = $db->update(
    "UPDATE framework_test SET name=? WHERE id=?",
    ['SchoolERP', 1]
);

echo "Updated Rows: ";
echo $count;
echo PHP_EOL . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

$count = $db->delete(
    "DELETE FROM framework_test WHERE id=?",
    [1]
);

echo "Deleted Rows: ";
echo $count;
echo PHP_EOL;