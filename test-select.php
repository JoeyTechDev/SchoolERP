<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Database\Database;
use SchoolERP\Providers\AppServiceProvider;

echo "=====================================" . PHP_EOL;
echo "SchoolERP SELECT Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$container = new Container();

$provider = new AppServiceProvider($container);

$provider->register();

$db = $container->get(Database::class);

$result = $db->select("SELECT 1 AS number");

print_r($result);