<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;

echo "=====================================" . PHP_EOL;
echo "SchoolERP Config Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$config = new Config(__DIR__ . '/config');

echo "App Name:" . PHP_EOL;
echo $config->get('app.name') . PHP_EOL;
echo PHP_EOL;

echo "Environment:" . PHP_EOL;
echo $config->get('app.env') . PHP_EOL;
echo PHP_EOL;

echo "Database Host:" . PHP_EOL;
echo $config->get('database.mysql.host') . PHP_EOL;
echo PHP_EOL;

echo "Database Name:" . PHP_EOL;
echo $config->get('database.mysql.database') . PHP_EOL;
echo PHP_EOL;

echo "Missing Key (Default Value):" . PHP_EOL;
var_dump(
    $config->get(
        'database.mysql.socket',
        'Not Found'
    )
);