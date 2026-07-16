<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;

echo "=====================================" . PHP_EOL;
echo "SchoolERP Database Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$config = new Config(__DIR__ . '/config');

$db = new Database($config);

try {

    $pdo = $db->connection();

    echo "Connection Successful!" . PHP_EOL;
    echo PHP_EOL;

    echo "Driver: ";
    echo $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo PHP_EOL;

    echo "Server Version: ";
    echo $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    echo PHP_EOL;

} catch (Throwable $e) {

    echo "Connection Failed!" . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}