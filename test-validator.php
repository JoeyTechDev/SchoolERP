<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Validation\Validator;

echo "VALIDATOR TEST\n";
echo "==============\n\n";

$tests = [
    [
        'name' => 'Valid student',
        'data' => [
            'first_name' => 'Joey',
            'last_name' => 'Cracky',
            'classroom_id' => '1',
        ],
        'expected' => true,
    ],
    [
        'name' => 'First name too short',
        'data' => [
            'first_name' => 'J',
            'last_name' => 'Cracky',
            'classroom_id' => '1',
        ],
        'expected' => false,
    ],
    [
        'name' => 'Last name too short',
        'data' => [
            'first_name' => 'Joey',
            'last_name' => 'C',
            'classroom_id' => '1',
        ],
        'expected' => false,
    ],
    [
        'name' => 'Classroom zero',
        'data' => [
            'first_name' => 'Joey',
            'last_name' => 'Cracky',
            'classroom_id' => '0',
        ],
        'expected' => false,
    ],
    [
        'name' => 'Negative classroom',
        'data' => [
            'first_name' => 'Joey',
            'last_name' => 'Cracky',
            'classroom_id' => '-1',
        ],
        'expected' => false,
    ],
    [
        'name' => 'Decimal classroom',
        'data' => [
            'first_name' => 'Joey',
            'last_name' => 'Cracky',
            'classroom_id' => '1.5',
        ],
        'expected' => false,
    ],
    [
        'name' => 'Empty classroom',
        'data' => [
            'first_name' => 'Joey',
            'last_name' => 'Cracky',
            'classroom_id' => '',
        ],
        'expected' => true,
    ],
];

foreach ($tests as $test) {
    $validator = Validator::make(
        $test['data'],
        [
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'classroom_id' => 'nullable|integer|min:1',
        ]
    );

    $passed = $validator->validate();

    $status = $passed === $test['expected']
        ? 'PASSED'
        : 'FAILED';

    echo $test['name']
        . ': '
        . $status
        . PHP_EOL;

    if (!$passed) {
        foreach ($validator->errors() as $field => $errors) {
            foreach ($errors as $error) {
                echo "  - {$error}" . PHP_EOL;
            }
        }
    }

    echo PHP_EOL;
}

echo "VALIDATOR TEST COMPLETE\n";