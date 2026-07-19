<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "CAST TEST\n";
echo "=========\n\n";

$student = (new Student())->find(1);

var_dump($student->id);

var_dump(gettype($student->id));