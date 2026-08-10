<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

use SchoolERP\Query\QueryBuilder;

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
        $this->initializeQuery();

        return $this->query
            ->select(['*'])
            ->get();
    }

    /**
     * Find a record by ID.
     *
     * @return static|null
     */
    public function find(int $id): ?static
    {
        $record = $this->query
            ->table($this->table)
            ->where('id', '=', $id)
            ->first();

        if ($record === null) {
            return null;
        }

        return (new static())->fill($record);
    }

    /**
     * Create a new record.
     *
     * @param array<string,mixed> $attributes
     */
    public function create(array $attributes): int
    {
        $attributes = $this->fillable($attributes);

        $attributes = $this->addCreationTimestamps(
            $attributes
        );

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
        $this->initializeQuery();

        $attributes = $this->filterFillable($attributes);

        return $this->query->update($attributes);
    }

    /**
     * Save the current model.
     */
    public function save(): bool
    {
        if (!$this->isDirty()) {
            return true;
        }

        $this->touch();

        $dirty = $this->getDirty();

        $dirty = $this->filterFillable($dirty);

        if ($dirty === []) {
            return true;
        }

        if (!isset($this->attributes['id'])) {
            throw new \RuntimeException(
                'Cannot save a model without an ID.'
            );
        }

        $id = $this->attributes['id'];

        $this->query
            ->table($this->table)
            ->where('id', '=', $id)
            ->update($dirty);

        $this->fill($this->attributes);

        return true;
    }

    /**
     * Delete records.
     */
    public function delete(): bool
    {
        if (!isset($this->attributes['id'])) {
            throw new \RuntimeException(
                'Cannot delete a model without an ID.'
            );
        }

        $affected = $this->query
            ->table($this->table)
            ->where('id', '=', $this->attributes['id'])
            ->delete();

        return $affected > 0;
    }

    /**
     * Determine whether records exist.
     */
    public function exists(): bool
    {
        $this->initializeQuery();

        return $this->first() !== null;
    }

    /**
     * Get the first record or throw.
     *
     * @return static
     */
    public function firstOrFail(): static
    {
        $record = $this->first();

        if ($record === null) {
            throw new \RuntimeException(
                'Record not found.'
            );
        }

        return $record;
    }

    /**
     * Count records.
     */
    public function count(): int
    {
        $this->initializeQuery();

        return $this->query->count();
    }

    /**
     * Begin a new model query.
     */
    public function query(): QueryBuilder
    {
        $this->initializeQuery();

        $this->applyGlobalScopes();

        return $this->query;
    }

    /**
     * Add a WHERE clause.
     */
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): static {
        /*
         * IMPORTANT:
         *
         * Initialize the model table before adding
         * the WHERE clause.
         *
         * Without this, a query such as:
         *
         * (new Student())
         *     ->where('id', '=', 1)
         *     ->first();
         *
         * would generate:
         *
         * SELECT * FROM WHERE id = ? LIMIT 1
         *
         * instead of:
         *
         * SELECT * FROM students WHERE id = ? LIMIT 1
         */
        $this->initializeQuery();

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
        $this->initializeQuery();

        return $this->query->get();
    }

    /**
     * Get the first matching record.
     *
     * @return static|null
     */
    public function first(): ?static
    {
        $this->initializeQuery();

        $record = $this->query->first();

        if ($record === null) {
            return null;
        }

        $model = (new static())->fill($record);

        foreach (
            $this->query->getEagerLoads()
            as $relation
        ) {
            if (!method_exists($model, $relation)) {
                continue;
            }

            $model->setRelation(
                $relation,
                $model->{$relation}()->get()
            );
        }

        return $model;
    }

    /**
     * Forward unknown methods to the Query Builder.
     */
    public function __call(
        string $method,
        array $arguments
    ): mixed {
        $this->initializeQuery();

        /*
         * Local Scope.
         */
        $scope = 'scope' . ucfirst($method);

        if (method_exists($this, $scope)) {
            return $this->$scope(...$arguments);
        }

        /*
         * Forward to Query Builder.
         */
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
         * Continue the model chain when QueryBuilder
         * returns itself.
         */
        if (
            $result instanceof \SchoolERP\Query\QueryBuilder
        ) {
            return $this;
        }

        return $result;
    }
}

