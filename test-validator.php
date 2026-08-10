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

/*                                                                         
| -------------------------------------------------------------------------- |
| Numeric Rule Tests                                                         |
| -------------------------------------------------------------------------- |
*/                                                                         

$validator = Validator::make(
[
'score' => '85',
],
[
'score' => 'numeric',
]
);

test(
'Numeric Pass Test',
$validator->validate()
);

$validator = Validator::make(
[
'score' => 'eighty-five',
],
[
'score' => 'numeric',
]
);

test(
'Numeric Fail Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'score' => '',
],
[
'score' => 'numeric',
]
);

test(
'Empty Numeric Test',
$validator->validate()
);

$validator = Validator::make(
[
'score' => '',
],
[
'score' => 'required|numeric',
]
);

test(
'Required Numeric Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'score' => 'abc',
],
[
'score' => 'numeric',
]
);

$validator->validate();

$errors = $validator->errorsFor('score');

test(
'Numeric Error Message Test',
isset($errors[0])
&& $errors[0] ===
'The score field must be a number.'
);

/*                                                                         |
| -------------------------------------------------------------------------- |
| In Rule Tests                                                              |
| -------------------------------------------------------------------------- |
*/                                                                         

$validator = Validator::make(
[
'status' => 'active',
],
[
'status' => 'in:active,inactive',
]
);

test(
'In Pass Test',
$validator->validate()
);

$validator = Validator::make(
[
'status' => 'inactive',
],
[
'status' => 'in:active,inactive',
]
);

test(
'In Second Allowed Value Test',
$validator->validate()
);

$validator = Validator::make(
[
'status' => 'pending',
],
[
'status' => 'in:active,inactive',
]
);

test(
'In Fail Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'status' => '',
],
[
'status' => 'in:active,inactive',
]
);

test(
'Empty In Test',
$validator->validate()
);

$validator = Validator::make(
[
'status' => 'pending',
],
[
'status' => 'in:active,inactive',
]
);

$validator->validate();

$errors = $validator->errorsFor('status');

test(
'In Error Message Test',
isset($errors[0])
&& $errors[0] ===
'The status field must be one of: active, inactive.'
);

/*                                                                         
| -------------------------------------------------------------------------- |
| Not In Rule Tests                                                          |
| -------------------------------------------------------------------------- |
*/                                                                         

$validator = Validator::make(
[
'status' => 'active',
],
[
'status' => 'not_in:banned,suspended',
]
);

test(
'Not In Pass Test',
$validator->validate()
);

$validator = Validator::make(
[
'status' => 'pending',
],
[
'status' => 'not_in:banned,suspended',
]
);

test(
'Not In Second Pass Test',
$validator->validate()
);

$validator = Validator::make(
[
'status' => 'banned',
],
[
'status' => 'not_in:banned,suspended',
]
);

test(
'Not In Fail Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'status' => 'suspended',
],
[
'status' => 'not_in:banned,suspended',
]
);

test(
'Not In Second Fail Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'status' => '',
],
[
'status' => 'not_in:banned,suspended',
]
);

test(
'Empty Not In Test',
$validator->validate()
);

$validator = Validator::make(
[
'status' => 'banned',
],
[
'status' => 'not_in:banned,suspended',
]
);

$validator->validate();

$errors = $validator->errorsFor('status');

test(
'Not In Error Message Test',
isset($errors[0])
&& $errors[0] ===
'The status field contains a prohibited value.'
);

/*                                                                         |
| -------------------------------------------------------------------------- |
| Same Rule Tests                                                            |
| -------------------------------------------------------------------------- |
*/                                                                         

$validator = Validator::make(
[
'password' => 'secret123',
'password_confirmation' => 'secret123',
],
[
'password_confirmation' => 'same:password',
]
);

test(
'Same Match Test',
$validator->validate()
);

$validator = Validator::make(
[
'password' => 'secret123',
'password_confirmation' => 'different123',
],
[
'password_confirmation' => 'same:password',
]
);

test(
'Same Mismatch Test',
$validator->validate() === false
);

$validator = Validator::make(
[
'password' => 'secret123',
],
[
'password_confirmation' => 'same:password',
]
);

test(
'Same Missing Field Test',
$validator->validate()
);

$validator = Validator::make(
[
'password' => '',
'password_confirmation' => '',
],
[
'password_confirmation' => 'same:password',
]
);

test(
'Same Empty Match Test',
$validator->validate()
);

$validator = Validator::make(
[
'password' => 'secret123',
'password_confirmation' => 'different123',
],
[
'password_confirmation' => 'same:password',
]
);

$validator->validate();

$errors = $validator->errorsFor(
'password_confirmation'
);

test(
'Same Error Message Test',
isset($errors[0])
&& $errors[0] ===
'The password_confirmation field must match the password field.'
);

/*
|--------------------------------------------------------------------------
| Unique
|--------------------------------------------------------------------------
|
| The unique rule checks whether a value already exists
| in the specified table and column.
|
| Example:
|
| 'email' => 'required|email|unique:users,email'
|
*/

/*
|--------------------------------------------------------------------------
| Unique Available
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    [
        'email' => 'new@example.com',
    ],
    [
        'email' => 'unique:users,email',
    ],
    function (
        string $table,
        string $column,
        mixed $value
    ): bool {
        return false;
    }
);

test(
    'Unique Available Test',
    $validator->validate()
);


/*
|--------------------------------------------------------------------------
| Unique Taken
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    [
        'email' => 'existing@example.com',
    ],
    [
        'email' => 'unique:users,email',
    ],
    function (
        string $table,
        string $column,
        mixed $value
    ): bool {
        return true;
    }
);

test(
    'Unique Taken Test',
    $validator->validate() === false
);


/*
|--------------------------------------------------------------------------
| Unique Empty Optional
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    [
        'email' => '',
    ],
    [
        'email' => 'unique:users,email',
    ],
    function (
        string $table,
        string $column,
        mixed $value
    ): bool {
        return true;
    }
);

test(
    'Unique Empty Test',
    $validator->validate()
);


/*
|--------------------------------------------------------------------------
| Unique Error Message
|--------------------------------------------------------------------------
*/

$validator = Validator::make(
    [
        'email' => 'existing@example.com',
    ],
    [
        'email' => 'unique:users,email',
    ],
    function (
        string $table,
        string $column,
        mixed $value
    ): bool {
        return true;
    }
);

$validator->validate();

$errors = $validator->errorsFor('email');

test(
    'Unique Error Message Test',
    isset($errors[0])
    && $errors[0] ===
        'The email has already been taken.'
);


/*
|--------------------------------------------------------------------------
| Unique Callback Parameters
|--------------------------------------------------------------------------
*/

$callbackCalled = false;

$validator = Validator::make(
    [
        'email' => 'john@example.com',
    ],
    [
        'email' => 'unique:users,email',
    ],
    function (
        string $table,
        string $column,
        mixed $value
    ) use (&$callbackCalled): bool {

        $callbackCalled =
            $table === 'users'
            && $column === 'email'
            && $value === 'john@example.com';

        return false;
    }
);

$validator->validate();

test(
    'Unique Callback Parameters Test',
    $callbackCalled
);

