<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * Handles model attribute casting.
 */
trait HasCasts
{
    /**
     * Attribute cast definitions.
     *
     * Example:
     *
     * protected array $casts = [
     *     'id' => 'int',
     *     'is_active' => 'bool',
     *     'created_at' => 'datetime',
     * ];
     *
     * @var array<string,string>
     */
    protected array $casts = [];

    /**
     * Cast a value.
     */
    protected function castAttribute(
        string $key,
        mixed $value
    ): mixed {

        if (!isset($this->casts[$key])) {
            return $value;
        }

        return match ($this->casts[$key]) {

            'int',
            'integer' => (int) $value,

            'float',
            'double' => (float) $value,

            'bool',
            'boolean' => (bool) $value,

            'string' => (string) $value,

            'array' => is_string($value)
            ? json_decode($value, true)
            : (array) $value,

            'date' => $value === null
            ? null
            : new \DateTimeImmutable($value),

            'datetime' => $value === null
            ? null
            : new \DateTimeImmutable($value),

            default => $value,
        };
    }
}