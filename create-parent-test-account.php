<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Models\User;

const ROLE_PARENT = 4;

$email = 'parent.test@schoolerp.local';
$password = 'ParentTest#2026!';

try {
    // Use the project's existing User model.
    $users = new User();

    // Prevent duplicate accounts.
    $existingUser = $users
        ->query()
        ->where('email', '=', $email)
        ->first();

    if ($existingUser !== null) {
        if ((int) $existingUser->role_id !== ROLE_PARENT) {
            throw new RuntimeException(
                'The test email already belongs to a different role. '
                . 'Choose another email address.'
            );
        }

        echo "Parent test account already exists.\n";
        echo "User ID: " . (int) $existingUser->id . "\n";
        echo "Role ID: " . (int) $existingUser->role_id . "\n";
        exit(0);
    }

    // Create the parent account using the existing ORM model.
    $userId = $users->create([
        'role_id' => ROLE_PARENT,
        'first_name' => 'Test',
        'last_name' => 'Parent',
        'email' => $email,
        'password' => password_hash(
            $password,
            PASSWORD_DEFAULT
        ),
        'status' => 'active',
    ]);

    if ((int) $userId <= 0) {
        throw new RuntimeException(
            'The parent account was not created successfully.'
        );
    }

    echo "PARENT TEST ACCOUNT CREATED SUCCESSFULLY\n";
    echo "========================================\n";
    echo "User ID: " . (int) $userId . "\n";
    echo "Role ID: " . ROLE_PARENT . "\n";
    echo "Email: " . $email . "\n";
    echo "Temporary password: " . $password . "\n";
    echo "\n";
    echo "Password stored as a secure hash.\n";

} catch (Throwable $exception) {
    fwrite(
        STDERR,
        "ERROR: " . $exception->getMessage() . "\n"
    );

    exit(1);
}
