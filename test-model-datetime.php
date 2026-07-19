<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "DATETIME CAST TEST\n";
echo "==================\n\n";

$student = (new Student())->find(1);

var_dump($student->created_at);

echo PHP_EOL;

var_dump(get_class($student->created_at));

echo PHP_EOL;

echo $student->created_at->format('Y-m-d H:i:s');