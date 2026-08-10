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
                ? (array) json_decode(
                    $value,
                    true
                )
                : (array) $value,

            'date' => $this->castDate($value),

            'datetime' => $this->castDateTime($value),

            default => $value,
        };
    }

    /**
     * Cast a value to DateTimeImmutable.
     */
    protected function castDate(
        mixed $value
    ): ?\DateTimeImmutable {

        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface(
                $value
            );
        }

        return new \DateTimeImmutable(
            (string) $value
        );
    }

    /**
     * Cast a value to DateTimeImmutable.
     */
    protected function castDateTime(
        mixed $value
    ): ?\DateTimeImmutable {

        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface(
                $value
            );
        }

        return new \DateTimeImmutable(
            (string) $value
        );
    }
}

