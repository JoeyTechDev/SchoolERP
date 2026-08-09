<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Validation\Validator;

echo "VALIDATOR TEST\n";
echo "==============\n\n";

function test(
    string $name,
    bool $result
): void {
    echo $name . ': ' . (
        $result ? 'PASSED' : 'FAILED'
    ) . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| Required Tests
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['name' => 'John'],
    ['name' => 'required']
);

test(
    'Present Test',
    $validator->validate()
);

$validator = Validator::make(
    [],
    ['name' => 'required']
);

test(
    'Missing Test',
    $validator->validate() === false
);

$validator = Validator::make(
    ['name' => ''],
    ['name' => 'required']
);

test(
    'Empty Test',
    $validator->validate() === false
);

$validator = Validator::make(
    ['name' => '   '],
    ['name' => 'required']
);

test(
    'Whitespace Test',
    $validator->validate() === false
);

/*
|--------------------------------------------------------------------------
| Required Error Message
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    [],
    ['email' => 'required']
);

$validator->validate();

$errors = $validator->errorsFor('email');

test(
    'Error Message Test',
    isset($errors[0])
    && $errors[0] ===
        'The email field is required.'
);

/*
|--------------------------------------------------------------------------
| Email Tests
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['email' => 'test@example.com'],
    ['email' => 'email']
);

test(
    'Valid Email Test',
    $validator->validate()
);

$validator = Validator::make(
    ['email' => 'not-an-email'],
    ['email' => 'email']
);

test(
    'Invalid Email Test',
    $validator->validate() === false
);

$validator = Validator::make(
    ['email' => ''],
    ['email' => 'email']
);

test(
    'Empty Email Test',
    $validator->validate()
);

$validator = Validator::make(
    ['email' => ''],
    ['email' => 'required|email']
);

test(
    'Required Email Test',
    $validator->validate() === false
);

/*
|--------------------------------------------------------------------------
| Email Error Message
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['email' => 'invalid-email'],
    ['email' => 'email']
);

$validator->validate();

$errors = $validator->errorsFor('email');

test(
    'Email Error Message Test',
    isset($errors[0])
    && $errors[0] ===
        'The email field must be a valid email address.'
);

/*
|--------------------------------------------------------------------------
| Min Tests
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['password' => '12345678'],
    ['password' => 'min:8']
);

test(
    'Min Length Pass Test',
    $validator->validate()
);

$validator = Validator::make(
    ['password' => '1234567'],
    ['password' => 'min:8']
);

test(
    'Min Length Fail Test',
    $validator->validate() === false
);

/*
|--------------------------------------------------------------------------
| Max Tests
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['username' => 'Joey'],
    ['username' => 'max:10']
);

test(
    'Max Length Pass Test',
    $validator->validate()
);

$validator = Validator::make(
    ['username' => 'JoeyTechnology'],
    ['username' => 'max:10']
);

test(
    'Max Length Fail Test',
    $validator->validate() === false
);

/*
|--------------------------------------------------------------------------
| Combined Min/Max
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['username' => 'Joey'],
    ['username' => 'required|min:3|max:10']
);

test(
    'Combined Min Max Test',
    $validator->validate()
);

/*
|--------------------------------------------------------------------------
| Min Error Message
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['password' => '123'],
    ['password' => 'min:8']
);

$validator->validate();

$errors = $validator->errorsFor('password');

test(
    'Min Error Message Test',
    isset($errors[0])
    && $errors[0] ===
        'The password field must be at least 8 characters.'
);

/*
|--------------------------------------------------------------------------
| Max Error Message
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    ['username' => 'VeryLongUsername'],
    ['username' => 'max:10']
);

$validator->validate();

$errors = $validator->errorsFor('username');

test(
    'Max Error Message Test',
    isset($errors[0])
    && $errors[0] ===
        'The username field may not be greater than 10 characters.'
);

/*                                                                         
| -------------------------------------------------------------------------- |
| Confirmed Rule Tests                                                       |
| -------------------------------------------------------------------------- |
*/                                                                         

$validator = Validator::make(
[
'password' => 'secret123',
'password_confirmation' => 'secret123',
],
[
'password' => 'required|min:8|confirmed',
]
);

test(
'Confirmed Match Test',
$validator->validate()
);

$validator = Validator::make(
[
'password' => 'secret123',
'password_confirmation' => 'different123',
],
[
'password' => 'required|min:8|confirmed',
]
);

test(
'Confirmed Mismatch Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'password' => 'secret123',
],
[
'password' => 'required|min:8|confirmed',
]
);

test(
'Missing Confirmation Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'password' => '',
'password_confirmation' => '',
],
[
'password' => 'confirmed',
]
);

test(
'Optional Empty Confirmation Test',
$validator->validate()
);

$validator = Validator::make(
[
'password' => 'secret123',
'password_confirmation' => 'different123',
],
[
'password' => 'required|confirmed',
]
);

$validator->validate();

$errors = $validator->errorsFor('password');

test(
'Confirmed Error Message Test',
isset($errors[0])
&& $errors[0] ===
'The password field confirmation does not match.'
);
