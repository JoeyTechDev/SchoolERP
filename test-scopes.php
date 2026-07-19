<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "LOCAL SCOPE TEST\n";
echo "================\n\n";

$student = new Student();

$students = $student
    ->scope('inClassroom', 1)
    ->query()
    ->get();

print_r($students);