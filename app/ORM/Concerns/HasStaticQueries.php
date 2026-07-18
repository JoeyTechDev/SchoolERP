<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * Static ORM query methods.
 */
trait HasStaticQueries
{
    /**
     * Begin a query.
     */
    public static function query(): static
    {
        return new static();
    }

    /**
     * Get all records.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function all(): array
    {
        return (new static())->all();
    }

    /**
     * Find a record.
     *
     * @return array<string,mixed>|null
     */
    public static function find(int $id): ?array
    {
        return (new static())->find($id);
    }

    /**
     * Begin a WHERE query.
     */
    public static function where(
        string $column,
        string $operator,
        mixed $value
    ): static {

        return (new static())
            ->query()
            ->where(
                $column,
                $operator,
                $value
            );
    }
}