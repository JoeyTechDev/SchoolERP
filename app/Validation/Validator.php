<?php

declare(strict_types=1);

namespace SchoolERP\Validation;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Validator
 * --------------------------------------------------------------------------
 *
 * Lightweight validation service.
 *
 * Supported rules:
 *
 * required
 * nullable
 * email
 * min
 * max
 * numeric
 * integer
 * in
 * not_in
 * same
 * confirmed
 * unique
 * exists
 */
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
     * Database uniqueness checker.
     *
     * @var callable|null
     */
    private $uniqueChecker;

    /**
     * Database existence checker.
     *
     * @var callable|null
     */
    private $existsChecker;

    /**
     * Create a validator.
     *
     * @param array<string,mixed> $data
     * @param array<string,string|array<int,string>> $rules
     */
    public function __construct(
        array $data,
        array $rules,
        ?callable $uniqueChecker = null,
        ?callable $existsChecker = null
    ) {
        $this->data = $data;
        $this->rules = $rules;
        $this->uniqueChecker = $uniqueChecker;
        $this->existsChecker = $existsChecker;
    }

    /**
     * Create a validator instance.
     *
     * @param array<string,mixed> $data
     * @param array<string,string|array<int,string>> $rules
     */
    public static function make(
        array $data,
        array $rules,
        ?callable $uniqueChecker = null,
        ?callable $existsChecker = null
    ): self {
        return new self(
            $data,
            $rules,
            $uniqueChecker,
            $existsChecker
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
        | Nullable
        |--------------------------------------------------------------------------
        |
        | Nullable itself does not generate an error.
        |
        | It is handled here so an empty value can safely pass the remaining
        | validation rules.
        |--------------------------------------------------------------------------
        */

        if ($rule === 'nullable') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Optional Empty Values
        |--------------------------------------------------------------------------
        */

        if ($this->isEmpty($value)) {
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
        | Minimum
        |--------------------------------------------------------------------------
        |
        | For numeric values:
        |
        |     min:1
        |
        | means the value must be >= 1.
        |
        | For strings:
        |
        |     min:2
        |
        | means the string must contain at least 2 characters.
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($rule, 'min:')) {
            $minimum = (float) substr(
                $rule,
                4
            );

            if (is_numeric($value)) {
                if ((float) $value < $minimum) {
                    $this->addError(
                        $field,
                        'The ' . $field
                        . ' field must be at least '
                        . $this->formatNumber($minimum)
                        . '.'
                    );
                }

                return;
            }

            if (is_string($value)) {
                $length = mb_strlen($value);

                if ($length < $minimum) {
                    $this->addError(
                        $field,
                        'The ' . $field
                        . ' field must be at least '
                        . (int) $minimum
                        . ' characters.'
                    );
                }
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($rule, 'max:')) {
            $maximum = (float) substr(
                $rule,
                4
            );

            if (is_numeric($value)) {
                if ((float) $value > $maximum) {
                    $this->addError(
                        $field,
                        'The ' . $field
                        . ' field may not be greater than '
                        . $this->formatNumber($maximum)
                        . '.'
                    );
                }

                return;
            }

            if (is_string($value)) {
                $length = mb_strlen($value);

                if ($length > $maximum) {
                    $this->addError(
                        $field,
                        'The ' . $field
                        . ' field may not be greater than '
                        . (int) $maximum
                        . ' characters.'
                    );
                }
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
        | Integer
        |--------------------------------------------------------------------------
        */

        if ($rule === 'integer') {
            if (
                filter_var(
                    $value,
                    FILTER_VALIDATE_INT
                ) === false
            ) {
                $this->addError(
                    $field,
                    'The ' . $field
                    . ' field must be an integer.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | In
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($rule, 'in:')) {
            $allowed = explode(
                ',',
                substr($rule, 3)
            );

            $allowed = array_map(
                'trim',
                $allowed
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
        */

        if (str_starts_with($rule, 'not_in:')) {
            $excluded = explode(
                ',',
                substr($rule, 7)
            );

            $excluded = array_map(
                'trim',
                $excluded
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
        | Confirmed
        |--------------------------------------------------------------------------
        */

        if ($rule === 'confirmed') {
            $confirmationField =
                $field . '_confirmation';

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
        | Unique
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | unique:users,email
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

        /*
        |--------------------------------------------------------------------------
        | Exists
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | exists:classrooms,id
        |
        */

        if (str_starts_with($rule, 'exists:')) {
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

            if (
                $this->existsChecker === null
                || $table === ''
                || $column === ''
            ) {
                return;
            }

            $exists = ($this->existsChecker)(
                $table,
                $column,
                $value
            );

            if ($exists !== true) {
                $this->addError(
                    $field,
                    'The selected ' . $field
                    . ' does not exist.'
                );
            }

            return;
        }
    }

    /**
     * Determine whether a value is empty.
     */
    private function isEmpty(
        mixed $value
    ): bool {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        return false;
    }

    /**
     * Format a numeric value for an error message.
     */
    private function formatNumber(
        float $value
    ): string {
        if (floor($value) === $value) {
            return (string) (int) $value;
        }

        return (string) $value;
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