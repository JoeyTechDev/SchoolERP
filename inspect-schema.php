<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;

$config = new Config(
    __DIR__ . '/config'
);

$db = new Database($config);

foreach ([
    'classrooms',
    'school_classes',
    'students',
    'results',
] as $table) {

    echo PHP_EOL;
    echo "TABLE: {$table}" . PHP_EOL;
    echo str_repeat('-', 40) . PHP_EOL;

    try {
        print_r(
            $db->select("DESCRIBE {$table}")
        );
    } catch (\Throwable $e) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL;
