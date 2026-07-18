<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

$student = new Student();

$affected = $student
    ->query()
    ->where('id', '=', 7)
    ->update([
        'first_name' => 'Johnny'
    ]);

echo "Updated: {$affected}\n\n";

print_r(
    $student->find(7)
);