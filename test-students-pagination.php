<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap/app.php';

use SchoolERP\Repositories\StudentRepository;

echo "STUDENT PAGINATION TEST\n";
echo "=======================\n\n";

$repository = new StudentRepository();

$pagination = $repository->paginate(3, 10);

echo "Total: " . $pagination->total() . PHP_EOL;
echo "Current Page: " . $pagination->currentPage() . PHP_EOL;
echo "Per Page: " . $pagination->perPage() . PHP_EOL;
echo "Last Page: " . $pagination->lastPage() . PHP_EOL;
echo "Items: " . count($pagination->items()) . PHP_EOL;

echo "\n-----------------------\n";

foreach ($pagination->items() as $student) {
    echo sprintf(
        "#%d - %s %s - Classroom: %s\n",
        $student['id'],
        $student['first_name'],
        $student['last_name'],
        $student['classroom_id'] ?? 'None'
    );
}

echo "\n-----------------------\n";
echo "PAGINATION TEST COMPLETE\n";