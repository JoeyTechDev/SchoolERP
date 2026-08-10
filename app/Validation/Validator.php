<?php

declare(strict_types=1);

namespace SchoolERP\Validation;

final class Validator
{
    /**
     * Input data.
     *
     * @var array<string,mixed>
     */
    private array $data;

    /**
     * Validation rules.
     *
     * @var array<string,string|array<int,string>>
     */
    private array $rules;

    /**
     * Validation errors.
     *
     * @var array<string,array<int,string>>
     */
    private array $errors = [];

    /**
     * Optional database existence checker.
     *
     * The callback must receive:
     *
     * - table name
     * - column name
     * - value
     *
     * and return true when a matching record exists.
     *
     * @var callable|null
     */
    private $uniqueChecker;

    /**
     * Create a validator.
     *
     * @param array<string,mixed> $data
     * @param array<string,string|array<int,string>> $rules
     * @param callable|null $uniqueChecker
     */
    public function __construct(
        array $data,
        array $rules,
        ?callable $uniqueChecker = null
    ) {
        $this->data = $data;
        $this->rules = $rules;
        $this->uniqueChecker = $uniqueChecker;
    }

    /**
     * Create a validator instance.
     *
     * Existing two-argument usage remains fully supported.
     *
     * @param array<string,mixed> $data
     * @param array<string,string|array<int,string>> $rules
     * @param callable|null $uniqueChecker
     */
    public static function make(
        array $data,
        array $rules,
        ?callable $uniqueChecker = null
    ): self {
        return new self(
            $data,
            $rules,
            $uniqueChecker
        );
    }

    /**
     * Run validation.
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $ruleList = is_array($rules)
                ? $rules
                : explode('|', $rules);

            foreach ($ruleList as $rule) {
                $rule = trim($rule);

                if ($rule === '') {
                    continue;
                }

                $this->validateRule(
                    $field,
                    $rule
                );
            }
        }

        return $this->passes();
    }

    /**
     * Validate a single rule.
     */
    private function validateRule(
        string $field,
        string $rule
    ): void {
        $value = $this->data[$field] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Required
        |--------------------------------------------------------------------------
        */

        if ($rule === 'required') {
            $empty = $value === null;

            if (is_string($value)) {
                $empty = trim($value) === '';
            }

            if ($empty) {
                $this->addError(
                    $field,
                    'The ' . $field . ' field is required.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Optional Empty Values
        |--------------------------------------------------------------------------
        |
        | Rules other than "required" should not reject an empty optional field.
        |
        */

        if (
            $value === null
            || (
                is_string($value)
                && trim($value) === ''
            )
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        if ($rule === 'email') {
            if (
                !is_string($value)
                || filter_var(
                    $value,
                    FILTER_VALIDATE_EMAIL
                ) === false
            ) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field must be a valid email address.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Length
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($rule, 'min:')) {
            $minimum = (int) substr(
                $rule,
                4
            );

            if (
                is_string($value)
                && mb_strlen($value) < $minimum
            ) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field must be at least '
                    . $minimum
                    . ' characters.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Length
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($rule, 'max:')) {
            $maximum = (int) substr(
                $rule,
                4
            );

            if (
                is_string($value)
                && mb_strlen($value) > $maximum
            ) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field may not be greater than '
                    . $maximum
                    . ' characters.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Confirmed
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 'password' => 'required|min:8|confirmed'
        |
        | Expects:
        |
        | password
        | password_confirmation
        |
        */

        if ($rule === 'confirmed') {
            $confirmationField = $field . '_confirmation';

            $confirmation = $this->data[
                $confirmationField
            ] ?? null;

            if ($value !== $confirmation) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field confirmation does not match.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Numeric
        |--------------------------------------------------------------------------
        */

        if ($rule === 'numeric') {
            if (!is_numeric($value)) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field must be a number.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | In
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 'status' => 'required|in:active,inactive'
        |
        */

        if (str_starts_with($rule, 'in:')) {
            $allowed = explode(
                ',',
                substr($rule, 3)
            );

            if (!in_array(
                (string) $value,
                $allowed,
                true
            )) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field must be one of: '
                    . implode(', ', $allowed)
                    . '.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Not In
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 'status' => 'not_in:banned,suspended'
        |
        */

        if (str_starts_with($rule, 'not_in:')) {
            $excluded = explode(
                ',',
                substr($rule, 7)
            );

            if (in_array(
                (string) $value,
                $excluded,
                true
            )) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field contains a prohibited value.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Same
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 'password_confirmation' => 'same:password'
        |
        */

        if (str_starts_with($rule, 'same:')) {
            $otherField = substr(
                $rule,
                5
            );

            if (!array_key_exists(
                $otherField,
                $this->data
            )) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field must match the '
                    . $otherField
                    . ' field.'
                );

                return;
            }

            $otherValue = $this->data[
                $otherField
            ];

            if ($value !== $otherValue) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field must match the '
                    . $otherField
                    . ' field.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Unique
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 'email' => 'required|email|unique:users,email'
        |
        | Format:
        |
        | unique:table,column
        |
        | The database lookup itself is supplied through the optional
        | $uniqueChecker callback.
        |
        */

        if (str_starts_with($rule, 'unique:')) {
            $parameters = explode(
                ',',
                substr($rule, 7)
            );

            $table = trim(
                $parameters[0] ?? ''
            );

            $column = trim(
                $parameters[1] ?? $field
            );

            /*
             * If no database checker has been supplied, do not attempt
             * a database operation inside the Validator.
             */
            if (
                $this->uniqueChecker === null
                || $table === ''
                || $column === ''
            ) {
                return;
            }

            $exists = ($this->uniqueChecker)(
                $table,
                $column,
                $value
            );

            if ($exists === true) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' has already been taken.'
                );
            }

            return;
        }
    }

    /**
     * Add a validation error.
     */
    private function addError(
        string $field,
        string $message
    ): void {
        $this->errors[$field][] = $message;
    }

    /**
     * Determine whether validation passed.
     */
    public function passes(): bool
    {
        return $this->errors === [];
    }

    /**
     * Determine whether validation failed.
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all validation errors.
     *
     * @return array<string,array<int,string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get errors for a specific field.
     *
     * @return array<int,string>
     */
    public function errorsFor(
        string $field
    ): array {
        return $this->errors[$field] ?? [];
    }

    /**
     * Determine whether a field has an error.
     */
    public function hasError(
        string $field
    ): bool {
        return isset($this->errors[$field])
            && $this->errors[$field] !== [];
    }

    /**
     * Get an input value.
     */
    public function value(
        string $field,
        mixed $default = null
    ): mixed {
        return $this->data[$field] ?? $default;
    }

    /**
     * Get all input data.
     *
     * @return array<string,mixed>
     */
    public function data(): array
    {
        return $this->data;
    }
}

