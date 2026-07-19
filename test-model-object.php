<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "OBJECT ORM TEST\n";
echo "===============\n\n";

$student = (new Student())->find(11);

echo $student->first_name . PHP_EOL;
echo $student->last_name . PHP_EOL;

$student->last_name = "Williams";

echo PHP_EOL;
echo "Updated Name:\n";

echo $student->last_name . PHP_EOL;