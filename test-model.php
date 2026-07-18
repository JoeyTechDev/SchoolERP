<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "Model Test\n";
echo "==========\n\n";

$student = new Student();

print_r(
    $student
        ->query()
        ->where('id', '>', 1)
        ->get()
);

echo PHP_EOL;

print_r(
    $student
        ->query()
        ->where('id', '=', 2)
        ->first()
);