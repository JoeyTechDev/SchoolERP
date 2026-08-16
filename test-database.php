<?php

declare(strict_types=1);

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;

require __DIR__ . '/vendor/autoload.php';

echo "DATABASE QUERY TEST\n";
echo "===================\n\n";

$config = new Config(
    __DIR__ . '/config'
);

$db = new Database($config);

echo "Database connection: ";

$connection = $db->connection();

echo get_class($connection) . PHP_EOL;

echo "\nTesting SELECT query...\n";

$students = $db->select(
    'SELECT id, first_name, last_name, classroom_id
     FROM students
     ORDER BY id DESC
     LIMIT 10'
);

echo "Query executed successfully.\n\n";

echo "Students found: " . count($students) . "\n\n";

foreach ($students as $student) {

    echo sprintf(
        "#%s - %s %s - Classroom: %s",
        $student['id'],
        $student['first_name'],
        $student['last_name'],
        $student['classroom_id'] ?? 'NULL'
    );

    echo PHP_EOL;
}

echo "\nDATABASE QUERY TEST PASSED\n";