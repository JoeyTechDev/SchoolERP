<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;

$config = new Config(__DIR__ . '/config');

$database = new Database($config);

try {
    $pdo = $database->connection();

    echo "Database connection successful!" . PHP_EOL;
    echo "Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . PHP_EOL;
    echo "Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . PHP_EOL;

} catch (Throwable $e) {
    echo "Connection failed:" . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}