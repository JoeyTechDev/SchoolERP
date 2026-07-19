<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "LOCAL SCOPE TEST\n";
echo "================\n\n";

$students = (new Student())
    ->query()
    ->inClassroom(1)
    ->get();

print_r($students);