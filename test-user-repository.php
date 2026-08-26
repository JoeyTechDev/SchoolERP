<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\UserRepository;

$repository = new UserRepository();

echo "USER REPOSITORY TEST\n";
echo "====================\n\n";

echo "Searching for an active user...\n";

$user = $repository->findActiveByEmail(
    'admin@schoolerp.com'
);

if ($user === null) {
    echo "No user found for that email.\n";
} else {
    echo "User found:\n";
    echo 'ID: ' . $user->id . PHP_EOL;
    echo 'Name: '
        . $user->first_name
        . ' '
        . $user->last_name
        . PHP_EOL;
    echo 'Email: ' . $user->email . PHP_EOL;
    echo 'Role ID: ' . $user->role_id . PHP_EOL;
    echo 'Status: ' . $user->status . PHP_EOL;
}

echo "\nUSER REPOSITORY TEST COMPLETE\n";