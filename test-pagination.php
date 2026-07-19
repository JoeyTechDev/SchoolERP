<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Models\Student;

echo "PAGINATION TEST\n";
echo "===============\n\n";

$paginator = (new Student())
    ->query()
    ->paginate(2, 1);

echo "Current Page: ";
echo $paginator->currentPage() . PHP_EOL;

echo "Per Page: ";
echo $paginator->perPage() . PHP_EOL;

echo "Total Records: ";
echo $paginator->total() . PHP_EOL;

echo "Last Page: ";
echo $paginator->lastPage() . PHP_EOL;

echo PHP_EOL;
echo "Records\n";
print_r($paginator->items());