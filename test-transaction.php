<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Database\Database;
use SchoolERP\Providers\AppServiceProvider;

echo "=====================================" . PHP_EOL;
echo "SchoolERP Transaction Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$container = new Container();

$provider = new AppServiceProvider($container);
$provider->register();

$db = $container->get(Database::class);

$db->delete("DELETE FROM framework_test");

$db->beginTransaction();

try {

    $db->insert(
        "INSERT INTO framework_test (name) VALUES (?)",
        ['Transaction Test']
    );

    echo "Inside Transaction:" . PHP_EOL;
    print_r(
        $db->select(
            "SELECT * FROM framework_test"
        )
    );

    $db->commit();

    echo PHP_EOL;
    echo "Transaction Committed!" . PHP_EOL;

} catch (Throwable $e) {

    $db->rollBack();

    echo $e->getMessage();
}