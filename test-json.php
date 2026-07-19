<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

$student = (new Student())->find(1);

echo $student->toJson();

echo PHP_EOL . PHP_EOL;

echo json_encode($student, JSON_PRETTY_PRINT);