<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\SubjectRepository;

$repository = new SubjectRepository();

echo "SUBJECT REPOSITORY TEST\n";
echo "=======================\n\n";

echo "All Subjects\n";

$subjects = $repository->allOrdered();

foreach ($subjects as $subject) {
    echo sprintf(
        "#%d - %s [%s] - %s\n",
        $subject['id'],
        $subject['name'],
        $subject['code'] ?? 'NO CODE',
        $subject['status']
    );
}

echo PHP_EOL;

echo "Active Subjects\n";

$active = $repository->active();

foreach ($active as $subject) {
    echo sprintf(
        "#%d - %s\n",
        $subject['id'],
        $subject['name']
    );
}

echo PHP_EOL;

$subject = $repository->find(1);

if ($subject === null) {
    echo "Find Subject: FAILED\n";
} else {
    echo "Find Subject: PASSED\n";
    echo 'ID: ' . $subject->id . PHP_EOL;
    echo 'Name: ' . $subject->name . PHP_EOL;
    echo 'Code: ' . $subject->code . PHP_EOL;
    echo 'Status: ' . $subject->status . PHP_EOL;
}

echo PHP_EOL;
echo "SUBJECT REPOSITORY TEST COMPLETE\n";