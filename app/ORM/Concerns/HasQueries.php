<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * Query methods for ORM models.
 */
trait HasQueries
{
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
     * Begin a query.
     */
    public function query(): static
    {
        $this->query->table($this->table);

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
}