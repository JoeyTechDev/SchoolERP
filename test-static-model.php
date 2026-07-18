<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "Static ORM Test\n";
echo "===============\n\n";

print_r(
    Student::all()
);

echo "\n";

print_r(
    Student::find(1)
);

echo "\n";

print_r(

    Student::where('id', '>', 1)
        ->orderBy('first_name')
        ->limit(2)
        ->get()

);
