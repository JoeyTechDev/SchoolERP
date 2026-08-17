<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use SchoolERP\Models\Student;

echo "WHERE LIKE TEST\n";
echo "===============\n\n";

$students = (new Student())
    ->whereLike('first_name', '%Joey%')
    ->get();

echo "Students matching 'Joey': " . count($students) . "\n\n";

foreach ($students as $student) {
    echo sprintf(
        "#%d - %s %s - Classroom: %s\n",
        $student['id'],
        $student['first_name'],
        $student['last_name'],
        $student['classroom_id'] ?? 'None'
    );
}

echo "\nWHERE LIKE TEST COMPLETE\n";