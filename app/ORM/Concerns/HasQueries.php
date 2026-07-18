<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

    /**
     * Query methods for ORM models.
     */
    trait HasQueries
    {

    /**
     * Ensure the Query Builder is initialized.
     */
    private function initializeQuery(): void
    {
        $this->query->table($this->table);
    }

    /**
     * Get all records.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->query
            ->table($this->table)
            ->select(['*'])
            ->get();
    }

    /**
     * Find a record by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?array
    {
        return $this->query
            ->table($this->table)
            ->where('id', '=', $id)
            ->first();
    }

/**
 * Create a new record.
 *
 * @param array<string,mixed> $attributes
 */
public function create(array $attributes): int
{
    return $this->query
        ->table($this->table)
        ->insert($attributes);
}

/**
 * Update records.
 *
 * @param array<string,mixed> $attributes
 */
public function update(array $attributes): int
{
    return $this->query->update($attributes);
}

/**
 * Delete records.
 */
public function delete(): int
{
    return $this->query->delete();
}

/**
 * Determine whether records exist.
 */
public function exists(): bool
{
    return $this->first() !== null;
}

/**
 * Get the first record or throw.
 *
 * @return array<string,mixed>
 */
public function firstOrFail(): array
{
    $record = $this->first();

    if ($record === null) {
        throw new \RuntimeException(
            'Record not found.'
        );
    }

    return $record;
}

public function count(): int
{
    return $this->query->count();
}

    /**
     * Begin a query.
     */
    public function query(): static
    {
        $this->initializeQuery();

        return $this;
    }

    /**
     * Add a WHERE clause.
     */
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): static {

        $this->query->where(
            $column,
            $operator,
            $value
        );

        return $this;
    }

    /**
     * Execute the query.
     *
     * @return array<int,array<string,mixed>>
     */
    public function get(): array
    {
        return $this->query->get();
    }

    /**
     * Get the first matching record.
     *
     * @return array<string,mixed>|null
     */
    public function first(): ?array
    {
        return $this->query->first();
   
    }

/**
 * Forward unknown methods to the Query Builder.
 */
public function __call(
    string $method,
    array $arguments
): mixed {

    $this->initializeQuery();

    if (!method_exists($this->query, $method)) {
        throw new \BadMethodCallException(
            sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            )
        );
    }

    $result = $this->query->$method(...$arguments);

    /*
     * If QueryBuilder returned itself,
     * continue chaining on the model.
     */
    if ($result instanceof \SchoolERP\Query\QueryBuilder) {
        return $this;
    }

    return $result;
}

}