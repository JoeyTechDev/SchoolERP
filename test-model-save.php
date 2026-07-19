<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "SAVE TEST\n";
echo "=========\n\n";

// Load student
$student = (new Student())->find(11);

if ($student === null) {
    die("Student not found.\n");
}

echo "Before:\n";

print_r($student->toArray());

// Change one field
$student->last_name = 'Framework Saved';

// Save it
$student->save();

echo PHP_EOL;
echo "After:\n";

// Reload from database
$student = (new Student())->find(11);

print_r($student->toArray());