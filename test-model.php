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

echo "\nExists Test\n";
echo "===========\n\n";

var_dump(

    $student
        ->query()
        ->where('id', '=', 2)
        ->exists()

);

echo "\nCreate Test\n";
echo "===========\n\n";

$id = $student->create([
    'first_name' => 'Alice',
    'last_name'  => 'Brown'
]);

echo "Inserted ID: {$id}\n";
