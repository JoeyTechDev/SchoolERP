<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "RELATIONSHIP TEST\n";
echo "=================\n\n";

$student = (new Student())->find(1);

echo "Student\n";
print_r($student->toArray());

echo PHP_EOL;
echo "Classroom\n";

$classroom = $student
    ->classroom()
    ->get();

if ($classroom !== null) {
    print_r($classroom->toArray());
}

echo PHP_EOL;
echo "Results\n";

$results = $student
    ->results()
    ->get();

print_r($results);