<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "FILLABLE TEST\n";
echo "=============\n\n";

$id = (new Student())->create([
    'first_name' => 'Alice',
    'last_name'  => 'Johnson',
    'is_admin'   => true,
]);

$student = (new Student())->find($id);

print_r($student->toArray());