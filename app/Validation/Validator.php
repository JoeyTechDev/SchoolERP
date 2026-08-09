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
     * Create a validator.
     *
     * @param array<string,mixed> $data
     * @param array<string,string|array<int,string>> $rules
     */
    public function __construct(
        array $data,
        array $rules
    ) {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Create a validator instance.
     *
     * @param array<string,mixed> $data
     * @param array<string,string|array<int,string>> $rules
     */
    public static function make(
        array $data,
        array $rules
    ): self {
        return new self($data, $rules);
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
            $parameter = substr(
                $rule,
                4
            );

            $minimum = (int) $parameter;

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
            $parameter = substr(
                $rule,
                4
            );

            $maximum = (int) $parameter;

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
| The validator expects:
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

}

/*
*
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
