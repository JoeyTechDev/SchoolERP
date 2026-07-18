<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

$student = new Student();

$deleted = $student
    ->query()
    ->where('id', '=', 7)
    ->delete();

echo "Deleted: {$deleted}\n";