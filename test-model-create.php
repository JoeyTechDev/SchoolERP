<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "CREATE TEST\n";
echo "===========\n\n";

$student = new Student();

$id = $student->create([
    'first_name' => 'John',
    'last_name'  => 'Doe'
]);

echo "Inserted ID: {$id}\n\n";

print_r(
    $student->find($id)
);