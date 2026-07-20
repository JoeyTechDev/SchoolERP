<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Repositories\StudentRepository;

echo "REPOSITORY TEST\n";
echo "===============\n\n";

$repository = new StudentRepository();

echo "Find Student\n";
print_r(
    $repository
        ->find(1)
        ?->toArray()
);

echo PHP_EOL;

echo "Students in Classroom 1\n";
print_r(
    $repository
        ->inClassroom(1)
);

echo PHP_EOL;

echo "Pagination\n";

$page = $repository->paginate(1, 2);

echo "Current Page: {$page->currentPage()}\n";
echo "Total: {$page->total()}\n";

print_r($page->items());