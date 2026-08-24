<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\ClassroomRepository;

$repository = new ClassroomRepository();

echo "CLASSROOM REPOSITORY TEST\n";
echo "=========================\n\n";

echo "All Classrooms\n";

$classrooms = $repository->allOrdered();

foreach ($classrooms as $classroom) {
    echo sprintf(
        "#%d - %s\n",
        $classroom['id'],
        $classroom['name']
    );
}

echo "\n";

echo "Find Classroom ID 1\n";

$classroom = $repository->find(1);

if ($classroom === null) {
    echo "Classroom not found.\n";
} else {
    echo sprintf(
        "ID: %d\n",
        $classroom->id
    );

    echo sprintf(
        "Name: %s\n",
        $classroom->name
    );
}

echo "\n";
echo "CLASSROOM REPOSITORY TEST COMPLETE\n";