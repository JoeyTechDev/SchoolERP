<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Database\Database;
use SchoolERP\Providers\AppServiceProvider;

echo "=====================================" . PHP_EOL;
echo "Database Container Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$container = new Container();

$provider = new AppServiceProvider($container);

$provider->register();

$db = $container->get(Database::class);

$pdo = $db->connection();

echo "Connected!" . PHP_EOL;
echo PHP_EOL;

echo "Driver: ";
echo $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
echo PHP_EOL;

echo "Database: school_erp" . PHP_EOL;